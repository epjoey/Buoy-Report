const express = require('express');
const router = express.Router();
const locationService = require('../services/locations');
const buoyService = require('../services/buoys');

const VIEW_ROUTES = [
  '/',
  '/about',
  '/me',

  '/buoys',
  '/buoys/:buoyId',

  '/locations/:locationId',
];

// Single page app: render index for all the view routes.
VIEW_ROUTES.forEach(route => {
  router.get(route, async function(req, res, next) {
    res.render('index');
  });
});

module.exports = router;
