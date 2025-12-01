const _ = require('lodash');
const db = require('../services/db');
const helper = require('../services/helper');
const buoyService = require('../services/buoys')

const SNAPSHOT_LIMIT = 10;
const SNAPSHOT_SELECT = '\
  SELECT s.id, s.text, \
    s.quality, s.imagepath, \
    s.obsdate, s.waveheight, \
    s.email, \
    l.id as locationId, l.name as locationName, l.timezone \
  FROM `report` s \
  LEFT JOIN `location` l ON s.locationid = l.id';


async function create(locationId, reqBody, user){
  let params = _.pick(reqBody, ['waveheight', 'quality', 'imagepath', 'text']);
  params.waveheight = params.waveheight || null;
  params.locationid = locationId;
  params.email = user._json.email;
  params.reportdate = Date.now() / 1000;
  let observationDate = Date.now() / 1000;
  let secondOffset = (parseInt(reqBody.hourOffset) * 60 * 60);
  observationDate -= secondOffset;
  params.obsdate = observationDate;
  try {
    let [result,] = await db.query('INSERT INTO `report` SET ?', params);
    const buoys = await buoyService.forLocation(locationId);
    for(const buoy of buoys){
      await snapshotBuoyData(buoy, result.insertId, observationDate);
    }
    return [null, await getSingle(result.insertId)];
  }
  catch(err){
    console.log('error creating snapshot for location', locationId, err);
    return [err.message, null];
  }
}

const CLOSEST_ROW_CUTOFF_SECONDS = 60 * 60 * 8; // Any reading after 8 hours disregard.
function closestRow(data, observationDate){
  // Returns the first row that is after observation date.
  // This is what a row looks like:
  // [
  //   '2021',   '07',   '30',
  //   '03',     '30',   '320',
  //   '2.0',    '3.0',  'MM',
  //   'MM',     'MM',   'MM',
  //   '1013.9', '13.2', 'MM',
  //   '12.8',   'MM',   'MM',
  //   'MM'
  // ]
  for(let row of data){
    row.date = Date.UTC(row[0], parseInt(row[1]) - 1, row[2], row[3], row[4]) / 1000;
    // Find the first buoy reading before the observation.
    if(row.date < observationDate){
      // If the latest buoy reading was more than 8 hours before the observation, bail.
      if(observationDate - row.date > CLOSEST_ROW_CUTOFF_SECONDS){
        return null;
      }
      return row;
    }
  }
  return null;
}

function parseRow(rawRow){
  // Spectral Wave Data from NOAA looks like:
  // #YY  MM DD hh mm WVHT  SwH  SwP  WWH  WWP SwD WWD  STEEPNESS  APD MWD
  // #yr  mo dy hr mn    m    m  sec    m  sec  -  degT     -      sec degT
  // 2021 07 14 21 40  2.2  2.1  9.1  0.4  3.3  NW   W    AVERAGE  7.4 316
  // 2021 07 14 20 40  2.1  2.1 10.0  0.4  3.3  NW WNW    AVERAGE  7.3 315
  // 2021 07 14 19 40  2.0  2.0 10.0  0.4  4.0  NW   W    AVERAGE  7.4 312
  let row = {};
  row.waveheight = rawRow[5];
  row.swellheight = rawRow[6];
  row.swellperiod = rawRow[7];
  row.swelldir = rawRow[10];
  row.meanwavedir = rawRow[14];
  row.windwaveheight = rawRow[8];
  row.windwaveperiod = rawRow[9];
  row.windwavedir = rawRow[11];
  // We already have `date` from `closestRow`.
  row.gmttime = rawRow.date;
  return row;
}


async function snapshotBuoyData(buoy, snapshotId, observationDate){
  let [err, data] = await buoyService.getData(buoy.buoyid, 'wave', 2, 240);
  if(err){
    console.log('error fetching buoy data for', buoy.buoyid, err);
    return;
  }
  let row = closestRow(data, observationDate);
  if(!row){
    console.log('historical buoy data not found for buoy', buoy.buoyid, 'at date', observationDate);
    return;
  }
  let params = _.extend({
    buoy: buoy.buoyid,
    reportid: snapshotId
  }, parseRow(row));
  await db.query('INSERT INTO `buoydata` SET ?', params);
}


async function getSingle(snapshotId){
  let snapshot = await helper.first(SNAPSHOT_SELECT + ' WHERE s.id = ?', snapshotId);
  let groupedBuoyData = await buoyDataForSnapshots([snapshotId]);
  snapshot.buoyData = groupedBuoyData[snapshot.id];
  return snapshot;
};


async function forLocation(locationId, user, page = 1){
  const offset = helper.getOffset(page, SNAPSHOT_LIMIT);
  let rows = await helper.rows(
    SNAPSHOT_SELECT + ' WHERE s.locationid = ? AND (s.public = 1 OR s.email = ?) ORDER BY s.id desc LIMIT ?,?',
    [locationId, user ? user._json.email : null, offset, SNAPSHOT_LIMIT]
  );

  const buoyData = await buoyDataForSnapshots(_.map(rows, 'id'));
  _.forEach(rows, function(row){
    row.buoyData = buoyData[row.id] || [];
  });

  return {
    rows: rows || [],
    meta: {page}
  };
}


async function forUser(user, page = 1){
  const offset = helper.getOffset(page, 100);
  const sqlEnd = ' ORDER BY s.id desc LIMIT ?,?';
  let rows;
  if(user.isAdmin){
    rows = await helper.rows(SNAPSHOT_SELECT + sqlEnd, [offset, 100]);
  }
  else {
    rows = await helper.rows(SNAPSHOT_SELECT + ' WHERE s.email = ? ' + sqlEnd, [user._json.email, offset, 100]);
  }
  const buoyData = await buoyDataForSnapshots(_.map(rows, 'id'));
  _.forEach(rows, function(row){
    row.buoyData = buoyData[row.id];
  });

  return {
    rows: rows || [],
    meta: {page}
  }
}


async function buoyDataForSnapshots(snapshotIds){
  if(!snapshotIds.length){
    return {};
  }
  let rows = await helper.rows(
    'SELECT * from `buoydata` WHERE reportid IN (?)',
    [snapshotIds]
  );
  let buoyIds = rows.map(row => parseInt(row.buoy));
  let buoys = await buoyService.getMultiple(buoyIds);
  let buoysById = _.keyBy(buoys, 'buoyid')
  rows.forEach(row => {
    row.buoy = buoysById[row.buoy];
  });
  let grouped = _.groupBy(rows, 'reportid');
  return grouped;
}


module.exports = {
  create,
  getSingle,
  forLocation,
  forUser
}