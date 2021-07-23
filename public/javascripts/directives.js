(function(){

  var directives = angular.module('directives', []);

  var ROWS_PER_TABLE = 24;

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

  var directions = {
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
  var parseDirection = function(bearing){
    if(bearing == 360 || bearing == 0){
      return 'N';
    }
    else {
      return _.find(_.keys(directions), function(key){
        var angles = directions[key];
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
    row.swellHeight = meters2Feet(dataRow[6]);
    row.swellPeriod = parseSeconds(dataRow[7]);
    row.meanWaveDirection = parseInt(dataRow[14]);
    row.swellDirection = dataRow[10];
    row.windWaveSummary = meters2Feet(dataRow[8]) + ' / ' + parseSeconds(dataRow[9]);
    row.windWavePeriod = parseSeconds(dataRow[9]);
    row.windWaveDirection = dataRow[11];
    return row;
  };

  var parseData = function(data, rowParser){
    // Both data sets have 2 rows of units.
    if(data[0][0] === "#YY" && data[1][0] === "#yr"){
      data = data.slice(2);
    }
    return _.map(data, rowParser);
  };

  directives.directive('ngBuoyWaveData', [
    function(){
      return {
        scope: true,
        controllerAs: 'buoyCtrl',
        controller: ['$scope', '$http', '$attrs', function($scope, $http, $attrs){
          var ctrl = this;

          ctrl.buoyId = parseInt($attrs.ngBuoyWaveData);
          ctrl.tables = [];

          ctrl.load = function(){
            var offset = ctrl.tables.length * ROWS_PER_TABLE;
            var url = '/buoys/' + ctrl.buoyId + '/wave?offset=' + offset;
            ctrl.isLoading = true;
            $http.get(url).success(function(res){
              ctrl.isLoading = false;
              if(res.data){
                ctrl.tables.push({
                  rows: parseData(res.data, parseWaveData)
                });
              }
              if(res.error){
                console.error('error loading wave data for buoy ' + ctrl.buoyId + ': ' + res.error);
              }
            }).error(function(res){
              ctrl.isLoading = false;
            });
          };

          ctrl.load();
        }]
      };
    }
  ]);

  directives.directive('ngBuoyStandardData', [
    function(){
      return {
        scope: true,
        controllerAs: 'buoyCtrl',
        controller: ['$scope', '$http', '$attrs', function($scope, $http, $attrs){
          var ctrl = this;

          ctrl.buoyId = parseInt($attrs.ngBuoyStandardData);
          ctrl.tables = [];

          ctrl.load = function(){
            var offset = ctrl.tables.length * ROWS_PER_TABLE;
            var url = '/buoys/' + ctrl.buoyId + '/standard?offset=' + offset;
            $http.get(url).success(function(res){
              ctrl.isLoading = false;
              if(res.data){
                ctrl.tables.push({
                  rows: parseData(res.data, parseStandardData)
                });
              }
              if(res.error){
                console.error('error loading standard data for buoy ' + ctrl.buoyId + ': ' + res.error);
              }
            }).error(function(res){
              ctrl.isLoading = false;
            });
          };

          ctrl.load();
        }]
      };
    }
  ]);

  directives.directive('ngAddLocation', [
    '$http',
    function($http){
      return {
        scope: true,
        link: function($scope, $el, $attrs){
          $scope.req = {};
          $scope.submit = function(){
            $scope.loading = true;
            $http.post('/locations', $scope.req).success(function(res){
              $scope.loading = false;
              $scope.error = res.error;
              if(res.location){
                window.location.href = '/locations/' + res.locationId;
              }
            }).error(function(res){
              $scope.loading = false;
              $scope.error = 'Error adding location';
            });
          };
        }
      };
    }
  ]);


  directives.directive('ngUpdateLocation', [
    '$http',
    function($http){
      return {
        scope: true,
        link: function($scope, $el, $attrs){
          var location = JSON.parse($attrs.ngUpdateLocation);
          var buoys = JSON.parse($attrs.ngUpdateLocationBuoys);
          $scope.req = _.clone(location);
          $scope.req.buoys = _.join(_.map(buoys, 'buoyid'), ', ');
          $scope.submit = function(){
            $scope.loading = true;
            $http.put('/locations/' + location.id, $scope.req).success(function(res){
              $scope.loading = false;
              $scope.error = res.error;
              if(!$scope.error){
                window.location.reload();
              }
            }).error(function(res){
              $scope.loading = false;
              $scope.error = 'Error updating location';
            });
          };

          $scope.deleteLocation = function(){
            if(window.confirm('Are you sure you want to delete ' + location.name + '?')){
              $http.delete('/locations/' + location.id).success(function(res){
                $scope.loading = false;
                $scope.error = res.error;
                if(res.success){
                  window.location.href = '/';
                }
              }).error(function(res){
                $scope.loading = false;
                $scope.error = 'Error deleting location';
              });
            }
          };
        }
      };
    }
  ]);


  // Snapshots.
  // `snapshot.waveheight` is stored as an number representing a range.
  // So 17.5 means the waves were 15-20' (hawaiian).
  var WAVE_HEIGHTS = {
    1.5: "1-2'",
    2.5: "2-3'",
    3.5: "3-4'",
    5: "4-6'",
    7: "6-8'",
    9: "8-10'",
    11: "10-12'",
    13.5: "12-15'",
    17.5: "15-20'",
    25: "20-30'"
  };

  var QUALITIES = {
    1: 'Terrible',
    2: 'Mediocre',
    3: 'OK',
    4: 'Fun',
    5: 'Great'
  };

  var parseSnapshotBuoyData = function(buoy){
    buoy.swellheight = meters2Feet(buoy.swellheight);
    return buoy;
  };

  var parseSnapshot = function(snapshot){
    snapshot.waveheight = snapshot.waveheight ? WAVE_HEIGHTS[snapshot.waveheight] : '';
    snapshot.qualityText = snapshot.quality ? QUALITIES[snapshot.quality] : '';
    snapshot.imagepath = snapshot.imagepath && !snapshot.imagepath.startsWith('http') ?
        'https://www.buoyreport.com/uploads/' + snapshot.imagepath : snapshot.imagepath;
    snapshot.buoyData = _.map(snapshot.buoyData, parseSnapshotBuoyData);
    return snapshot;
  };


  directives.directive('ngSnapshots', [
    '$http',
    function($http){
      return {
        scope: true,
        link: function($scope, $el, $attrs){
          var locationId = $attrs.ngSnapshots;
          $scope.page = 0;
          $scope.snapshots = [];
          var url = (locationId ? '/locations/' + locationId : '') + '/snapshots?page=';
          $scope.load = function(){
            $scope.loading = true;
            $http.get(url + ($scope.page + 1)).success(function(res){
              $scope.loading = false;
              $scope.snapshots = _.concat($scope.snapshots, _.map(res.snapshots.rows, parseSnapshot));
              $scope.page += 1;
              $scope.isLastPage = res.snapshots.rows.length < 10;
            }).error(function(res){
              $scope.loading = false;
            });
          };
          // Location snapshots feed starts off blank.
          if(!locationId){
            $scope.load();
          }
        }
      };
    }
  ]);
})();
