const express = require('express');
const router = express.Router();
const locationService = require('../services/locations');

router.get('/', async function(req, res, next) {
  const locations = await locationService.getMultiple(req.query.page);
  res.render('locations', { locations: locations });
});

router.get('/:locationId', async function(req, res, next) {
  const location = await locationService.getSingle(parseInt(req.params.locationId));
  console.log(location)
  const buoys = await locationService.getBuoys(location);
  res.render('location', {
    location: location,
    buoys: buoys
  });
});

module.exports = router;


