const db = require('../db');
const helper = require('../helper');


async function forLocation(locationId, page = 1){
  const LIMIT = 10;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT r.id, r.text, r.quality, r.imagepath, \
    r.obsdate, r.waveheight, \
    u.id as reporterId, u.name as reporterName, u.email, \
    l.id as locationId, l.name as locationName, l.timezone \
    FROM `report` r \
    LEFT JOIN `reporter` u ON r.reporterid = u.id \
    LEFT JOIN `location` l ON r.locationid = l.id \
    WHERE r.locationid = ? AND r.public = 1 \
    ORDER BY r.obsdate desc \
    LIMIT ?,?', 
    [locationId, offset, LIMIT]
  );
  rows = helper.rows(rows);
  const meta = {page};

  return {
    rows,
    meta
  }
}


async function forReporter(reporterEmail, page = 1){
  const LIMIT = 10;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT r.id, r.text, r.quality, r.imagepath, \
    r.obsdate, r.waveheight, \
    u.id as reporterId, u.name as reporterName, u.email, \
    l.id as locationId, l.name as locationName, l.timezone \
    FROM `report` r \
    LEFT JOIN `reporter` u ON r.reporterid = u.id \
    LEFT JOIN `location` l ON r.locationid = l.id \
    WHERE u.email = ? \
    ORDER BY r.obsdate desc \
    LIMIT ?,?', 
    [reporterEmail, offset, LIMIT]
  );
  rows = helper.rows(rows);
  const meta = {page};

  return {
    rows,
    meta
  }
}


module.exports = {
  forLocation,
  forReporter
}