const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');


async function create(reqBody, user){
  let params = _.pick(reqBody, ['name', 'timezone', 'stormsurfingurl']);
  if(reqBody.latitude){
    params.latitude = parseFloat(reqBody.latitude).toFixed(3);
  }
  if(reqBody.longitude){
    params.longitude = parseFloat(reqBody.longitude).toFixed(3);
  }
  params.email = user._json.email;
  try {
    const [result, fields] = await db.query('INSERT INTO `location` SET ?', params);
    await addBuoysToLocation(reqBody.buoys, result.insertId);
    return [null, result.insertId];
  }
  catch(err){
    return [err.message, null];
  }
}


async function update(locationId, reqBody, user){
  let params = _.pick(reqBody, ['name', 'timezone', 'stormsurfingurl']);
  if(reqBody.latitude){
    params.latitude = parseFloat(reqBody.latitude).toFixed(3);
  }
  if(reqBody.longitude){
    params.longitude = parseFloat(reqBody.longitude).toFixed(3);
  }
  try {
    const [result, fields] = await db.query(
      'UPDATE `location` SET ? WHERE id = ?', [params, locationId]
    );
    // Update buoys
    await db.query('DELETE FROM `buoy_location` WHERE locationid = ?', locationId);
    await addBuoysToLocation(reqBody.buoys, locationId);
    return [null, getSingle(locationId, user)];
  }
  catch(err){
    return [err.message, null];
  }
}


async function del(location){
  try {
    const [result, fields] = await db.query(
      'DELETE FROM `location` WHERE id = ?', location.id
    );
    return [null, true];
  }
  catch(err){
    return [err.message, null];
  }
}


async function getMultiple(page = 1, user){
  const LIMIT = 1000;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT id, name, timezone, f.email AS favorite \
    FROM `location` \
    LEFT JOIN `favorites` f ON f.locationid = id AND f.email = ? \
    ORDER BY name LIMIT ?,?',
    [user._json.email, offset, LIMIT]
  );
  rows = helper.rows(rows);
  rows.forEach(function(row){
    row.$isFavorite = !!row.favorite;
    delete row.favorite;
  });
  const meta = {page};
  return {
    rows,
    meta
  }
}


async function getSingle(id, user){
  let [rows,] = await db.query(
    'SELECT id, name, timezone, latitude, longitude, email \
     FROM `location` WHERE id = ?',
    [id]
  );
  let row = helper.first(rows);
  let [favoriteRows,] = await db.query(
    'SELECT email FROM `favorites` WHERE locationid = ? AND email = ?',
    [id, user._json.email]
  );
  let favoriteRow = helper.first(favoriteRows);
  row.$isFavorite = !!favoriteRow;
  return row;
}


async function addBuoysToLocation(buoyIds, locationId){
  // `buoyIds` is a list of buoy ids seperated by comma or space.
  if(!buoyIds){
    return;
  }
  buoyIds = _.map(_.split(buoyIds, /[ ,]+/), _.trim);
  _.forEach(buoyIds, async function(buoyId){
    let params = {buoyid: buoyId, locationid: locationId, created: Date.now()};
    try {
      await db.query(
        'INSERT INTO `buoy_location` SET ?', params
      );
    } catch(err) {
      console.log('error inserting buoy location:', err);
      return; // Duplicate entry, pass.
    }
  });
}


module.exports = {
  create,
  update,
  del,

  getSingle,
  getMultiple,
}