var express = require('express');
var router = express.Router();
const reporterService = require('../services/reporters');

/* GET users listing. */
router.get('/', async function(req, res, next) {
  try {
    reporters = await reporterService.getMultiple(req.query.page);
    res.render('reporters', { reporters: reporters });
  } catch (err) {
    console.error(`Error while getting reporters `, err.message);
    next(err);
  }
});
module.exports = router;
