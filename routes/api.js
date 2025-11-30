const _ = require('lodash');
const express = require('express');
const createError = require('http-errors');
const router = express.Router();
const locationService = require('../services/locations');
const buoyService = require('../services/buoys');
const snapshotService = require('../services/snapshots');
const helper = require('../helper');
const db = require('../db');


/* Locations */
router.get('/locations', async function(req, res){
  let locations = await locationService.getMultiple(req.user);
  res.json({ locations });
});

router.post('/locations', helper.secured, async function(req, res, next){
  const [error, locationId] = await locationService.create(req.body, req.user);
  res.json({ locationId, error });
});

router.get('/locations/:locationId', async function(req, res, next){
  const location = await locationService.getSingle(req.params.locationId, req.user);
  if(!location){
    return res.sendStatus(404);
  }
  res.json({ location });
});

router.put('/locations/:locationId', helper.secured, async function(req, res, next){
  const [error, location] = await locationService.update(parseInt(req.params.locationId), req.body, req.user);
  res.json({ location, error });
});

router.delete('/locations/:locationId', helper.secured, async function(req, res, next){
  let location = await locationService.getSingle(parseInt(req.params.locationId), req.user);
  if(!req.user.isAdmin && req.user._json.email !== location.email){
    return res.sendStatus(404);
  }
  const [error, success] = await locationService.del(location);
  res.json({ success, error });
});

router.get('/locations/:locationId/snapshots', async function(req, res, next){
  const snapshots = await snapshotService.forLocation(
    parseInt(req.params.locationId),
    req.user,
    req.query.page
  );
  res.json({ snapshots });
});

router.post('/locations/:locationId/snapshots', async function(req, res, next){
  const [error, snapshot] = await snapshotService.create(parseInt(req.params.locationId), req.body, req.user);
  res.json({ snapshot, error });
});

router.get('/locations/:locationId/buoys', async function(req, res, next){
  const buoys = await buoyService.forLocation(parseInt(req.params.locationId));
  res.json({ buoys });
});

/* Buoys */
router.get('/buoys', async function(req, res, next){
  const buoys = await buoyService.getMultiple(req.query.page);
  res.json({ buoys });
});

router.post('/buoys', helper.secured, async function(req, res, next){
  const [error, buoy] = await buoyService.create(req.body);
  res.json({ buoy, error });
});

router.get('/buoys/:buoyId', async function(req, res, next){
  const buoy = await buoyService.getSingle(parseInt(req.params.buoyId));
  if(!buoy){
    return res.sendStatus(404);
  }
  res.json({ buoy });
});

router.put('/buoys/:buoyId', helper.secured, async function(req, res, next){
  const [error, buoy] = await buoyService.update(parseInt(req.params.buoyId), req.body);
  res.json({ buoy, error });
});

router.delete('/buoys/:buoyId', helper.secured, async function(req, res, next){
  let buoyId = parseInt(req.params.buoyId);
  const [error, success] = await buoyService.del(buoyId);
  res.json({ success, error });
});

router.get('/buoys/:buoyId/data', async function(req, res, next) {
  const [error, rows] = await buoyService.getData(
    parseInt(req.params.buoyId),
    req.query.type,
    parseInt(req.query.offset) || 0
  );
  res.json({ rows, error });
});


/* Snapshots */
router.get("/snapshots", helper.secured, async function(req, res, next){
  const snapshots = await snapshotService.forUser(req.user, req.query.page);
  res.json({ snapshots });
});

router.put('/snapshots/:snapshotId', helper.secured, async function(req, res, next){
  let snapshot = await snapshotService.getSingle(req.params.snapshotId);
  if(!snapshot || (req.user._json.email !== snapshot.email && !req.user.isAdmin)) {
    return res.sendStatus(404);
  }
  let params = _.pick(req.body, ['waveheight', 'quality', 'imagepath', 'text']);
  await db.query(
    'UPDATE `report` SET ? WHERE id = ?', [params, snapshot.id]
  );
  snapshot = await snapshotService.getSingle(snapshot.id);
  res.json({ snapshot });
});

router.delete('/snapshots/:snapshotId', helper.secured, async function(req, res, next){
  let snapshot = await snapshotService.getSingle(req.params.snapshotId);
  if(!snapshot || (req.user._json.email !== snapshot.email && !req.user.isAdmin)) {
    return res.sendStatus(404);
  }
  const [result, fields] = await db.query(
    'DELETE FROM `report` WHERE id = ?', snapshot.id
  );

  res.json({ 'success': true });
});


/* Favorites */
router.post("/favorites/:locationId", helper.secured, async function(req, res, next){
  let params = {email: req.user._json.email, locationid: req.params.locationId};
  await db.query('INSERT INTO `favorites` SET ?', params);
  res.json({});
});

router.delete("/favorites/:locationId", helper.secured, async function(req, res, next){
  await db.query(
    'DELETE FROM `favorites` WHERE email = ? AND locationId = ?',
    [req.user._json.email, req.params.locationId]
  );
  res.json({});
});

router.get('/activestations', async (req, res) => {
  try {
    const response = await fetch('https://www.ndbc.noaa.gov/activestations.xml');
    const text = await response.text();
    res.setHeader('Content-Type', 'application/xml');
    res.send(text);
  } catch (err) {
    console.error(err);
    res.status(500).send('Error fetching buoy data');
  }
});

module.exports = router;
