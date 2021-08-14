const _ = require('lodash');
const express = require('express');
const createError = require('http-errors');
const router = express.Router();
const locationService = require('../services/locations');
const snapshotService = require('../services/snapshots');
const buoyService = require('../services/buoys');
const helper = require('../helper');


router.get('/', async function(req, res){
  let locations = await locationService.getMultiple(req.query.page, req.user);
  res.json({ locations: locations.rows || [] });
});


router.post('/', helper.secured, async function(req, res, next){
  const [error, locationId] = await locationService.create(req.body, req.user);
  res.json({ locationId, error });
});


router.get('/:locationId', async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  const location = await locationService.getSingle(locationId, req.user);
  if(!location){
    return next(createError(404));
  }
  const buoys = await buoyService.forLocation(locationId);
  res.render('location', { location, buoys });
});


router.put('/:locationId', helper.secured, async function(req, res, next){
  const locationId = parseInt(req.params.locationId);
  const [error, location] = await locationService.update(locationId, req.body, req.user);
  res.json({ location, error });
});


router.delete('/:locationId', helper.secured, async function(req, res, next){
  let locationId = parseInt(req.params.locationId);
  let location = await locationService.getSingle(locationId, req.user);
  if(!req.user.isAdmin && req.user._json.email !== location.email){
    return res.sendStatus(404);
  }
  const [error, success] = await locationService.del(location);
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


