Vue.prototype.fetch = function(method, url, data){
  var vm = this;
  vm.loading = true;
  let config = {
    method: method,
    headers: {
      'Content-Type': 'application/json'
    }
  };
  if(method === 'POST' || method === 'PUT'){
    config.body = JSON.stringify(data);
  }
  return fetch('/api' + url, config)
    .then(response => response.json())
    .then(function(data){
      vm.loading = false;
      if(data.error){
        throw data.error;
      }
      return data;
    })
    .catch(function(err){
      vm.loading = false;
      vm.error = err;
      vm.$forceUpdate();
    });
};

function toggleFavorite(vm, location){
  var url = '/favorites/' + location.id;
  var isFavorite = location.$isFavorite;
  vm.fetch(isFavorite ? 'DELETE' : 'POST', url, {}).then(function(data){
    location.$isFavorite = !isFavorite;
    vm.$forceUpdate();
  });
}

//************************************************
Vue.component('locations', {
  template: '#locations',
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
    this.fetch('GET', "/locations").then(function(data){
      self.locations = data.locations;
    });
  }
});


//************************************************
Vue.component('add-location', {
  template: '#add-location',
  data: function(){
    return {
      req: {},
      error: '',
      loading: false
    }
  },
  methods: {
    invalid: function(){
      return !this.req.name;
    },
    submit: function(){
      this.fetch('POST', '/locations', this.req)
        .then(data => this.$goTo('/locations/' + data.locationId));
    }
  }
});


//************************************************
// Buoy Readings.
const DIRECTIONS = {
  'NNW': [337.5, 360],
  'NNE': [0, 22.5],
  'NE': [22.5, 67.5],
  'E': [67.5, 112.5],
  'SE': [112.5, 157.5],
  'S': [157.5, 202.5],
  'SW': [202.5, 247.5],
  'W': [247.5, 292.5],
  'NW': [292.5, 337.5]
};

function meters2Feet(meters){
  // Missing data in the Realtime files are denoted by "MM" (https://www.ndbc.noaa.gov/measdes.shtml#stdmet).
  if(meters === 'MM'){
    return 'MM';
  }
  return (parseFloat(meters) * 3.28084).toFixed(1);
}

function metersPerSec2mph(metersPerSec){
  if(metersPerSec === 'MM'){
    return 'MM';
  }
  return (parseFloat(metersPerSec) * 2.23694).toFixed(1); // meters/sec -> mph
}

function parseDateTime(year, month, day, hour, minute){
  return Vue.prototype.$moment(year + "-" + month + "-" + day + "T" + hour + ":" + minute + ":00Z");
}

function parseSeconds(seconds){
  if(seconds === 'MM'){
    return 'MM';
  }
  return parseFloat(seconds).toFixed(1);
}

function parseDirection(bearing){
  if(bearing == 360 || bearing == 0){
    return 'N';
  }
  else {
    return Object.keys(DIRECTIONS).find(key => {
      const angles = DIRECTIONS[key];
      return bearing >= angles[0] && bearing < angles[1];
    });
  }
}

// Standard Wave Data from NOAA looks like:
// #YY  MM DD hh mm WDIR WSPD GST  WVHT   DPD   APD MWD   PRES  ATMP  WTMP  DEWP  VIS PTDY  TIDE
// #yr  mo dy hr mn degT m/s  m/s     m   sec   sec degT   hPa  degC  degC  degC  nmi  hPa    ft
// 2021 07 14 21 40 230  3.0  5.0    MM    MM    MM  MM 1015.6  12.7    MM  11.1   MM   MM    MM
// 2021 07 14 21 30 230  3.0  4.0    MM    MM    MM  MM 1015.6  12.7    MM  11.1   MM   MM    MM
// 2021 07 14 21 20 220  3.0  4.0    MM    MM    MM  MM 1015.6  12.6    MM  11.0   MM   MM    MM
function parseStandardData(dataRow){
  var row = {};
  row.time = parseDateTime(dataRow[0], dataRow[1], dataRow[2], dataRow[3], dataRow[4]);
  row.windDirection = dataRow[5];
  row.windDirectionAbbr = parseDirection(row.windDirection);
  row.windSpeed = metersPerSec2mph(dataRow[6]);
  row.gust = metersPerSec2mph(dataRow[7]);
  row.waveHeight = meters2Feet(dataRow[8]);
  row.wavePeriod = parseSeconds(dataRow[9]);
  row.waveDirection = dataRow[11];
  row.waveDirectionAbbr = parseDirection(row.waveDirection);
  return row;
}

