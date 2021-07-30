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


async function update(locationId, reqBody){
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
    return [null, getSingle(locationId)];
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


async function getMultiple(page = 1){
  const LIMIT = 1000;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT id, name, timezone FROM `location` ORDER BY name LIMIT ?,?',
    [offset, LIMIT]
  );
  rows = helper.rows(rows);
  const meta = {page};
  return {
    rows,
    meta
  }
}


async function getSingle(id){
  let [rows, fields] = await db.query(
    'SELECT id, name, timezone, latitude, longitude, stormsurfingurl, email \
     FROM `location` WHERE id = ?',
    [id]
  );
  return helper.first(rows);
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

function getFavorites(req){
  let favorites = req.cookies.favorites;
  favorites = favorites ? favorites.split('-') : [];
  return favorites.map(f => parseInt(f));
}


function setFavorites(res, favorites){
  favorites = (favorites || []).join('-');
  res.cookie('favorites', favorites, { maxAge: 900000, secure: true, sameSite: 'strict' });
}


// Keep your favorite locations at the top of the list.
function updateFavorites(req, res, locationId, isDeleting){
  let favorites = getFavorites(req);
  let index = favorites.indexOf(locationId);
  if(index >= 0){
    favorites.splice(index, 1);
  }
  if(!isDeleting){
    favorites.unshift(locationId);
  }
  setFavorites(res, favorites);
}


module.exports = {
  create,
  update,
  del,

  getSingle,
  getMultiple,

  getFavorites,
  setFavorites,
  updateFavorites
}