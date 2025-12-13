import { toggleFavorite } from './utils/favoritesUtils.js';
import About from './components/About.js';
import Locations from './components/Locations.js';
import Location from './components/Location.js';
import Snapshots from './components/Snapshots.js';
import Buoys from './components/Buoys.js';
import Buoy from './components/Buoy.js';
import BuoyMap from './components/BuoyMap.js';

//************************************************
// Router
const routes = [
  { path: '/', component: Locations, name: 'home' },
  { path: '/map', component: BuoyMap, name: 'map' },
  { path: '/about', component: About, name: 'about' },
  { path: '/me', component: Snapshots, name: 'me' },
  { path: '/buoys', component: Buoys, name: 'buoys' },
  { path: '/buoys/:id', component: Buoy, name: 'buoy' },
  { path: '/locations/:id', component: Location, name: 'location' }
];

const router = new VueRouter({
  mode: 'history', // optional, uses pushState instead of hash
  routes
});

//************************************************
new Vue({
  el: '#app',
  router,
  data: {
    isMenuOpen: false,
    buoy: null, // custom header on buoy page
    location: null // custom header on location page
  },
  computed: {
    route() {
      return this.$route.name; // 'home', 'map', 'about', etc.
    }
  },
  mounted: function(){
    let vm = this;

    // Hack? Child components sending data up here to customize the header.
    vm.$on('buoy:buoy', buoy => this.buoy = buoy);
    vm.$on('location:location', location => this.location = location);
  },
  methods: {
    toggleFavorite: function(location){
      toggleFavorite(this, location);
    },
    scrollTo: function(target){
      // window.location.hash = target;
      document.getElementById(target).scrollIntoView();
    }
  }
});
