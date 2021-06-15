var express = require('express');
var router = express.Router();
const reporters = require('../services/reporters');

/* GET users listing. */
router.get('/', async function(req, res, next) {
  try {
    rows = await reporters.getMultiple(req.query.page);
    res.render('reporters', { reporters: rows });
  } catch (err) {
    console.error(`Error while getting reporters `, err.message);
    next(err);
  }
});
module.exports = router;
