var assert = require('assert');
const https = require('https');
const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');


async function create(reqBody){
  try {
    const buoyid = parseInt(reqBody.buoyid);
    assert(buoyid > 0, 'Invalid buoy id');
    const params = {buoyid, name: reqBody.name};
    const [result, fields] = await db.query('INSERT INTO `buoy` SET ?', params);
    return [null, await getSingle(buoyid)];
  }
  catch(err){
    return [err.message, null];
  }
}


async function update(buoyId, reqBody){
  try{
    const params = _.pick(reqBody, ['name']);
    const [result, fields] = await db.query(
      'UPDATE `buoy` SET ? WHERE buoyid = ?', [params, buoyId]
    );
    return [null, await getSingle(buoyId)];
  }
  catch(err){
    return [err.message, null];
  }
}


async function del(buoyId){
  try {
    const [result, fields] = await db.query(
      'DELETE FROM `buoy` WHERE buoyid = ?', buoyId
    );
    return [null, true];
  }
  catch(err){
    return [err.message, null];
  }
}


function splitRows(data, offset, limit){
  let rows = data.split("\n");
  let result = [];
  rows = _.slice(rows, offset, offset + limit);
  _.forEach(rows, function(row){
    row = _.compact(row.split(" "));
    result.push(row);
  });
  return result;
}


async function getData(id, type, offset=0, limit=24){ // get 24 hours of data
  // Standard data: https://www.ndbc.noaa.gov/data/realtime2/46012.txt
  // Wave data: https://www.ndbc.noaa.gov/data/realtime2/46012.spec
  let url = 'https://www.ndbc.noaa.gov/data/realtime2/' + id + (type === 'wave' ? '.spec' : '.txt');
  console.log('fetching buoy data from', url);
  try {
    let data = await helper.makeRequest(url);
    let rows = splitRows(data, offset, limit);
    return [null, rows];
  } catch(err){
    console.log('error getting rows from buoy', id, err);
    return [err.message, []];
  }
}


async function forLocation(locationId){
  let rows = await helper.rows(
    'SELECT bl.buoyid, b.name \
    FROM `buoy_location` bl \
    LEFT JOIN `buoy` b ON bl.buoyid = b.buoyid \
    WHERE bl.locationid = ? \
    ORDER BY bl.created asc',
    locationId
  );
  return rows;
}


async function getMultiple(){
  let rows = await helper.rows('SELECT buoyid, name FROM `buoy` ORDER BY buoyid');
  return rows
}

async function getSingle(id){
  return await helper.first(
    'SELECT buoyid, name FROM `buoy` WHERE buoyid = ?',
    [id]
  );
}


module.exports = {
  create,
  update,
  del,
  getData,
  forLocation,
  getMultiple,
  getSingle
}