(function() {
  angular.module('app', ['directives', 'controllers', 'filters'])

  .run([
    '$rootScope',
    function($rootScope){
      window.$$scope = function(element){
        return angular.element(element).scope();
      };

      $rootScope.moment = moment;
    }
  ]);
})();