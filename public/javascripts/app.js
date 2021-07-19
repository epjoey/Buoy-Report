(function() {
  var application = angular.module('app', [ 'app.directives', 'app.controllers' ]);

  application.run([
    '$rootScope',
    function($rootScope){
      window.$$scope = function(element){
        return angular.element(element).scope();
      };

      $rootScope.moment = moment;
    }
  ]);
})();