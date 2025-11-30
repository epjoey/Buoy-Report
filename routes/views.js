const express = require('express');
const router = express.Router();
const locationService = require('../services/locations');
const buoyService = require('../services/buoys');

const VIEW_ROUTES = [
  '/',
  '/about',
  '/me',
  '/map',

  '/buoys',
  '/buoys/:buoyId',

  '/locations/:locationId'
];

// Single page app: render index for all the view routes.
VIEW_ROUTES.forEach(route => {
  router.get(route, (req, res) => res.render('index'));
});

// Legacy
router.get('/l/:locationId', (req, res) => {
  res.redirect(301, '/locations/' + req.params.locationId);
});

module.exports = router;
