const express = require('express');
const createError = require('http-errors');
const router = express.Router();
const snapshotService = require('../services/snapshots');
const helper = require('../helper');
const db = require('../db');


router.delete('/:snapshotId', helper.secured, async function(req, res, next){
  let snapshot = await snapshotService.getSingle(req.params.snapshotId);
  if(!snapshot || (req.user._json.email !== snapshot.email && !req.user.isAdmin)) {
    return res.sendStatus(404);
  }
  const [result, fields] = await db.query(
    'DELETE FROM `report` WHERE id = ?', snapshot.id
  );

  res.json({ 'success': true });
});


module.exports = router;
