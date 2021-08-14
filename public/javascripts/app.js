(function() {
  angular.module('app', ['directives', 'controllers'])

  .filter('map', function(){
    return _.map;
  })

  // Cloudinary url parameters.
  .filter('addImageParameters', function(){
    return function(url){
      if(url){
        url = url.replace("/image/upload/", "/image/upload/c_scale,w_680/");
      }
      return url;
    };
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
        scope.error = '';
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
    'http',
    function($rootScope, http){
      window.$$scope = function(element){
        return angular.element(element).scope();
      };

      $rootScope.moment = moment;
      $rootScope.screen = screen;
      $rootScope.location = location;

      $rootScope.toggleFavorite = function(location, $event){
        $event.stopPropagation();
        $event.preventDefault();
        var url = '/favorites/' + location.id;
        if(location.$isFavorite){
          http.delete($rootScope, url).then(function(res){
            location.$isFavorite = false;
          });
        }
        else {
          http.post($rootScope, url).then(function(res){
            location.$isFavorite = true;
          });
        }
      };

      $rootScope.scrollTo = function(target){
        document.getElementById(target).scrollIntoView();
      };
    }
  ]);
})();