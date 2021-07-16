const https = require('https');
const _ = require('lodash');
const db = require('../db');
const helper = require('../helper');

const LIMIT = 24; // get 24 hours of data.

function splitRows(data, offset){
  let rows = data.split("\n");
  let result = [];
  rows = _.slice(rows, offset, offset + LIMIT);
  _.forEach(rows, function(row){
    row = _.compact(row.split(" "));
    result.push(row);
  });
  return result;
}

async function getBuoyStandardData(id, offset = 0){
  // Example: https://www.ndbc.noaa.gov/data/realtime2/46012.txt
  const URL = 'https://www.ndbc.noaa.gov/data/realtime2/' + id + '.txt';
  return helper.makeRequest(URL).then(function(data){
    return splitRows(data, offset);
  });
}

async function getBuoyWaveData(id, offset = 0){
  // Example: https://www.ndbc.noaa.gov/data/realtime2/51205.spec
  const URL = 'https://www.ndbc.noaa.gov/data/realtime2/' + id + '.spec';
  return helper.makeRequest(URL).then(function(data){
    return splitRows(data, offset);
  });
}


module.exports = {
  getBuoyStandardData,
  getBuoyWaveData
}