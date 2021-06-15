var express = require('express');
var router = express.Router();
const locations = require('../services/locations');

/* GET programming languages. */
router.get('/', async function(req, res, next) {
  try {
    res.json(await locations.getMultiple(req.query.page));
  } catch (err) {
    console.error(`Error while getting locations `, err.message);
    next(err);
  }
});

module.exports = router;


