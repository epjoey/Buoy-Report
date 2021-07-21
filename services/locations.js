const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');


async function create(reqBody, user){
  let params = _.pick(reqBody, ['name']);
  if(reqBody.latitude){
    params.latitude = parseFloat(reqBody.latitude).toFixed(3);
  }
  if(reqBody.longitude){
    params.longitude = parseFloat(reqBody.longitude).toFixed(3);
  }
  params.email = user._json.email;
  console.log(params)
  const [result, fields] = await db.query('INSERT INTO `location` SET ?', params);
  return result.insertId ? getSingle(result.insertId) : null;
}


async function update(location, reqBody, user){
  let params = _.pick(reqBody, ['name']);
  if(reqBody.latitude){
    params.latitude = parseFloat(reqBody.latitude).toFixed(3);
  }
  if(reqBody.longitude){
    params.longitude = parseFloat(reqBody.longitude).toFixed(3);
  }
  const [result, fields] = await db.query(
    'UPDATE `location` SET ? WHERE id = ?', [params, location.id]
  );
  return result.changedRows ? getSingle(location.id) : null;
}


async function del(location){
  const [result, fields] = await db.query(
    'DELETE FROM `location` WHERE id = ?', location.id
  );
  return result.affectedRows ? true : false;
}

async function getMultiple(page = 1){
  const LIMIT = 1000;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT id, name, timezone FROM `location` LIMIT ?,?', 
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
    'SELECT id, name, timezone, latitude, longitude, email \
     FROM `location` WHERE id = ?', 
    [id]
  );
  return helper.first(rows);
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


function getFavorites(req){
  let favorites = req.cookies.favorites;
  favorites = favorites ? favorites.split('-') : [];
  return favorites.map(f => parseInt(f));  
}


function setFavorites(res, favorites){
  favorites = (favorites || []).join('-');
  res.cookie('favorites', favorites);
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
  getBuoys,

  getFavorites,
  setFavorites,
  updateFavorites
}