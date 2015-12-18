var app = angular.module("buoyReport", []);
app.directive("buoyObservations", ["$http", function($http) {
  return {
    templateUrl: "/view/templates/partials/buoy-observations.html",
    scope: {
      stationId: "=",
    },
    link: function(scope, el, attrs) {
      console.log(scope.stationId)
      $http.get("/controllers/buoy/buoy-observations.php?stationid="+scope.stationId).success(function(d) {
        console.log(d)
      })
    }
  }
}]);