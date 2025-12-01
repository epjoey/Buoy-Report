import L from 'leaflet';

// HAWAII.
const CENTER = { lat: 20.5, lon: -157.5 };
const RADIUS_DEG = 8;

function inRadius(lat, lon){
  const dLat = lat - CENTER.lat;
  const dLon = lon - CENTER.lon;
  return (dLat * dLat + dLon * dLon) <= (RADIUS_DEG * RADIUS_DEG);
}

export default {
  template: '#buoy-map',
  name: 'BuoyMap',
  data() {
    return {
      stations: [],
      buoyMap: null,
      markers: [],
      prevZoom: null
    };
  },
  methods: {
    renderStations() {
      // remove old markers
      this.markers.forEach(m => this.buoyMap.removeLayer(m));
      this.markers = [];

      const inRadiusStations = this.stations.filter(s => {
        const lat = parseFloat(s.getAttribute('lat'));
        const lon = parseFloat(s.getAttribute('lon'));
        const type = s.getAttribute('type');   // get type
        return (type === 'buoy' || type === "other") && inRadius(lat, lon); // only buoys
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
    this.buoyMap = L.map('map-canvas').setView([CENTER.lat, CENTER.lon], 6);
    this.prevZoom = this.buoyMap.getZoom();

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 12,
      minZoom: 4
    }).addTo(this.buoyMap);

    fetch('/api/activestations')
      .then(res => res.text())
      .then(text => {
        const parser = new DOMParser();
        const xml = parser.parseFromString(text, 'application/xml');
        this.stations = [...xml.querySelectorAll('station')];
        this.renderStations();
      });

    this.buoyMap.on('zoomend', () => {
      const currentZoom = this.buoyMap.getZoom();
      if (currentZoom < this.prevZoom) {
        // only render markers when zooming out
        this.renderStations();
      }
      this.prevZoom = currentZoom;
    });
  }
};
