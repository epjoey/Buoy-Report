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
function updateFavorites(req, res, locationId){
  let favorites = getFavorites(req);
  let index = favorites.indexOf(locationId);
  if(index >= 0){
    favorites.splice(index, 1);
  }
  favorites.unshift(locationId);
  setFavorites(res, favorites);
}


module.exports = {
  getSingle,
  getMultiple,
  getBuoys,

  getFavorites,
  setFavorites,
  updateFavorites
}