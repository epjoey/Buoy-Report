const express = require('express');
const router = express.Router();
const locationService = require('../services/locations');
const buoyService = require('../services/buoys');

router.get('/', async function(req, res, next) {
  res.render('index');
});

router.get('/about', function(req, res, next) {
  res.render('about', {});
});

router.get('/locations/:locationId', async function(req, res, next){
  const location = await locationService.getSingle(req.params.locationId, req.user);
  if(!location){
    return next(createError(404));
  }
  res.render('location', { location });
});

router.get('/buoys', async function(req, res, next){
  const buoys = await buoyService.getMultiple(req.query.page);
  res.render('buoys', { buoys });
});

router.get('/buoys/:buoyId', async function(req, res, next){
  const buoy = await buoyService.getSingle(parseInt(req.params.buoyId));
  res.render('buoy', { buoy });
});


module.exports = router;
