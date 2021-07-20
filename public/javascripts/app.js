(function() {
  angular.module('app', ['directives', 'controllers'])

  .filter('snapshotWaveHeight', function(){
    // `snapshot.waveheight` is stored as an number representing a range.
    // So 17.5 means the waves were 15-20.
    var map = {
      1.5: "1-2'",
      2.5: "2-3'",
      3.5: "3-4'",
      5: "4-6'",
      7: "6-8'",
      9: "8-10'",
      11: "10-12'",
      13.5: "12-15'",
      17.5: "15-20'",
      25: "20-30'"
    };
    return function(waveHeight){
      return map[waveHeight];
    }
  })

  .filter('snapshotImagePath', function(){
    return function(path){
      if(path && !path.startsWith('http')){
        return 'https://www.buoyreport.com/uploads/' + path;
      }
      return path;
    }
  })

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