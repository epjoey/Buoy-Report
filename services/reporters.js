const db = require('../db');
const helper = require('../helper');
const config = require('../config');

async function getMultiple(page = 1){
  const offset = helper.getOffset(page, config.listPerPage);
  let [rows, fields] = await db.query(
    'SELECT id, name FROM `reporter` LIMIT ?,?', 
    [offset, config.listPerPage]
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