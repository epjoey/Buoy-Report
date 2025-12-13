export const WAVE_HEIGHTS = {
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
let waveHeightKeys = Object.keys(WAVE_HEIGHTS).sort((a, b) => parseFloat(a) - parseFloat(b));
waveHeightKeys.unshift(null);
export const WAVE_HEIGHTS_KEYS = waveHeightKeys;

export const DIRECTIONS = {
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

export const QUALITIES = {
  1: 'Bad',
  2: 'OK',
  3: 'Fun',
  4: 'Great',
  5: 'Epic'
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

function parseSnapshotBuoyData(buoy){
  buoy.waveheight = meters2Feet(buoy.waveheight);
  buoy.swellheight = meters2Feet(buoy.swellheight);
  buoy.swellperiod = parseSeconds(buoy.swellperiod);
  buoy.windWaveSummary = meters2Feet(buoy.windwaveheight) + ' / ' + parseSeconds(buoy.windwaveperiod);
  return buoy;
}

export function parseSnapshot(snapshot){
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

//************************************************
// Buoy Readings.

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

export function parseBuoyData(rows, type){
  if(!rows || !rows.length){
    return [];
  }
  // Both data sets have 2 rows of units.
  if(rows[0][0] === "#YY" && rows[1][0] === "#yr"){
    rows = rows.slice(2);
  }
  return rows.map(type === 'wave' ? parseWaveData : parseStandardData);
}

export function submitImage(vm, imagePath, onSuccess){
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
