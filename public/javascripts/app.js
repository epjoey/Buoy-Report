(function() {
  angular.module('app', ['directives', 'controllers'])

  .filter('map', function(){
    return _.map;
  })

  .factory('http', ['$http', '$q', function($http, $q){
    var req = function(scope, method, url, data){
      var deferred = $q.defer();
      var _error = function(res){
        console.log('Error:', url, res.statusText, res);
        scope.loading = false;
        scope.error = _.get(res, 'data.error') || 'Something went wrong';
        return deferred.reject(res);
      };
      scope.loading = true;
      $http({
        method: method,
        url: url,
        data: data
      }).then(function(res){
        scope.loading = false;
        if(res.data.error){
          _error(res);
          return deferred.reject(res);
        }
        else {
          return deferred.resolve(res);
        }
      }, _error);
      return deferred.promise;
    };

    var http = {
      get: function(scope, url){
        return req(scope, 'GET', url, null);
      },
      post: function(scope, url, data){
        return req(scope, 'POST', url, data);
      },
      put: function(scope, url, data){
        return req(scope, 'PUT', url, data);
      },
      delete: function(scope, url, data){
        return req(scope, 'DELETE', url, data);
      }
    };
    return http;
  }])

  .run([
    '$rootScope',
    function($rootScope){
      window.$$scope = function(element){
        return angular.element(element).scope();
      };

      $rootScope.moment = moment;
      $rootScope.screen = screen;
      $rootScope.location = location;
    }
  ]);
})();