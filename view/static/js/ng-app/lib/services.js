(function() {
  var services = angular.module('app.services', []);
  services.factory('urls', [
    function(){
      return {
        noaaBuoy: function(buoyId){
          return 'http://www.ndbc.noaa.gov/station_page.php?station=' + buoyId;
        },
        api: {
          reportFormHandler: '/controllers/report/report-form-handler.php',
          buoySort: '/controllers/buoy/buoy-sort.php',
          buoyData: function(buoyId){
            return '/controllers/buoy/buoy.php?buoyid=' + buoyId;
          }
        }
      };
    }
  ]);

  services.run([
    '$rootScope', 'urls',
    function($rootScope, urls){
      $rootScope.urls = urls;
    }
  ]);
})();