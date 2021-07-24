(function() {
  angular.module('app', ['directives', 'controllers'])

  .run([
    '$rootScope',
    function($rootScope){
      window.$$scope = function(element){
        return angular.element(element).scope();
      };

      $rootScope.moment = moment;
      $rootScope.screen = screen;
    }
  ]);
})();