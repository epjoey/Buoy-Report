import { fetchJson } from '../utils/fetchUtils.js';

export default {
  name: 'UpdateBuoy',
  template: '#update-buoy',
  props: ['buoy'],
  data: function(){
    return {
      name: this.buoy.name,
      error: '',
      loading: false
    }
  },
  methods: {
    invalid: function(){
      return !this.name;
    },
    submit: function(){
      let vm = this;
      fetchJson(this, 'PUT', '/buoys/' + this.buoy.buoyid, {name: this.name}).then(function(data){
        vm.buoy.name = vm.name;
        vm.$forceUpdate();
      });
    },
    deleteBuoy: function(){
      if(window.confirm('Are you sure you want to delete buoy #' + this.buoy.buoyid + '?')){
        fetchJson(this, 'DELETE', '/buoys/' + this.buoy.buoyid).then(() => this.$router.push('/buoys'))
      }
    }
  }
};