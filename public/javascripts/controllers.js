(function() {
  var controllers = angular.module('controllers', []);

  controllers.controller('LocationsCtrl', [
    'http',
    '$scope',
    function(http, $scope){
      $scope.locationSearchText = '';
      $scope.addLocation = {isOpen: false};
      $scope.locationMatchesSearch = function(locationName){
        if(!$scope.locationSearchText.length){
          return true;
        }
        var search = $scope.locationSearchText.toLowerCase();
        return locationName.toLowerCase().includes(search);
      };

      $scope.toggleFavorite = function(location, $event){
        $event.stopPropagation();
        $event.preventDefault();
        var url = '/favorites/' + location.id;
        if(location.$isFavorite){
          http.delete($scope, url).then(function(res){
            location.$isFavorite = false;
          });
        }
        else {
          http.post($scope, url).then(function(res){
            location.$isFavorite = true;
          });
        }
      };

      http.get($scope, '/locations').then(function(res){
        $scope.locations = res.data.locations;
      });
    }
  ]);

})();