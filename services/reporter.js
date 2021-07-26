const db = require('../db');
const helper = require('../helper');


async function getSingle(reporterId){
  const [rows, fields] = await db.query(
    'SELECT id, email, name FROM `reporter` WHERE id = ?', reporterId
  );
  return helper.first(rows);
}


async function del(reporterId){
  try {
    const [result, fields] = await db.query(
      'DELETE FROM `reporter` WHERE id = ?', reporterId
    );
    return [null, true];
  }
  catch(err){
    return [err.message, null];
  }
}


module.exports = {
  getSingle,
  del
}