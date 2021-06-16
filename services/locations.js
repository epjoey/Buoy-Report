const db = require('../db');
const helper = require('../helper');

async function getMultiple(page = 1){
  const LIMIT = 1000;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT id, locname FROM `location` LIMIT ?,?', 
    [offset, LIMIT]
  );
  rows = helper.emptyOrRows(rows);
  const meta = {page};

  return {
    rows,
    meta
  }
}

async function getSingle(id){
  let [rows, fields] = await db.query(
    'SELECT * FROM `location` WHERE id = ?', 
    [id]
  );
  let row = rows[0];
  return row;
}


async function getBuoys(location, page = 1){
  const LIMIT = 1000;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT * FROM `buoy` b JOIN `buoy_location` bl ON b.buoyid = bl.buoyid WHERE bl.locationid = ? LIMIT ?,?', 
    [location.id, offset, LIMIT]
  );
  rows = helper.emptyOrRows(rows);
  const meta = {page};

  return {
    rows,
    meta
  }
}

module.exports = {
  getSingle,
  getMultiple,
  getBuoys
}