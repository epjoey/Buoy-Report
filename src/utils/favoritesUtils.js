function toggleFavorite(vm, location){
  var url = '/favorites/' + location.id;
  var isFavorite = location.$isFavorite;
  vm.fetch(isFavorite ? 'DELETE' : 'POST', url, {}).then(function(data){
    location.$isFavorite = !isFavorite;
    vm.$forceUpdate();
  });
}

export {
	toggleFavorite
}