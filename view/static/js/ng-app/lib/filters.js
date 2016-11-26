(function(){
  var filters = angular.module('app.filters', []);
  filters.filter('unix2date', function(){
    return function(input){
      return moment.unix(input).format("M/D h:mm a");
    };
  });
  filters.filter('m2f', function(){
    return function(m){
      if(m == 'MM') return '-';
      return (m * 3.281).toFixed(1);
    };
  });
  filters.filter('clean', function(){
    return function(d){
      return d === 'MM' ? '-' : d;
    };
  });
  filters.filter('mps2kn', function(){
    return function(d){
      if(d == 'MM') return '-';
      return (d * 1.944).toFixed(1);
    };
  });

  filters.filter('dir2str', function(){
    return function(d){
      if(d === 'MM') return '';
      d = parseInt(d);
      return (
        (d < 11.25) ? 'N' :
        (d < 33.75) ? 'NNE' :
        (d < 56.25) ? 'NE' :
        (d < 78.75) ? 'ENE' :
        (d < 101.25) ? 'E' :
        (d < 123.75) ? 'ESE' :
        (d < 146.25) ? 'SE' :
        (d < 168.75) ? 'SSE' :
        (d < 191.25) ? 'S' :
        (d < 213.75) ? 'SSW' :
        (d < 236.25) ? 'SW' :
        (d < 258.75) ? 'WSW' :
        (d < 281.25) ? 'W' :
        (d < 303.75) ? 'WNW' :
        (d < 326.25) ? 'NW' :
        (d < 348.75) ? 'NNW' : 'N'
      );
    };
  });
})();