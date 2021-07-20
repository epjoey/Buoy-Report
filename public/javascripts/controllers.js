(function() {
  var controllers = angular.module('controllers', []);

  controllers.controller('LocationsCtrl', [
    '$scope',
    function($scope){
      $scope.locationSearchText = '';
      $scope.locationMatchesSearch = function(locationName){
        if(!$scope.locationSearchText.length){
          return true;
        }
        var search = $scope.locationSearchText.toLowerCase();
        return locationName.toLowerCase().includes(search);
      }
    }
  ]);

  
})();