const express = require('express');
const router = express.Router();
const locationService = require('../services/locations');


router.get('/:locationId', async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  locationService.updateFavorites(req, res, locationId);

  const location = await locationService.getSingle(locationId);
  const buoys = await locationService.getBuoys(location);
  res.render('location', {
    location: location,
    buoys: buoys
  });
});

module.exports = router;


