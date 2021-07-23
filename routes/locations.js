const express = require('express');
const createError = require('http-errors');
const router = express.Router();
const locationService = require('../services/locations');
const snapshotService = require('../services/snapshots');
const buoyService = require('../services/buoys');
const helper = require('../helper');


router.get('/', function(req, res){
  res.redirect(301, '/'); // Redirect /locations to /
});


router.post('/', helper.secured, async function(req, res, next){
  let error, locationId;
  try{
    locationId = await locationService.create(req.body, req.user);
  }
  catch(err){
    error = err.message;
  }
  res.json({
    locationId: locationId,
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
  const buoys = await buoyService.forLocation(location);
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
    locationService.updateFavorites(req, res, locationId, true);
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
  const snapshots = await snapshotService.forLocation(req.params.locationId, req.query.page);
  res.json({
    snapshots: snapshots
  });
});


module.exports = router;