// Spectral Wave Data from NOAA looks like:
// #YY  MM DD hh mm WVHT  SwH  SwP  WWH  WWP SwD WWD  STEEPNESS  APD MWD
// #yr  mo dy hr mn    m    m  sec    m  sec  -  degT     -      sec degT
// 2021 07 14 21 40  2.2  2.1  9.1  0.4  3.3  NW   W    AVERAGE  7.4 316
// 2021 07 14 20 40  2.1  2.1 10.0  0.4  3.3  NW WNW    AVERAGE  7.3 315
// 2021 07 14 19 40  2.0  2.0 10.0  0.4  4.0  NW   W    AVERAGE  7.4 312
function parseWaveData(dataRow){
  var row = {};
  row.time = parseDateTime(dataRow[0], dataRow[1], dataRow[2], dataRow[3], dataRow[4]);
  row.waveHeight = meters2Feet(dataRow[5]);
  row.swellHeight = meters2Feet(dataRow[6]);
  row.swellPeriod = parseSeconds(dataRow[7]);
  row.meanWaveDirection = parseInt(dataRow[14]);
  row.swellDirection = dataRow[10];
  row.windWaveSummary = meters2Feet(dataRow[8]) + ' / ' + parseSeconds(dataRow[9]);
  row.windWaveDirection = dataRow[11];
  return row;
}

function parseBuoyData(rows, type){
  if(!rows || !rows.length){
    return [];
  }
  // Both data sets have 2 rows of units.
  if(rows[0][0] === "#YY" && rows[1][0] === "#yr"){
    rows = rows.slice(2);
  }
  return rows.map(type === 'wave' ? parseWaveData : parseStandardData);
}

Vue.component('buoy-data', {
  template: '#buoy-data',
  props: ['buoy', 'type', 'location'],
  data: function(){
    return {
      tables: []
    }
  },
  methods: {
    load: function(){
      const BUOY_DATA_ROWS_PER_TABLE = 24;
      let offset = this.tables.length * BUOY_DATA_ROWS_PER_TABLE;
      let url = '/buoys/' + this.buoy.buoyid + '/data?type=' + this.type + '&offset=' + offset;
      let vm = this;
      this.fetch('GET', url).then(function(data){
        if(data.rows.length){
          vm.tables.push({
            rows: parseBuoyData(data.rows, vm.type)
          });
        }
        else {
          vm.$forceUpdate();
        }
      });
    },
    formatDate: function(time){
      time = time.clone();
      return time.tz(this.location && this.location.timezone || 'UTC').format('M/D h:mm a');
    }
  },
  created: function(){
    this.load();
  }
});


//************************************************
/* Snapshots */
function parseSnapshotBuoyData(buoy){
  buoy.waveheight = meters2Feet(buoy.waveheight);
  buoy.swellheight = meters2Feet(buoy.swellheight);
  buoy.swellperiod = parseSeconds(buoy.swellperiod);
  buoy.windWaveSummary = meters2Feet(buoy.windwaveheight) + ' / ' + parseSeconds(buoy.windwaveperiod);
  return buoy;
}

function parseSnapshot(snapshot){
  snapshot.$waveHeight = snapshot.waveheight ? WAVE_HEIGHTS[snapshot.waveheight] : '';
  snapshot.$qualityText = snapshot.quality ? QUALITIES[snapshot.quality] : '';

  snapshot.$imagePath = snapshot.imagepath;
  if(snapshot.$imagePath && !snapshot.$imagePath.startsWith('http')){ // Super legacy
    snapshot.$imagePath = 'https://www.buoyreport.com/uploads/' + snapshot.imagepath;
  }
  else if(snapshot.$imagePath){ // Cloudinary
    snapshot.$imagePath = snapshot.$imagePath.replace("/image/upload/", "/image/upload/c_scale,w_680/");
  }

  snapshot.$buoyData = (snapshot.buoyData || []).map(parseSnapshotBuoyData);
  snapshot.$by = snapshot.email ? snapshot.email.split('@')[0] : 0;
  return snapshot;
}

