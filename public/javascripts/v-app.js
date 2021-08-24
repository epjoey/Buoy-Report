Vue.prototype.$moment = window.moment;
Vue.prototype.$user = window.br.user;

const makeFetch = function(vm, url, method, data){
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
  return fetch('/api/' + url, config)
    .then(response => response.json())
    .then(function(data){
      vm.loading = false;
      if(data.error){
        throw data.error;
      }
      return data;
    })
    .catch(function(err){
      vm.error = err;
      vm.$forceUpdate();
    });
};

let http = {};
['POST', 'PUT', 'GET', 'DELETE'].forEach(method => {
  http[method] = (vm, url, data) => {
    return makeFetch(vm, url, method, data);
  };
});

const toggleFavorite = function(vm, location){
  var url = 'favorites/' + location.id;
  var isFavorite = location.$isFavorite;
  http[isFavorite ? 'DELETE' : 'POST'](vm, url, {}).then(function(data){
    location.$isFavorite = !isFavorite;
    vm.$forceUpdate();
  });
};

//************************************************
Vue.component('header-menu', {
  data: function(){
    return {
      isOpen: false
    };
  },
  template: '#header-menu'
});

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
      return _.filter(locations, '$isFavorite');
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
    http.GET(self, "locations").then(function(data){
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
      http.POST(this, 'locations', this.req).then(function(data){
        window.location.href = '/locations/' + data.locationId;
      });
    }
  }
});


//************************************************
Vue.component('location-header-link', {
  template: '#location-header-link',
  data: function(){
    return {
      location: window.br.location
    }
  },
  methods: {
    toggleFavorite: function(location){
      toggleFavorite(this, location);
    }
  }
});


