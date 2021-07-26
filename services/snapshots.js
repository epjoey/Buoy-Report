const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');

const SNAPSHOT_LIMIT = 10;
const SNAPSHOT_SELECT = '\
  SELECT s.id, s.text, \
    s.quality, s.imagepath, \
    s.obsdate, s.waveheight, \
    r.id as reporterId, r.name as reporterName, r.email, \
    l.id as locationId, l.name as locationName, l.timezone \
  FROM `report` s \
  LEFT JOIN `reporter` r ON s.reporterid = r.id \
  LEFT JOIN `location` l ON s.locationid = l.id';


async function create(locationId, reqBody, user){
  let params = _.pick(reqBody, ['waveheight', 'quality', 'imagepath', 'obsdate', 'text']);
  params.locationid = locationId;
  params.email = user._json.email;
  params.reportdate = Date.now();
  try {
    let [result,] = await db.query('INSERT INTO `report` SET ?', params);
    let [rows,] = await db.query(
      SNAPSHOT_SELECT + ' WHERE s.id = ?',
      result.insertId
    );
    return [null, helper.first(rows)];
  }
  catch(err){
    return [err.message, null];
  }
}


async function forLocation(locationId, user, page = 1){
  const offset = helper.getOffset(page, SNAPSHOT_LIMIT);
  let [rows, fields] = await db.query(
    SNAPSHOT_SELECT + ' WHERE s.locationid = ? AND (s.public = 1 OR r.email = ?) ORDER BY s.id desc LIMIT ?,?',
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


async function forReporter(reporter, user, page = 1){
  const offset = helper.getOffset(page, SNAPSHOT_LIMIT);
  let [rows, fields] = await db.query(
    SNAPSHOT_SELECT + ' WHERE (r.email = ? OR s.email = ?) AND (s.public = 1 OR r.email = ?) ORDER BY s.id desc LIMIT ?,?', 
    [reporter.email, reporter.email, user ? user._json.email : null, offset, SNAPSHOT_LIMIT]
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
  create,
  forLocation,
  forReporter
}