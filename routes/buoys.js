const express = require('express');
const router = express.Router();
const buoyService = require('../services/buoys');

router.get('/', async function(req, res, next){
  let buoys = await buoyService.getMultiple(req.query.page);
  buoys = buoys.rows;
  res.render('buoys', {
    buoys: buoys
  });
});


router.get('/:buoyId', async function(req, res, next){
  let buoyId = parseInt(req.params.buoyId);
  const buoy = await buoyService.getSingle(buoyId);
  res.render('buoy', {
    buoy: buoy,
  });
});


router.get('/:buoyId/standard', async function(req, res, next) {
  let data, error;
  try {
    data = await buoyService.getBuoyStandardData(parseInt(req.params.buoyId), parseInt(req.query.offset) || 0);
  } catch(err){
    if(err === 404){
      return res.sendStatus(404);
    }
    error = err;
  }
  res.json({
    data: data,
    error: error
  });
});

router.get('/:buoyId/wave', async function(req, res, next) {
  let data, error;
  try {
    data = await buoyService.getBuoyWaveData(parseInt(req.params.buoyId), parseInt(req.query.offset) || 0);
  } catch(err){
    if(err === 404){
      return res.sendStatus(404);
    }
    error = err;
  }
  res.json({
    data: data,
    error: error
  });
});

module.exports = router;


