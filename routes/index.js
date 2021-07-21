const express = require('express');
const _ = require('lodash');
const router = express.Router();
const locationService = require('../services/locations');

/* GET home page. */
router.get('/', async function(req, res, next) {
  let locations = await locationService.getMultiple(req.query.page);
  locations = locations.rows;

  let favorites = locationService.getFavorites(req);
  let orderedLocations = [];

  favorites.forEach(id => {
    location = locations.find(loc => loc.id === id);
    if(location){
      orderedLocations.push(location);
    }
  })

  orderedLocations = _.union(orderedLocations, locations);
  res.render('index', { locations: orderedLocations });
});

router.get('/about', function(req, res, next) {
  res.render('about', {});
});

module.exports = router;
