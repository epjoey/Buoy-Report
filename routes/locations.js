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
  const [error, locationId] = await locationService.create(req.body, req.user);
  res.json({ locationId, error });
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
  res.render('location', { location, buoys });
});


router.put('/:locationId', helper.secured, async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  const [error, location] = await locationService.update(locationId, req.body);
  res.json({ location, error });
});


router.delete('/:locationId', helper.secured, async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  let location = await locationService.getSingle(locationId);
  let userEmail = req.user._json.email;
  if(userEmail !== location.email && userEmail !== 'jhodara@gmail.com'){
    return res.sendStatus(404);
  }
  const [error, success] = await locationService.del(location);
  locationService.updateFavorites(req, res, locationId, true);
  res.json({ success, error });
});


router.get('/:locationId/snapshots', async function(req, res, next){
  const snapshots = await snapshotService.forLocation(
    req.params.locationId,
    req.user,
    req.query.page
  );
  res.json({ snapshots });
});


router.post('/:locationId/snapshots', async function(req, res, next){
  const locationId = parseInt(req.params.locationId);
  const [error, snapshot] = await snapshotService.create(locationId, req.body, req.user);
  res.json({ snapshot, error });
});


module.exports = router;


