(function() {
  var controllers = angular.module('app.controllers', []);
  controllers.controller('SnapshotFormCtrl', [
    '$scope', 'urls', '$http',
    function($scope, urls, $http){
      $scope.post = {};

      $scope.closeForm = function(){
        $scope.isFormOpen = false;
      };
      $scope.toggleForm = function(){
        $scope.isFormOpen = !$scope.isFormOpen;
      };
    }
  ]);
})();