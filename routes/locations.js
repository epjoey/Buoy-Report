const express = require('express');
const router = express.Router();
const locationService = require('../services/locations');
const snapshotService = require('../services/snapshots');


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

router.get('/:locationId/snapshots', async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  const location = await locationService.getSingle(locationId);
  const snapshots = await snapshotService.forLocation(locationId);
  res.render('location-snapshots', {
    location: location,
    snapshots: snapshots
  });
});

module.exports = router;


