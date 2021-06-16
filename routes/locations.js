const express = require('express');
const router = express.Router();
const locations = require('../services/locations');

router.get('/', async function(req, res, next) {
  const rows = await locations.getMultiple(req.query.page);
  res.render('locations', { locations: rows });
});

router.get('/:locationId', async function(req, res, next) {
  const location = await locations.getSingle(parseInt(req.params.locationId));
  const buoys = await locations.getBuoys(location);
  res.render('location', {
    location: location,
    buoys: buoys
  });
});

module.exports = router;


