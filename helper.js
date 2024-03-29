const https = require('https');
const db = require('./db');

function getOffset(currentPage = 1, listPerPage = 1000){
  return (currentPage - 1) * [listPerPage];
}

async function rows(query, params){
  let [rows,] = await db.query(query, params);
  if(!rows){
    return [];
  }
  return rows;
}

async function first(query, params){
  let [rows,] = await db.query(query, params);
  if(!rows || !rows.length){
    return null;
  }
  return rows[0];
}

function secured(req, res, next){
  if(req.user){
    return next();
  }
  req.session.returnTo = req.originalUrl;
  res.redirect("/login");
}

function makeRequest(url){
  return new Promise((resolve, reject) => {
    const req = https.get(url, res => {
      console.log("Got response: " + res.statusCode);
      
      if(res.statusCode === 404){
        return reject(res.statusCode)
      }
      let data = '';

      res.on('data', (chunk) => {
        data += chunk;
      });

      res.on('end', () => {
        resolve(data);
      });
    });
    req.on('error', e => {
      console.log("Got error: " + e.message);
      reject(e);
    }); 
  });
}

module.exports = {
  getOffset,
  rows,
  first,
  makeRequest,
  secured
}