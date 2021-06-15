const db = require('../db');
const helper = require('../helper');

async function getMultiple(page = 1){
  const LIMIT = 1000;
  const offset = helper.getOffset(page, LIMIT);
  let [rows, fields] = await db.query(
    'SELECT id, name FROM `reporter` LIMIT ?,?', 
    [offset, LIMIT]
  );
  rows = helper.emptyOrRows(rows);
  const meta = {page};

  return {
    rows,
    meta
  }
}

module.exports = {
  getMultiple
}