//************************************************
// Buoy Readings.
var DIRECTIONS = {
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

var meters2Feet = function(meters){
  // Missing data in the Realtime files are denoted by "MM" (https://www.ndbc.noaa.gov/measdes.shtml#stdmet).
  if(meters === 'MM'){
    return 'MM';
  }
  return (parseFloat(meters) * 3.28084).toFixed(1);
};

var metersPerSec2mph = function(metersPerSec){
  if(metersPerSec === 'MM'){
    return 'MM';
  }
  return (parseFloat(metersPerSec) * 2.23694).toFixed(1); // meters/sec -> mph
};

var parseDateTime = function(year, month, day, hour, minute){
  return m = moment(year + "-" + month + "-" + day + "T" + hour + ":" + minute + ":00Z");
};

var parseSeconds = function(seconds){
  if(seconds === 'MM'){
    return 'MM';
  }
  return parseFloat(seconds).toFixed(1);
}

var parseDirection = function(bearing){
  if(bearing == 360 || bearing == 0){
    return 'N';
  }
  else {
    return _.find(_.keys(DIRECTIONS), function(key){
      var angles = DIRECTIONS[key];
      return bearing >= angles[0] && bearing < angles[1];
    });
  }
};

// Standard Wave Data from NOAA looks like:
// #YY  MM DD hh mm WDIR WSPD GST  WVHT   DPD   APD MWD   PRES  ATMP  WTMP  DEWP  VIS PTDY  TIDE
// #yr  mo dy hr mn degT m/s  m/s     m   sec   sec degT   hPa  degC  degC  degC  nmi  hPa    ft
// 2021 07 14 21 40 230  3.0  5.0    MM    MM    MM  MM 1015.6  12.7    MM  11.1   MM   MM    MM
// 2021 07 14 21 30 230  3.0  4.0    MM    MM    MM  MM 1015.6  12.7    MM  11.1   MM   MM    MM
// 2021 07 14 21 20 220  3.0  4.0    MM    MM    MM  MM 1015.6  12.6    MM  11.0   MM   MM    MM
var parseStandardData = function(dataRow){
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
};

// Spectral Wave Data from NOAA looks like:
// #YY  MM DD hh mm WVHT  SwH  SwP  WWH  WWP SwD WWD  STEEPNESS  APD MWD
// #yr  mo dy hr mn    m    m  sec    m  sec  -  degT     -      sec degT
// 2021 07 14 21 40  2.2  2.1  9.1  0.4  3.3  NW   W    AVERAGE  7.4 316
// 2021 07 14 20 40  2.1  2.1 10.0  0.4  3.3  NW WNW    AVERAGE  7.3 315
// 2021 07 14 19 40  2.0  2.0 10.0  0.4  4.0  NW   W    AVERAGE  7.4 312
var parseWaveData = function(dataRow){
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
};

var parseBuoyData = function(data, type){
  if(!data || !data.length){
    return [];
  }
  // Both data sets have 2 rows of units.
  if(data[0][0] === "#YY" && data[1][0] === "#yr"){
    data = data.slice(2);
  }
  return _.map(data, type === 'wave' ? parseWaveData : parseStandardData);
};

Vue.component('buoy-data', {
  template: '#buoy-data',
  props: ['buoy', 'type'],
  data: function(){
    return {
      tables: [],
      location: window.br.location // hack
    }
  },
  methods: {
    load: function(){
      const BUOY_DATA_ROWS_PER_TABLE = 24;
      let offset = this.tables.length * BUOY_DATA_ROWS_PER_TABLE;
      let url = 'buoys/' + this.buoy.buoyid + '/data?type=' + this.type + '&offset=' + offset;
      let vm = this;
      http.GET(vm, url).then(function(data){
        if(data.data){
          vm.tables.push({
            rows: parseBuoyData(data.data, vm.type)
          });
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
var parseSnapshotBuoyData = function(buoy){
  buoy.waveheight = meters2Feet(buoy.waveheight);
  buoy.swellheight = meters2Feet(buoy.swellheight);
  buoy.swellperiod = parseSeconds(buoy.swellperiod);
  buoy.windWaveSummary = meters2Feet(buoy.windwaveheight) + ' / ' + parseSeconds(buoy.windwaveperiod);
  return buoy;
};

var parseSnapshot = function(snapshot){
  snapshot.$waveHeight = snapshot.waveheight ? WAVE_HEIGHTS[snapshot.waveheight] : '';
  snapshot.$qualityText = snapshot.quality ? QUALITIES[snapshot.quality] : '';

  snapshot.$imagePath = snapshot.imagepath;
  if(snapshot.$imagePath && !snapshot.$imagePath.startsWith('http')){ // Super legacy
    snapshot.$imagePath = 'https://www.buoyreport.com/uploads/' + snapshot.imagepath;
  }
  else if(snapshot.$imagePath){ // Cloudinary
    snapshot.$imagePath = snapshot.$imagePath.replace("/image/upload/", "/image/upload/c_scale,w_680/");
  }

  snapshot.$buoyData = _.map(snapshot.buoyData, parseSnapshotBuoyData);
  snapshot.$by = snapshot.email ? snapshot.email.split('@')[0] : 0;
  return snapshot;
};

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
let WAVE_HEIGHTS_KEYS = _.sortBy(_.keys(WAVE_HEIGHTS), parseFloat);
WAVE_HEIGHTS_KEYS.unshift(null);

var QUALITIES = {
  1: 'Terrible',
  2: 'Mediocre',
  3: 'OK',
  4: 'Fun',
  5: 'Great'
};
let QUALITIES_KEYS = _.keys(QUALITIES);

let submitImage = function(vm, imagePath, onSuccess){
  vm.loading = true;
  var formData = new FormData();
  formData.append("file", imagePath);
  formData.append("upload_preset", window.br.NODE_ENV === 'production' ? 'buoyreport' : 'buoyreport_dev');
  formData.append("folder", window.br.NODE_ENV === 'production' ? 'buoyreport' : 'buoyreport_dev');
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
};

//************************************************
Vue.component('add-snapshot', {
  template: '#add-snapshot',
  props: ['location', 'snapshots'],
  data: function(){
    return {
      req: { hourOffset: 0 },
      error: '',
      loading: false,
      hourOffsetRange: _.range(0, 240),
      QUALITIES_KEYS: QUALITIES_KEYS,
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
      http.POST(vm, '/locations/' + this.location.id + '/snapshots', this.req).then(function(data){
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
      let url = this.location ? 'locations/' + this.location.id + '/snapshots' : 'snapshots';
      let vm = this;
      http.GET(vm, url + '?page=' + (this.page + 1)).then(function(data){
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
        http.DELETE(this, 'snapshots/' + this.snapshot.id).then(function(){
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
      req: _.pick(this.snapshot, ['quality', 'waveheight', 'text', 'imagepath']),
      error: '',
      loading: false,
      QUALITIES_KEYS: QUALITIES_KEYS,
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
      if(this.req.imagepath){
        submitImage(this, this.req.imagepath, this.updateSnapshot);
      }
      else {
        this.updateSnapshot();
      }
    },
    updateSnapshot: function(imagepath){
      this.req.imagepath = imagepath || ''; // `undefined` will omit imagepath from the post, so it won't get cleared.
      var vm = this;
      http.PUT(this, 'snapshots/' + this.snapshot.id, this.req).then(function(data){
        _.extend(vm.snapshot, parseSnapshot(data.snapshot));
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
    return {
      location: window.br.location,
      buoys: []
    };
  },
  methods: {
    screenWidth: function(){
      return window.screen.width;
    }
  },
  created: function(){
    var vm = this;
    http.GET(vm, 'locations/' + this.location.id + '/buoys').then(function(data){
      vm.buoys = data.buoys;
    });
  }  
});


//************************************************
Vue.component('update-location', {
  template: '#update-location',
  props: ['location', 'buoys', 'snapshots'],
  data: function(){
    return {
      req: _.clone(this.location),
      error: '',
      loading: false
    }
  },
  watch: {
    buoys: function(buoys){
      this.req.buoys = _.join(_.map(this.buoys, 'buoyid'), ', ');
      this.$forceUpdate();
    }
  },
  methods: {
    invalid: function(){
      return !this.req.name;
    },
    submit: function(){
      http.PUT(this, 'locations/' + this.location.id, this.req).then(function(data){
        return window.location.reload();
      });
    },
    deleteLocation: function(){
      if(window.confirm('Are you sure you want to delete this location?')){
        http.DELETE(this, 'locations/' + this.location.id).then(function(){
          window.location.href = '/';
        });
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
    var vm = this;
    http.GET(vm, "buoys").then(function(data){
      vm.buoys = data.buoys;
    });
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
      http.POST(this, 'buoys', this.req).then(function(data){
        window.location.href = '/buoys/' + data.buoy.buoyid;
      });
    }
  }
});


//************************************************
Vue.component('buoy', {
  template: '#buoy',
  data: function(){
    return {
      buoy: window.br.buoy // hack
    }
  }
});

//************************************************
Vue.component('update-buoy', {
  template: '#update-buoy',
  data: function(){
    return {
      name: window.br.buoy.name,
      error: '',
      loading: false,
      buoy: window.br.buoy // hack
    }
  },
  methods: {
    invalid: function(){
      return !this.name;
    },
    submit: function(){
      http.PUT(this, 'buoys/' + this.buoy.buoyid, {name: this.name}).then(function(data){
        return window.location.reload();
      });
    },
    deleteBuoy: function(){
      if(window.confirm('Are you sure you want to delete buoy #' + this.buoy.buoyid + '?')){
        http.DELETE(this, 'buoys/' + this.buoy.buoyid, {}).then(function(){
          window.location.href = '/buoys';
        });
      }
    }
  }
});


//************************************************
var app = new Vue({
  el: '#app',
  data: {
    isMenuOpen: false
  },
  methods: { 
    scrollTo: function(target){
      window.location.hash = target;
      document.getElementById(target).scrollIntoView();
    }    
  }
});
