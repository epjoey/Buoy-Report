const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');

const SNAPSHOT_LIMIT = 10;
const SNAPSHOT_SELECT = '\
  SELECT r.id, r.text, \
    r.quality, r.imagepath, \
    r.obsdate, r.waveheight, \
    u.id as reporterId, u.name as reporterName, u.email, \
    l.id as locationId, l.name as locationName, l.timezone \
  FROM `report` r \
  LEFT JOIN `reporter` u ON r.reporterid = u.id \
  LEFT JOIN `location` l ON r.locationid = l.id';


async function create(reqBody, user){
  let params = _.pick(reqBody);
  params.email = user._json.email;
  const [result, fields] = await db.query('INSERT INTO `report` SET ?', params);
  if(result.insertId){
    let [rows, fields] = await db.query(
      'SELECT * FROM `report` WHERE id = ?',
      result.insertId
    );
    return helper.first(rows);
  }
  return null;
}


async function forLocation(locationId, page = 1){
  const offset = helper.getOffset(page, SNAPSHOT_LIMIT);
  let [rows, fields] = await db.query(
    SNAPSHOT_SELECT + ' WHERE r.locationid = ? AND r.public = 1 ORDER BY r.id desc LIMIT ?,?',
    [locationId, offset, SNAPSHOT_LIMIT]
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


async function forReporter(reporterEmail, page = 1){
  const offset = helper.getOffset(page, SNAPSHOT_LIMIT);
  let [rows, fields] = await db.query(
    SNAPSHOT_SELECT + ' WHERE u.email = ? ORDER BY r.id desc LIMIT ?,?', 
    [reporterEmail, offset, SNAPSHOT_LIMIT]
  );

  const buoyData = await buoyDataByReport(_.map(rows, 'id'));
  _.forEach(rows, function(row){
    row.buoyData = buoyData[row.id];
  });

  return {
    rows: rows || [],
    meta: {page}
  }
}


async function buoyDataByReport(reportIds){
  if(!reportIds.length){
    return {};
  }
  let [rows, fields] = await db.query(
    'SELECT bd.*, b.name from `buoydata` bd LEFT JOIN `buoy` b ON bd.buoy = b.buoyid WHERE reportid IN (?)',
    reportIds
  );
  return _.groupBy(rows, 'reportid');
}


module.exports = {
  forLocation,
  forReporter
}