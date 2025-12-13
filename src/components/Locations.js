import { toggleFavorite } from '../utils/favoritesUtils.js';
import { fetchJson } from '../utils/fetchUtils.js';
import AddLocation from './AddLocation.js'

export default {
  name: 'Locations',
  template: '#locations',
  components: {
    AddLocation,
  },
  data: function(){
    return {
      adding: false,
      locations: [],
      locationSearchText: ''
    };
  },
  methods: {
    isFavorite: function(locations){
      return locations.filter(loc => loc.$isFavorite);
    },
    matchesSearchText: function(location){
      return !this.locationSearchText.length ||
        location.name.toLowerCase().includes(this.locationSearchText.toLowerCase());
    },
    toggleFavorite: function(location){
      toggleFavorite(this, location);
    }
  },
  created: function(){
    var self = this;
    fetchJson(self, 'GET', "/locations").then(function(data){
      self.locations = data.locations;
    });
  }
};