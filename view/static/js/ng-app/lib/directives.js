(function(){
  var directives = angular.module('app.directives', []);

  directives.directive('ngLocation', [
    function(){
      return {
        scope: true,
        controllerAs: 'locationCtrl',
        controller: ['$attrs', function($attrs){
          this.locationId = parseInt($attrs.ngLocation);
        }]
      };
    }
  ]);

  directives.directive('ngBuoy', [
    '$http', '$parse', 'urls',
    function($http, $parse, urls){
      var buoyCount = 0;
      var BUOY_WIDTH = 350;
      return {
        templateUrl: 'buoy.template',
        require: '^ngLocation',
        scope: true,
        controllerAs: 'buoyCtrl',
        controller: ['$scope', '$http', function($scope, $http){
          var ctrl = this;
          ctrl.saveSortOrder = function(){
            var buoyIds = [];
            $('[ng-buoy]').each(function(){
              buoyIds.push($(this).attr('ng-buoy'));
            });
            $http.post(urls.api.buoySort, {
              locationId: $scope.locationCtrl.locationId,
              buoyIds: buoyIds
            });
          };
        }],
        compile: function($el, $attrs){
          return function(scope, el, attrs){
            // Widen screen.
            var width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            buoyCount++;
            if(width > 768){
              $('.buoys').css({ width: BUOY_WIDTH * buoyCount });
            }

            scope.buoyId = attrs.ngBuoy;
            scope.buoyName = attrs.ngBuoyName;
            $http.get(urls.api.buoyData(scope.buoyId)).success(function(data){
              scope.data = data;
            });
          };
        }
      };
    }
  ]);

})();