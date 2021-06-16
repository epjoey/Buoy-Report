const express = require('express');
const router = express.Router();
const locations = require('../services/locations');

/* GET home page. */
router.get('/', async function(req, res, next) {
  const rows = await locations.getMultiple(req.query.page);
  res.render('index', { locations: rows });  
});

module.exports = router;
