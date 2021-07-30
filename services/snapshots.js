const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');
const buoyService = require('./buoys')

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
    return [err.message, null];
  }
}


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
    if(row.date < observationDate){
      return row;
    }
  }
  return null;
}


async function snapshotBuoyData(buoy, snapshotId, observationDate){
  let [err, data] = await buoyService.getData(buoy.buoyid, 'standard', 2, 240);
  if(err){
    console.log('error fetching buoy data for', buoy.buoyid, err);
    return;
  }
  let row = closestRow(data, observationDate);
  if(!row){
    console.log('historical buoy data not found for buoy', buoy.buoyid, 'at date', observationDate);
    return;
  }
  let params = {};
  params.buoy = buoy.buoyid;
  params.reportid = snapshotId;
  params.winddir = row.winddir;
  params.windspeed = row.windspeed;
  params.swellheight = row.swellheight;
  params.swellperiod = row.swellperiod;
  params.swelldir = row.swelldir;
  params.gmttime = row.date;
  await db.query('INSERT INTO `buoydata` SET ?', params);
}


async function getSingle(snapshotId){
  let [rows,] = await db.query(SNAPSHOT_SELECT + ' WHERE s.id = ?', snapshotId);
  let row = helper.first(rows);
  let groupedBuoyData = await buoyDataByReport([snapshotId]);
  row.buoyData = groupedBuoyData[row.id];
  return row;
};


async function forLocation(locationId, user, page = 1){
  const offset = helper.getOffset(page, SNAPSHOT_LIMIT);
  let [rows, fields] = await db.query(
    SNAPSHOT_SELECT + ' WHERE s.locationid = ? AND (s.public = 1 OR s.email = ?) ORDER BY s.id desc LIMIT ?,?',
    [locationId, user ? user._json.email : null, offset, SNAPSHOT_LIMIT]
  );

  const buoyData = await buoyDataByReport(_.map(rows, 'id'));
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
    [rows,] = await db.query(SNAPSHOT_SELECT + sqlEnd, [offset, 100]);
  }
  else {
    [rows,] = await db.query(SNAPSHOT_SELECT + ' WHERE s.email = ? ' + sqlEnd, [user._json.email, offset, 100]);
  }
  const buoyData = await buoyDataByReport(_.map(rows, 'id'));
  _.forEach(rows, function(row){
    row.buoyData = buoyData[row.id];
  });

  return {
    rows: rows || [],
    meta: {page}
  }
}


async function insertBuoyData(reportId){
  let [result,] = await db.query(
    'INSERT INTO `buoydata` SET ?',
    params
  );
  return _.groupBy(rows, 'reportid');
}


async function buoyDataByReport(reportIds){
  if(!reportIds.length){
    return {};
  }
  let [rows, fields] = await db.query(
    'SELECT * from `buoydata` WHERE reportid IN (?)',
    reportIds
  );
  return _.groupBy(rows, 'reportid');
}


module.exports = {
  create,
  forLocation,
  forUser
}