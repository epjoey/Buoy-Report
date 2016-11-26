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
      var BUOY_WIDTH = 370;
      return {
        templateUrl: 'buoy.template',
        require: ['^ngLocation', 'ngBuoy'],
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

          ctrl.pages = [];
          ctrl.loadData = function(page){
            $http.get(urls.api.buoyData({
              buoyId:$scope.buoyId,
              offset:page.offset
            })).success(function(data){
              page.data = data;
              var lastRow = _.last(data);
              if(lastRow){
                page.lastIndex = lastRow.index;
              }
            });
          };
          ctrl.paginate = function(){
            var lastPage = _.last(ctrl.pages);
            var lastIndex = 0;
            if(lastPage){
              lastIndex = lastPage.lastIndex;
            }
            var nextPage = {data: null, offset: lastIndex};
            ctrl.pages.push(nextPage);
            ctrl.loadData(nextPage);
          };
        }],
        compile: function($el, $attrs){
          return function(scope, el, attrs, ctrls){
            var ctrl = ctrls[1];
            // Widen screen.
            var width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            buoyCount++;
            if(width > 768){
              $('.buoys').css({ width: BUOY_WIDTH * buoyCount });
            }

            scope.buoyId = attrs.ngBuoy;
            scope.buoyName = attrs.ngBuoyName;
            ctrl.paginate();
          };
        }
      };
    }
  ]);

})();