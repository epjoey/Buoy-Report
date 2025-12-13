import { fetchJson } from '../utils/fetchUtils.js';
import UpdateBuoy from './UpdateBuoy.js'
import BuoyData from './BuoyData.js'

export default {
  name: 'Buoy',
  template: '#buoy',
  components: {
    UpdateBuoy,
    BuoyData,
  },
  data: function(){
    let id = window.location.pathname.match(/buoys\/([\d]+)/g);
    id = id ? id[0].split('/')[1] : null;
    return {
      buoyId: id,
      buoy: null
    }
  },
  created: function(){
    var vm = this;
    fetchJson(vm, 'GET', '/buoys/' + this.buoyId).then(function(data){
      vm.buoy = data.buoy;
      vm.$root.$emit('buoy:buoy', vm.buoy);
      vm.$forceUpdate();
    }, function(){
      vm.$root.$emit('buoy:buoy', null);
    });
  },
  destroyed: function(){
    this.$root.$emit('buoy:buoy', null);
  }
};
