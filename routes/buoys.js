const express = require('express');
const router = express.Router();
const buoyService = require('../services/buoys');
const helper = require('../helper');


router.get('/', async function(req, res, next){
  const buoys = await buoyService.getMultiple(req.query.page);
  res.render('buoys', { buoys });
});


router.post('/', helper.secured, async function(req, res, next){
  const [error, buoy] = await buoyService.create(req.body);
  res.json({ buoy, error });
});


router.get('/:buoyId', async function(req, res, next){
  const buoy = await buoyService.getSingle(parseInt(req.params.buoyId));
  res.render('buoy', { buoy });
});


router.put('/:buoyId', helper.secured, async function(req, res, next){
  const [error, buoy] = await buoyService.update(parseInt(req.params.buoyId), req.body);
  res.json({ buoy, error });
});


router.delete('/:buoyId', helper.secured, async function(req, res, next){
  let buoyId = parseInt(req.params.buoyId);
  const [error, success] = await buoyService.del(buoyId);
  res.json({ success, error });
});


router.get('/:buoyId/data', async function(req, res, next) {
  const [error, data] = await buoyService.getData(
    parseInt(req.params.buoyId),
    req.query.type,
    parseInt(req.query.offset) || 0
  );
  res.json({ data, error });
});



module.exports = router;


