const express = require('express');
const createError = require('http-errors');
const router = express.Router();
const locationService = require('../services/locations');
const snapshotService = require('../services/snapshots');
const helper = require('../helper');


router.post('/', helper.secured, async function(req, res, next){
  let error, location;
  try{
    location = await locationService.create(req.body, req.user);
  }
  catch(err){
    error = err.message;
  }
  res.json({
    location: location,
    error: error
  })
});


router.get('/:locationId', async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  const location = await locationService.getSingle(locationId);
  if(!location){
    return next(createError(404));
  }
  // Make it show up first in the index page.
  locationService.updateFavorites(req, res, locationId);
  const buoys = await locationService.getBuoys(location);
  res.render('location', {
    location: location,
    buoys: buoys
  });
});


router.put('/:locationId', helper.secured, async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  let location = await locationService.getSingle(locationId);
  let error;
  try{
    location = await locationService.update(location, req.body, req.user);
  }
  catch(err){
    error = err.message;
  }
  res.json({
    location: location,
    error: error
  })
});


router.delete('/:locationId', helper.secured, async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  let location = await locationService.getSingle(locationId);
  let userEmail = req.user._json.email;
  if(userEmail !== location.email && userEmail !== 'jhodara@gmail.com'){
    return res.sendStatus(404);
  }
  let success, error;
  try{
    success = await locationService.del(location);
  }
  catch(err){
    error = err.message;
  }
  res.json({
    success: success,
    error: error
  })
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


