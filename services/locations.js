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
    return [null, await getSingle(locationId, user)];
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

const LOCATION_SELECT = 'SELECT id, name, timezone, latitude, longitude, email FROM `location`';
async function getMultiple(user){
  let locations = await helper.rows(LOCATION_SELECT + 'ORDER BY name');

  if(user){
    let favorites = await helper.rows('SELECT locationid FROM `favorites` WHERE email = ?', user._json.email);
    let favoritesMap = {};
    favorites.forEach(function(fave){
      favoritesMap[fave.locationid] = true;
    });
    locations.forEach(function(location){
      location.$isFavorite = favoritesMap[location.id];
    });
  }
  return locations;
}


async function getSingle(id, user){
  let location = await helper.first(LOCATION_SELECT + 'WHERE id = ?', [id]);
  if(user){
    let favorite = await helper.first(
      'SELECT email FROM `favorites` WHERE locationid = ? AND email = ?',
      [id, user._json.email]
    );
    location.$isFavorite = !!favorite;
  }
  return location;
}


async function addBuoysToLocation(buoyIds, locationId){
  // `buoyIds` is a list of buoy ids seperated by comma or space.
  if(!buoyIds){
    return;
  }
  buoyIds = _.map(_.split(buoyIds, /[ ,]+/), _.trim);
  let i = 0; // increment `created` to maintain order.
  buoyIds.forEach(async function(buoyId){
    i += 1;
    let params = {buoyid: buoyId, locationid: locationId, created: Date.now() + i};
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