const WAVE_HEIGHTS = {
  '1.5': "1-2'",
  '2.5': "2-3'",
  '3.5': "3-4'",
  '5': "4-6'",
  '7': "6-8'",
  '9': "8-10'",
  '11': "10-12'",
  '13.5': "12-15'",
  '17.5': "15-20'",
  '25': "20-30'"
};
let WAVE_HEIGHTS_KEYS = Object.keys(WAVE_HEIGHTS).sort((a, b) => parseFloat(a) - parseFloat(b));

WAVE_HEIGHTS_KEYS.unshift(null);

var QUALITIES = {
  1: 'Bad',
  2: 'OK',
  3: 'Fun',
  4: 'Great',
  5: 'Epic'
};

function submitImage(vm, imagePath, onSuccess){
  vm.loading = true;
  let folder = Vue.$nodeEnv === 'production' ? 'buoyreport' : 'buoyreport_dev';
  var formData = new FormData();
  formData.append("file", imagePath);
  formData.append("upload_preset", Vue.$nodeEnv === 'production' ? 'buoyreport' : 'buoyreport_dev');
  formData.append("folder", folder);
  var xhr = new XMLHttpRequest();
  xhr.onload = function(){
    vm.loading = false;
    var response = JSON.parse(xhr.responseText);
    if(response.secure_url){
      onSuccess(response.secure_url);
    }
    else {
      vm.error = 'Error uploading image';
      console.warn(response);
      vm.$forceUpdate();
    }
  };
  xhr.open("post", "https://api.cloudinary.com/v1_1/duq2wnb9p/image/upload");
  xhr.send(formData);
}

//************************************************
Vue.component('add-snapshot', {
  template: '#add-snapshot',
  props: ['location', 'snapshots'],
  data: function(){
    return {
      req: {hourOffset: 0},
      error: '',
      loading: false,
      hourOffsetRange: Array.from(Array(240).keys()),
      QUALITIES_KEYS: Object.keys(QUALITIES),
      QUALITIES: QUALITIES,
      WAVE_HEIGHTS_KEYS: WAVE_HEIGHTS_KEYS,
      WAVE_HEIGHTS: WAVE_HEIGHTS
    };
  },
  methods: {
    hourOffsetStr: function(o){
      return o === 0 ? 'Now' : (o + (o > 1 ? ' hours ago': ' hour ago'));
    },
    clearImage: function(){
      this.req.imagepath = '';
    },
    imageSelected: function(event){
      var files = event.target.files;
      var file = files[0];
      this.req.imagepath = file;
    },
    submit: function(){
      if(this.req.imagepath){
        submitImage(this, this.req.imagepath, this.postSnapshot);
      }
      else {
        this.postSnapshot();
      }
    },
    postSnapshot: function(imagepath){
      this.req.imagepath = imagepath || '';
      let vm = this;
      this.fetch('POST', '/locations/' + this.location.id + '/snapshots', this.req).then(function(data){
        vm.snapshots.unshift(parseSnapshot(data.snapshot));
      });
    }
  }
});

//************************************************
Vue.component('snapshots', {
  template: '#snapshots',
  props: ['location'],
  data: function(){
    return {
      page: 0,
      isLastPage: false,
      snapshots: []
    };
  },
  created: function(){
    this.load();
  },
  methods: {
    load: function(){
      let url = this.location ? '/locations/' + this.location.id + '/snapshots' : '/snapshots';
      let vm = this;
      this.fetch('GET', url + '?page=' + (this.page + 1)).then(function(data){
        let snapshots = data.snapshots.rows;
        snapshots.forEach(function(snapshot){
          vm.snapshots.push(parseSnapshot(snapshot));
        });
        vm.page += 1;
        vm.isLastPage = snapshots.length < 10;
      });
    }
  }
});


//************************************************
Vue.component('snapshot', {
  template: '#snapshot',
  props: ['snapshot', 'snapshots'],
  data: function(){
    return {
      isUpdating: false,
      loading: false
    };
  },
  methods: {
    deleteSnapshot: function(){
      if(window.confirm('Are you sure you want to delete this snapshot?')){
        var vm = this;
        this.fetch('DELETE', '/snapshots/' + this.snapshot.id).then(function(){
          vm.snapshots.splice(vm.snapshots.indexOf(vm.snapshot), 1);
        });
      }
    }
  }
});


