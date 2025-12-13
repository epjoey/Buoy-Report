export function fetchJson(vm, method, url, data){
  vm.loading = true;
  let config = {
    method: method,
    headers: {
      'Content-Type': 'application/json'
    }
  };
  if(method === 'POST' || method === 'PUT'){
    config.body = JSON.stringify(data);
  }
  return fetch('/api' + url, config)
    .then(response => response.json())
    .then(function(data){
      vm.loading = false;
      if(data.error){
        throw data.error;
      }
      return data;
    })
    .catch(function(err){
      vm.loading = false;
      vm.error = err;
      vm.$forceUpdate();
    });
};