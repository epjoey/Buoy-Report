/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ (() => {

eval("Vue.prototype.$moment = window.moment;\nVue.prototype.$user = window.br.user;\nVue.prototype.$goTo = function(path){\n  window.history.pushState({ path: path }, '', path);\n};\n\nconst makeFetch = function(vm, method, url, data){\n  vm.loading = true;\n  let config = {\n    method: method,\n    headers: {\n      'Content-Type': 'application/json'\n    }\n  };\n  if(method === 'POST' || method === 'PUT'){\n    config.body = JSON.stringify(data);\n  }\n  return fetch('/api' + url, config)\n    .then(response => response.json())\n    .then(function(data){\n      vm.loading = false;\n      if(data.error){\n        throw data.error;\n      }\n      return data;\n    })\n    .catch(function(err){\n      vm.loading = false;\n      vm.error = err;\n      vm.$forceUpdate();\n    });\n};\n\nfunction capitalize(string){\n  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();\n};\n\n['POST', 'PUT', 'GET', 'DELETE'].forEach(method => {\n  Vue.prototype['$fetch' + capitalize(method)] = function(url, data){\n    return makeFetch(this, method, url, data);\n  };\n});\n\nconst toggleFavorite = function(vm, location){\n  var url = '/favorites/' + location.id;\n  var isFavorite = location.$isFavorite;\n  vm[isFavorite ? '$fetchDelete' : '$fetchPost'](url, {}).then(function(data){\n    location.$isFavorite = !isFavorite;\n    vm.$forceUpdate();\n  });\n};\n\n\n//************************************************\nVue.component('locations', {\n  template: '#locations',\n  data: function(){\n    return {\n      adding: false,\n      locations: [],\n      locationSearchText: ''\n    };\n  },\n  methods: {\n    isFavorite: function(locations){\n      return locations.filter(loc => loc.$isFavorite);\n    },\n    matchesSearchText: function(location){\n      return !this.locationSearchText.length ||\n        location.name.toLowerCase().includes(this.locationSearchText.toLowerCase());\n    },\n    toggleFavorite: function(location){\n      toggleFavorite(this, location);\n    }\n  },\n  created: function(){\n    var self = this;\n    this.$fetchGet(\"/locations\").then(function(data){\n      self.locations = data.locations;\n    });\n  }\n});\n\n\n//************************************************\nVue.component('add-location', {\n  template: '#add-location',\n  data: function(){\n    return {\n      req: {},\n      error: '',\n      loading: false\n    }\n  },\n  methods: {\n    invalid: function(){\n      return !this.req.name;\n    },\n    submit: function(){\n      this.$fetchPost('/locations', this.req)\n        .then(data => this.$goTo('/locations/' + data.locationId));\n    }\n  }\n});\n\n\n//************************************************\n// Buoy Readings.\nconst DIRECTIONS = {\n  'NNW': [337.5, 360],\n  'NNE': [0, 22.5],\n  'NE': [22.5, 67.5],\n  'E': [67.5, 112.5],\n  'SE': [112.5, 157.5],\n  'S': [157.5, 202.5],\n  'SW': [202.5, 247.5],\n  'W': [247.5, 292.5],\n  'NW': [292.5, 337.5]\n};\n\nconst meters2Feet = function(meters){\n  // Missing data in the Realtime files are denoted by \"MM\" (https://www.ndbc.noaa.gov/measdes.shtml#stdmet).\n  if(meters === 'MM'){\n    return 'MM';\n  }\n  return (parseFloat(meters) * 3.28084).toFixed(1);\n};\n\nconst metersPerSec2mph = function(metersPerSec){\n  if(metersPerSec === 'MM'){\n    return 'MM';\n  }\n  return (parseFloat(metersPerSec) * 2.23694).toFixed(1); // meters/sec -> mph\n};\n\nconst parseDateTime = function(year, month, day, hour, minute){\n  return moment(year + \"-\" + month + \"-\" + day + \"T\" + hour + \":\" + minute + \":00Z\");\n};\n\nconst parseSeconds = function(seconds){\n  if(seconds === 'MM'){\n    return 'MM';\n  }\n  return parseFloat(seconds).toFixed(1);\n}\n\nconst parseDirection = function(bearing){\n  if(bearing == 360 || bearing == 0){\n    return 'N';\n  }\n  else {\n    return Object.keys(DIRECTIONS).find(key => {\n      const angles = DIRECTIONS[key];\n      return bearing >= angles[0] && bearing < angles[1];\n    });\n  }\n};\n\n// Standard Wave Data from NOAA looks like:\n// #YY  MM DD hh mm WDIR WSPD GST  WVHT   DPD   APD MWD   PRES  ATMP  WTMP  DEWP  VIS PTDY  TIDE\n// #yr  mo dy hr mn degT m/s  m/s     m   sec   sec degT   hPa  degC  degC  degC  nmi  hPa    ft\n// 2021 07 14 21 40 230  3.0  5.0    MM    MM    MM  MM 1015.6  12.7    MM  11.1   MM   MM    MM\n// 2021 07 14 21 30 230  3.0  4.0    MM    MM    MM  MM 1015.6  12.7    MM  11.1   MM   MM    MM\n// 2021 07 14 21 20 220  3.0  4.0    MM    MM    MM  MM 1015.6  12.6    MM  11.0   MM   MM    MM\nconst parseStandardData = function(dataRow){\n  var row = {};\n  row.time = parseDateTime(dataRow[0], dataRow[1], dataRow[2], dataRow[3], dataRow[4]);\n  row.windDirection = dataRow[5];\n  row.windDirectionAbbr = parseDirection(row.windDirection);\n  row.windSpeed = metersPerSec2mph(dataRow[6]);\n  row.gust = metersPerSec2mph(dataRow[7]);\n  row.waveHeight = meters2Feet(dataRow[8]);\n  row.wavePeriod = parseSeconds(dataRow[9]);\n  row.waveDirection = dataRow[11];\n  row.waveDirectionAbbr = parseDirection(row.waveDirection);\n  return row;\n};\n\n// Spectral Wave Data from NOAA looks like:\n// #YY  MM DD hh mm WVHT  SwH  SwP  WWH  WWP SwD WWD  STEEPNESS  APD MWD\n// #yr  mo dy hr mn    m    m  sec    m  sec  -  degT     -      sec degT\n// 2021 07 14 21 40  2.2  2.1  9.1  0.4  3.3  NW   W    AVERAGE  7.4 316\n// 2021 07 14 20 40  2.1  2.1 10.0  0.4  3.3  NW WNW    AVERAGE  7.3 315\n// 2021 07 14 19 40  2.0  2.0 10.0  0.4  4.0  NW   W    AVERAGE  7.4 312\nconst parseWaveData = function(dataRow){\n  var row = {};\n  row.time = parseDateTime(dataRow[0], dataRow[1], dataRow[2], dataRow[3], dataRow[4]);\n  row.waveHeight = meters2Feet(dataRow[5]);\n  row.swellHeight = meters2Feet(dataRow[6]);\n  row.swellPeriod = parseSeconds(dataRow[7]);\n  row.meanWaveDirection = parseInt(dataRow[14]);\n  row.swellDirection = dataRow[10];\n  row.windWaveSummary = meters2Feet(dataRow[8]) + ' / ' + parseSeconds(dataRow[9]);\n  row.windWaveDirection = dataRow[11];\n  return row;\n};\n\nconst parseBuoyData = function(data, type){\n  if(!data || !data.length){\n    return [];\n  }\n  // Both data sets have 2 rows of units.\n  if(data[0][0] === \"#YY\" && data[1][0] === \"#yr\"){\n    data = data.slice(2);\n  }\n  return data.map(type === 'wave' ? parseWaveData : parseStandardData);\n};\n\nVue.component('buoy-data', {\n  template: '#buoy-data',\n  props: ['buoy', 'type', 'location'],\n  data: function(){\n    return {\n      tables: []\n    }\n  },\n  methods: {\n    load: function(){\n      const BUOY_DATA_ROWS_PER_TABLE = 24;\n      let offset = this.tables.length * BUOY_DATA_ROWS_PER_TABLE;\n      let url = '/buoys/' + this.buoy.buoyid + '/data?type=' + this.type + '&offset=' + offset;\n      let vm = this;\n      this.$fetchGet(url).then(function(data){\n        if(data.data){\n          vm.tables.push({\n            rows: parseBuoyData(data.data, vm.type)\n          });\n        }\n      });\n    },\n    formatDate: function(time){\n      time = time.clone();\n      return time.tz(this.location && this.location.timezone || 'UTC').format('M/D h:mm a');\n    }\n  },\n  created: function(){\n    this.load();\n  }\n});\n\n\n//************************************************\n/* Snapshots */\nvar parseSnapshotBuoyData = function(buoy){\n  buoy.waveheight = meters2Feet(buoy.waveheight);\n  buoy.swellheight = meters2Feet(buoy.swellheight);\n  buoy.swellperiod = parseSeconds(buoy.swellperiod);\n  buoy.windWaveSummary = meters2Feet(buoy.windwaveheight) + ' / ' + parseSeconds(buoy.windwaveperiod);\n  return buoy;\n};\n\nvar parseSnapshot = function(snapshot){\n  snapshot.$waveHeight = snapshot.waveheight ? WAVE_HEIGHTS[snapshot.waveheight] : '';\n  snapshot.$qualityText = snapshot.quality ? QUALITIES[snapshot.quality] : '';\n\n  snapshot.$imagePath = snapshot.imagepath;\n  if(snapshot.$imagePath && !snapshot.$imagePath.startsWith('http')){ // Super legacy\n    snapshot.$imagePath = 'https://www.buoyreport.com/uploads/' + snapshot.imagepath;\n  }\n  else if(snapshot.$imagePath){ // Cloudinary\n    snapshot.$imagePath = snapshot.$imagePath.replace(\"/image/upload/\", \"/image/upload/c_scale,w_680/\");\n  }\n\n  snapshot.$buoyData = snapshot.buoyData.map(parseSnapshotBuoyData);\n  snapshot.$by = snapshot.email ? snapshot.email.split('@')[0] : 0;\n  return snapshot;\n};\n\nconst WAVE_HEIGHTS = {\n  '1.5': \"1-2'\",\n  '2.5': \"2-3'\",\n  '3.5': \"3-4'\",\n  '5': \"4-6'\",\n  '7': \"6-8'\",\n  '9': \"8-10'\",\n  '11': \"10-12'\",\n  '13.5': \"12-15'\",\n  '17.5': \"15-20'\",\n  '25': \"20-30'\"\n};\nlet WAVE_HEIGHTS_KEYS = Object.keys(WAVE_HEIGHTS).sort((a, b) => parseFloat(a) - parseFloat(b));\n\nWAVE_HEIGHTS_KEYS.unshift(null);\n\nvar QUALITIES = {\n  1: 'Terrible',\n  2: 'Mediocre',\n  3: 'OK',\n  4: 'Fun',\n  5: 'Great'\n};\n\nlet submitImage = function(vm, imagePath, onSuccess){\n  vm.loading = true;\n  var formData = new FormData();\n  formData.append(\"file\", imagePath);\n  formData.append(\"upload_preset\", window.br.NODE_ENV === 'production' ? 'buoyreport' : 'buoyreport_dev');\n  formData.append(\"folder\", window.br.NODE_ENV === 'production' ? 'buoyreport' : 'buoyreport_dev');\n  var xhr = new XMLHttpRequest();\n  xhr.onload = function(){\n    vm.loading = false;\n    var response = JSON.parse(xhr.responseText);\n    if(response.secure_url){\n      onSuccess(response.secure_url);\n    }\n    else {\n      vm.error = 'Error uploading image';\n      console.warn(response);\n      vm.$forceUpdate();\n    }\n  };\n  xhr.open(\"post\", \"https://api.cloudinary.com/v1_1/duq2wnb9p/image/upload\");\n  xhr.send(formData);\n};\n\n//************************************************\nVue.component('add-snapshot', {\n  template: '#add-snapshot',\n  props: ['location', 'snapshots'],\n  data: function(){\n    return {\n      req: {hourOffset: 0},\n      error: '',\n      loading: false,\n      hourOffsetRange: Array.from(Array(240).keys()),\n      QUALITIES_KEYS: Object.keys(QUALITIES),\n      QUALITIES: QUALITIES,\n      WAVE_HEIGHTS_KEYS: WAVE_HEIGHTS_KEYS,\n      WAVE_HEIGHTS: WAVE_HEIGHTS\n    };\n  },\n  methods: {\n    hourOffsetStr: function(o){\n      return o === 0 ? 'Now' : (o + (o > 1 ? ' hours ago': ' hour ago'));\n    },\n    clearImage: function(){\n      this.req.imagepath = '';\n    },\n    imageSelected: function(event){\n      var files = event.target.files;\n      var file = files[0];\n      this.req.imagepath = file;\n    },\n    submit: function(){\n      if(this.req.imagepath){\n        submitImage(this, this.req.imagepath, this.postSnapshot);\n      }\n      else {\n        this.postSnapshot();\n      }\n    },\n    postSnapshot: function(imagepath){\n      this.req.imagepath = imagepath || '';\n      let vm = this;\n      this.$fetchPost('/locations/' + this.location.id + '/snapshots', this.req).then(function(data){\n        vm.snapshots.unshift(parseSnapshot(data.snapshot));\n      });\n    }\n  }\n});\n\n//************************************************\nVue.component('snapshots', {\n  template: '#snapshots',\n  props: ['location'],\n  data: function(){\n    return {\n      page: 0,\n      isLastPage: false,\n      snapshots: []\n    };\n  },\n  created: function(){\n    this.load();\n  },\n  methods: {\n    load: function(){\n      let url = this.location ? '/locations/' + this.location.id + '/snapshots' : '/snapshots';\n      let vm = this;\n      this.$fetchGet(url + '?page=' + (this.page + 1)).then(function(data){\n        let snapshots = data.snapshots.rows;\n        snapshots.forEach(function(snapshot){\n          vm.snapshots.push(parseSnapshot(snapshot));\n        });\n        vm.page += 1;\n        vm.isLastPage = snapshots.length < 10;\n      });\n    }\n  }\n});\n\n\n//************************************************\nVue.component('snapshot', {\n  template: '#snapshot',\n  props: ['snapshot', 'snapshots'],\n  data: function(){\n    return {\n      isUpdating: false,\n      loading: false\n    };\n  },\n  methods: {\n    deleteSnapshot: function(){\n      if(window.confirm('Are you sure you want to delete this snapshot?')){\n        var vm = this;\n        this.$fetchDelete('/snapshots/' + this.snapshot.id).then(function(){\n          vm.snapshots.splice(vm.snapshots.indexOf(vm.snapshot), 1);\n        });\n      }\n    }\n  }\n});\n\n\n//************************************************\nVue.component('update-snapshot', {\n  template: '#update-snapshot',\n  props: ['snapshot'],\n  data: function(){\n    return {\n      req: (({ quality, waveheight, text, imagepath }) => ({ quality, waveheight, text, imagepath }))(this.snapshot),\n      error: '',\n      loading: false,\n      QUALITIES_KEYS: Object.keys(QUALITIES),\n      QUALITIES: QUALITIES,\n      WAVE_HEIGHTS_KEYS: WAVE_HEIGHTS_KEYS,\n      WAVE_HEIGHTS: WAVE_HEIGHTS\n    }\n  },\n  methods: {\n    clearImage: function(){\n      this.req.imagepath = '';\n    },\n    imageSelected: function(event){\n      var files = event.target.files;\n      var file = files[0];\n      this.req.imagepath = file;\n    },\n    submit: function(){\n      if(this.req.imagepath){\n        submitImage(this, this.req.imagepath, this.updateSnapshot);\n      }\n      else {\n        this.updateSnapshot();\n      }\n    },\n    updateSnapshot: function(imagepath){\n      this.req.imagepath = imagepath || ''; // `undefined` will omit imagepath from the post, so it won't get cleared.\n      var vm = this;\n      this.$fetchPut('/snapshots/' + this.snapshot.id, this.req).then(function(data){\n        Object.assign(vm.snapshot, parseSnapshot(data.snapshot));\n        vm.$emit('update-snapshot:close');\n      });\n    }\n  }\n});\n\n\n//************************************************\nVue.component('my-snapshots', {\n  template: '#my-snapshots'\n});\n\n\n//************************************************\n/* Location */\nVue.component('location', {\n  template: '#location',\n  data: function(){\n    let idStr = window.location.pathname.match(/([\\d]+)/g);\n    let id = idStr ? parseInt(idStr[0]) : null;\n    return {\n      locationId: id,\n      location: null,\n      buoys: [],\n      loading: false\n    };\n  },\n  methods: {\n    screenWidth: function(){\n      return window.screen.width;\n    }\n  },\n  created: function(){\n    var vm = this;\n    if(!this.locationId){\n      return;\n    }\n    this.$fetchGet('/locations/' + this.locationId).then(function(data){\n      vm.location = data.location;\n      vm.$root.$emit('location', vm.location);\n    });\n    this.$fetchGet('/locations/' + this.locationId + '/buoys')\n      .then((data) => this.buoys = data.buoys);\n  },\n  destroyed: function(){\n    this.$root.$emit('location', null);\n  }\n});\n\n\n//************************************************\nVue.component('update-location', {\n  template: '#update-location',\n  props: ['location', 'buoys', 'snapshots'],\n  data: function(){\n    return {\n      req: Object.assign({}, this.location),\n      error: '',\n      loading: false\n    }\n  },\n  watch: {\n    buoys: function(buoys){\n      this.req.buoys = this.buoys.map(buoy => buoy.buoyid).join(', ');\n      this.$forceUpdate();\n    }\n  },\n  methods: {\n    invalid: function(){\n      return !this.req.name;\n    },\n    submit: function(){\n      this.$fetchPut('/locations/' + this.location.id, this.req).then(function(data){\n        return window.location.reload();\n      });\n    },\n    deleteLocation: function(){\n      if(window.confirm('Are you sure you want to delete this location?')){\n        this.$fetchDelete('/locations/' + this.location.id).then(() => this.$goTo('/'));\n      }\n    }\n  }\n});\n\n\n//************************************************\nVue.component('buoys', {\n  template: '#buoys',\n  data: function(){\n    return {\n      adding: false,\n      buoys: []\n    };\n  },\n  created: function(){\n    this.$fetchGet(\"/buoys\").then(data => this.buoys = data.buoys);\n  }\n});\n\n//************************************************\nVue.component('add-buoy', {\n  template: '#add-buoy',\n  data: function(){\n    return {\n      req: {},\n      error: '',\n      loading: false\n    }\n  },\n  methods: {\n    invalid: function(){\n      return !this.req.buoyid;\n    },\n    submit: function(){\n      this.$fetchPost('/buoys', this.req).then(function(data){\n        window.location.href = '/buoys/' + data.buoy.buoyid;\n      });\n    }\n  }\n});\n\n\n//************************************************\nVue.component('buoy', {\n  template: '#buoy',\n  data: function(){\n    return {\n      buoy: null\n    }\n  },\n  created: function(){\n    var vm = this;\n    this.$fetchGet(window.location.pathname).then(function(data){\n      vm.buoy = data.buoy;\n      vm.$root.$emit('buoy', vm.buoy);\n      vm.$forceUpdate();\n    }, function(){\n      vm.$root.$emit('buoy', null);\n    });\n  },\n  destroyed: function(){\n    this.$root.$emit('buoy', null);\n  }\n});\n\n//************************************************\nVue.component('update-buoy', {\n  template: '#update-buoy',\n  props: ['buoy'],\n  data: function(){\n    return {\n      name: this.buoy.name,\n      error: '',\n      loading: false\n    }\n  },\n  methods: {\n    invalid: function(){\n      return !this.name;\n    },\n    submit: function(){\n      let vm = this;\n      this.$fetchPut('/buoys/' + this.buoy.buoyid, {name: this.name}).then(function(data){\n        vm.buoy.name = vm.name;\n        vm.$forceUpdate();\n      });\n    },\n    deleteBuoy: function(){\n      if(window.confirm('Are you sure you want to delete buoy #' + this.buoy.buoyid + '?')){\n        this.$fetchDelete('/buoys/' + this.buoy.buoyid).then(() => this.$goTo('/buoys'))\n      }\n    }\n  }\n});\n\n\n//************************************************\nVue.component('about', {\n  template: '#about'\n});\n\n\n//************************************************\nvar app = new Vue({\n  el: '#app',\n  data: {\n    isMenuOpen: false,\n    path: window.location.pathname,\n    buoy: null, // custom header on buoy page\n    location: null // custom header on location page\n  },\n  mounted: function(){\n    let vm = this;\n\n    // Modify `history.pushState` so we call a custom `window.onpushstate` fn\n    // which we define *inside* of react controller and accepts the same\n    // signature as window.onpopstate. So both can set the react `state`.\n    var pushState = window.history.pushState;\n    window.history.pushState = function(state) {\n      window.onpushstate({state: state}); // Call our custom function.\n      return pushState.apply(history, arguments);\n    };\n\n    window.onpopstate = window.onpushstate = function(event){\n      console.log('state change', event.state);\n      // Popstate event back to initial page has null state.\n      var path = event.state ? event.state.path : '/';\n      vm.path = path;\n      window.scrollTo(0, 0);\n      vm.$forceUpdate();\n    };\n\n    window.addEventListener('click', event => {\n      // ensure we use the link, in case the click has been received by a subelement\n      let { target } = event\n      while (target && target.tagName !== 'A') target = target.parentNode\n      // handle only links that do not reference external resources\n      if(target && target.matches(\"a:not([href*='://'])\") && target.href){\n        // some sanity checks taken from vue-router:\n        // https://github.com/vuejs/vue-router/blob/dev/src/components/link.js#L106\n        const { altKey, ctrlKey, metaKey, shiftKey, button, defaultPrevented } = event;\n        if((metaKey || altKey || ctrlKey || shiftKey) || // don't handle with control keys\n          defaultPrevented || // don't handle when preventDefault called\n          (button !== undefined && button !== 0) // don't handle right clicks\n        ){\n          return;\n        }\n\n        // Auth0 urls.\n        if(target.href.match('/login|/logout')){\n          return;\n        }\n\n        // don't handle if `target=\"_blank\"`\n        if(target.getAttribute){\n          const linkTarget = target.getAttribute('target')\n          if(/\\b_blank\\b/i.test(linkTarget)){\n            return;\n          }\n        }\n        // don't handle same page links/anchors\n        const url = new URL(target.href);\n        const to = url.pathname\n        if(window.location.pathname !== to && event.preventDefault){\n          event.preventDefault()\n          vm.$goTo(to);\n        }\n      }\n    });\n\n    vm.$on('buoy', function(buoy){\n      this.buoy = buoy; // change header.\n    });\n    vm.$on('location', function(location){\n      this.location = location; // change header.\n    });\n  },\n  methods: {\n    toggleFavorite: function(location){\n      toggleFavorite(this, location);\n    },\n    scrollTo: function(target){\n      // window.location.hash = target;\n      document.getElementById(target).scrollIntoView();\n    }\n  }\n});\n\n\n//# sourceURL=webpack://buoy-report/./src/index.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/index.js"]();
/******/ 	
/******/ })()
;