//************************************************
Vue.component('update-snapshot', {
  template: '#update-snapshot',
  props: ['snapshot'],
  data: function(){
    return {
      req: (({ quality, waveheight, text, imagepath }) => ({ quality, waveheight, text, imagepath }))(this.snapshot),
      error: '',
      loading: false,
      QUALITIES_KEYS: Object.keys(QUALITIES),
      QUALITIES: QUALITIES,
      WAVE_HEIGHTS_KEYS: WAVE_HEIGHTS_KEYS,
      WAVE_HEIGHTS: WAVE_HEIGHTS
    }
  },
  methods: {
    clearImage: function(){
      this.req.imagepath = '';
    },
    imageSelected: function(event){
      var files = event.target.files;
      var file = files[0];
      this.req.imagepath = file;
    },
    submit: function(){
      if(this.req.imagepath && this.req.imagepath !== this.snapshot.imagepath){
        submitImage(this, this.req.imagepath, this.updateSnapshot);
      }
      else {
        this.updateSnapshot(this.snapshot.imagepath);
      }
    },
    updateSnapshot: function(imagepath){
      this.req.imagepath = imagepath || ''; // `undefined` will omit imagepath from the post, so it won't get cleared.
      var vm = this;
      this.fetch('PUT', '/snapshots/' + this.snapshot.id, this.req).then(function(data){
        Object.assign(vm.snapshot, parseSnapshot(data.snapshot));
        vm.$emit('update-snapshot:close');
      });
    }
  }
});


//************************************************
Vue.component('my-snapshots', {
  template: '#my-snapshots'
});


//************************************************
/* Location */
Vue.component('location', {
  template: '#location',
  data: function(){
    let idStr = window.location.pathname.match(/([\d]+)/g);
    let id = idStr ? parseInt(idStr[0]) : null;
    return {
      locationId: id,
      location: null,
      buoys: [],
      loading: false
    };
  },
  methods: {
    screenWidth: function(){
      return window.screen.width;
    }
  },
  created: function(){
    var vm = this;
    if(!this.locationId){
      return;
    }
    this.fetch('GET', '/locations/' + this.locationId).then(function(data){
      vm.location = data.location;
      vm.$root.$emit('location:location', vm.location);
    });
    this.fetch('GET', '/locations/' + this.locationId + '/buoys')
      .then((data) => {
        this.buoys = data.buoys;
      }); 
  },
  destroyed: function(){
    this.$root.$emit('location:location', null);
  }
});


//************************************************
Vue.component('update-location', {
  template: '#update-location',
  props: ['location', 'buoys', 'snapshots'],
  data: function(){
    let req = Object.assign({}, this.location);
    req.buoys = this.joinBuoyIds();
    return {
      req: req,
      error: '',
      loading: false
    }
  },
  watch: {
    buoys: function(){
      this.req.buoys = this.joinBuoyIds();
    }
  },
  methods: {
    joinBuoyIds: function(){
      return this.buoys.map(buoy => buoy.buoyid).join(', ');
    },
    invalid: function(){
      return !this.req.name;
    },
    submit: function(){
      this.fetch('PUT', '/locations/' + this.location.id, this.req).then(function(data){
        return window.location.reload();
      });
    },
    deleteLocation: function(){
      if(window.confirm('Are you sure you want to delete this location?')){
        this.fetch('DELETE', '/locations/' + this.location.id).then(() => this.$goTo('/'));
      }
    }
  }
});


//************************************************
Vue.component('buoys', {
  template: '#buoys',
  data: function(){
    return {
      adding: false,
      buoys: []
    };
  },
  created: function(){
    this.fetch('GET', '/buoys').then(data => this.buoys = data.buoys);
  }
});

//************************************************
Vue.component('add-buoy', {
  template: '#add-buoy',
  data: function(){
    return {
      req: {},
      error: '',
      loading: false
    }
  },
  methods: {
    invalid: function(){
      return !this.req.buoyid;
    },
    submit: function(){
      this.fetch('POST', '/buoys', this.req).then(function(data){
        window.location.href = '/buoys/' + data.buoy.buoyid;
      });
    }
  }
});


