const https = require('https');

function getOffset(currentPage = 1, listPerPage = 1000){
  return (currentPage - 1) * [listPerPage];
}

function emptyOrRows(rows){
  if(!rows){
    return [];
  }
  return rows;
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
  emptyOrRows,
  makeRequest
}