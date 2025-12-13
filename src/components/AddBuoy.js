import { fetchJson } from '../utils/fetchUtils.js';

export default {
  name: 'AddBuoy',
  template: '#add-buoy',
  data: function(){
    return {
      req: {},
      error: '',
      loading: false
    }
  },
  methods: {
    invalid: function(){
      return !this.req.buoyid;
    },
    submit: function(){
      fetchJson(this, 'POST', '/buoys', this.req).then(function(data){
        window.location.href = '/buoys/' + data.buoy.buoyid;
      });
    }
  }
};