//************************************************
Vue.component('buoy', {
  template: '#buoy',
  data: function(){
    let id = window.location.pathname.match(/buoys\/([\d]+)/g);
    id = id ? id[0].split('/')[1] : null;
    return {
      buoyId: id,
      buoy: null
    }
  },
  created: function(){
    var vm = this;
    this.fetch('GET', '/buoys/' + this.buoyId).then(function(data){
      vm.buoy = data.buoy;
      vm.$root.$emit('buoy:buoy', vm.buoy);
      vm.$forceUpdate();
    }, function(){
      vm.$root.$emit('buoy:buoy', null);
    });
  },
  destroyed: function(){
    this.$root.$emit('buoy:buoy', null);
  }
});

//************************************************
Vue.component('update-buoy', {
  template: '#update-buoy',
  props: ['buoy'],
  data: function(){
    return {
      name: this.buoy.name,
      error: '',
      loading: false
    }
  },
  methods: {
    invalid: function(){
      return !this.name;
    },
    submit: function(){
      let vm = this;
      this.fetch('PUT', '/buoys/' + this.buoy.buoyid, {name: this.name}).then(function(data){
        vm.buoy.name = vm.name;
        vm.$forceUpdate();
      });
    },
    deleteBuoy: function(){
      if(window.confirm('Are you sure you want to delete buoy #' + this.buoy.buoyid + '?')){
        this.fetch('DELETE', '/buoys/' + this.buoy.buoyid).then(() => this.$goTo('/buoys'))
      }
    }
  }
});


//************************************************
Vue.component('about', {
  template: '#about'
});


//************************************************
var app = new Vue({
  el: '#app',
  data: {
    currentPath: window.location.pathname,
    isMenuOpen: false,
    buoy: null, // custom header on buoy page
    location: null // custom header on location page
  },
  mounted: function(){
    let vm = this;

    // Modify `history.pushState` so we call a custom `window.onpushstate` fn
    // which we define here and accepts the same signature as window.onpopstate.
    // Both will set the currentPath variable.
    var pushState = window.history.pushState;
    window.history.pushState = function(state) {
      window.onpushstate({state: state}); // Call our custom function.
      return pushState.apply(history, arguments);
    };

    // Listen to our new `onpushstate` and the existing `onpopstate` event.
    window.onpopstate = window.onpushstate = function(event){
      console.log('state change', event.state);
      // Note: popstate event back to initial page has null state.
      vm.currentPath = event.state ? event.state.path : '/';

      window.scrollTo(0, 0); // scroll to top of the page as if you were loading page.
      vm.isMenuOpen = false; // close the header menu.
      
      vm.$forceUpdate();
    };

    // Helper for components.
    Vue.prototype.$goTo = function(path){
      window.history.pushState({ path: path }, '', path);
    };    

    // Hi-jack all anchor tag clicks.
    window.addEventListener('click', event => {
      // ensure we use the link, in case the click has been received by a subelement
      let { target } = event
      while (target && target.tagName !== 'A') target = target.parentNode;
      // handle only links that do not reference external resources
      if(target && target.matches("a:not([href*='://'])") && target.href){
        // some sanity checks taken from vue-router:
        // https://github.com/vuejs/vue-router/blob/dev/src/components/link.js#L106
        const { altKey, ctrlKey, metaKey, shiftKey, button, defaultPrevented } = event;
        if((metaKey || altKey || ctrlKey || shiftKey) || // don't handle with control keys
          defaultPrevented || // don't handle when preventDefault called
          (button !== undefined && button !== 0) // don't handle right clicks
        ){
          return;
        }

        // Auth0 urls.
        if(target.href.match('/login|/logout')){
          return;
        }

        // don't handle if `target="_blank"`
        if(target.getAttribute){
          const linkTarget = target.getAttribute('target')
          if(/\b_blank\b/i.test(linkTarget)){
            return;
          }
        }
        // don't handle same page links/anchors
        const url = new URL(target.href);
        const to = url.pathname
        if(window.location.pathname !== to && event.preventDefault){
          event.preventDefault()
          window.history.pushState({ path: to }, '', to);
        }
      }
    });

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
