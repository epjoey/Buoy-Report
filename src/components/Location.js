import { fetchJson } from '../utils/fetchUtils.js';
import Snapshots from './Snapshots.js';
import BuoyMap from './BuoyMap.js';
import UpdateLocation from './UpdateLocation.js';
import BuoyData from './BuoyData.js';

export default {
  name: 'Location',
  template: '#location',
  components: {
    Snapshots,
    BuoyMap,
    UpdateLocation,
    BuoyData,
  },
  data() {
    let idStr = window.location.pathname.match(/([\d]+)/g);
    let id = idStr ? parseInt(idStr[0]) : null;

    return {
      locationId: id,
      location: null,
      buoys: [],
      loading: false,
      mapEnabled: false
    };
  },

  computed: {

    tableUrl() {
      const params = new URLSearchParams(window.location.search);
      params.delete('map');

      const q = params.toString();
      return q ? `${window.location.pathname}?${q}` : window.location.pathname;
    },

    mapUrl() {
      const params = new URLSearchParams(window.location.search);
      params.set('map', '1');

      return `${window.location.pathname}?${params.toString()}`;
    }
  },

  watch: {
    mapEnabled(newVal) {
      const params = new URLSearchParams(window.location.search);

      if (newVal) {
        params.set('map', '1');
      } else {
        params.delete('map');
      }

      const newUrl =
        window.location.pathname +
        (params.toString() ? '?' + params.toString() : '');

      window.history.pushState({}, '', newUrl);
    }    
  },

  methods: {
    screenWidth() {
      return window.screen.width;
    },
  },

  mounted() {
    window.addEventListener('popstate', () => {
      this.mapEnabled = new URLSearchParams(window.location.search).has('map');
    });
  },

  created() {
    if (!this.locationId) return;

    this.mapEnabled = new URLSearchParams(window.location.search).has('map');

    fetchJson(this, 'GET', '/locations/' + this.locationId)
      .then((data) => {
        this.location = data.location;
        this.$root.$emit('location:location', this.location);
      });

    fetchJson(this, 'GET', '/locations/' + this.locationId + '/buoys')
      .then((data) => {
        this.buoys = data.buoys;
      });
  },

  destroyed() {
    this.$root.$emit('location:location', null);
  }
};
