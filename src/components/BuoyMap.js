import L from 'leaflet';

const RADIUS_DEG = 8;
const DEFAULT_CENTER = { lat: 20.5, lon: -157.5 };
const DEFAULT_ZOOM = 5;

export default {
  template: '#buoy-map',
  name: 'BuoyMap',

  props: {
    center: {
      type: Object,
      required: false,   // { lat: Number, lon: Number }
      default: DEFAULT_CENTER
    }
  },

  data() {
    return {
      stations: [],
      buoyMap: null,
      markers: [],
      prevZoom: null
    };
  },

  methods: {
    inRadius(lat, lon) {
      const dLat = lat - this.center.lat;
      const dLon = lon - this.center.lon;
      return (dLat * dLat + dLon * dLon) <= (RADIUS_DEG * RADIUS_DEG);
    },

    renderStations() {
      this.markers.forEach(m => this.buoyMap.removeLayer(m));
      this.markers = [];

      const inRadiusStations = this.stations.filter(s => {
        const lat = parseFloat(s.getAttribute('lat'));
        const lon = parseFloat(s.getAttribute('lon'));
        const type = s.getAttribute('type');

        return (type === 'buoy' || type === 'other') && this.inRadius(lat, lon);
      });

      inRadiusStations.forEach(s => {
        const id = s.getAttribute("id");
        const name = s.getAttribute("name");
        const lat = parseFloat(s.getAttribute("lat"));
        const lon = parseFloat(s.getAttribute("lon"));

        const popupContent = `
          <a href="https://www.ndbc.noaa.gov/station_page.php?station=${id}" target="_blank">
            <strong>${id}</strong>
          </a><br>
          ${name}<br>
          ${lat}, ${lon}
        `;

        const marker = L.marker([lat, lon])
          .addTo(this.buoyMap)
          .bindPopup(popupContent);

        this.markers.push(marker);
      });
    }
  },

  mounted() {
    this.center = this.center.lat && this.center.lon ? this.center : DEFAULT_CENTER;
    this.buoyMap = L.map('map-canvas').setView(
      [this.center.lat, this.center.lon],
      DEFAULT_ZOOM // zoom.
    );

    this.prevZoom = this.buoyMap.getZoom();

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 12,
      minZoom: 4
    }).addTo(this.buoyMap);

    fetch('/api/activestations')
      .then(res => res.text())
      .then(text => {
        const xml = new DOMParser().parseFromString(text, 'application/xml');
        this.stations = [...xml.querySelectorAll('station')];
        this.renderStations();
      });

    this.buoyMap.on('zoomend', () => {
      const currentZoom = this.buoyMap.getZoom();
      if (currentZoom < this.prevZoom) {
        this.renderStations();
      }
      this.prevZoom = currentZoom;
    });
  }
};
