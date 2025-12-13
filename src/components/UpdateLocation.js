import { fetchJson } from '../utils/fetchUtils.js';

export default {
  name: 'UpdateLocation',
  template: '#update-location',
  props: ['location', 'buoys', 'snapshots'],
  data: function(){
    let req = Object.assign({}, this.location);
    req.buoys = this.joinBuoyIds();
    return {
      req: req,
      error: '',
      loading: false
    }
  },
  watch: {
    buoys: function(){
      this.req.buoys = this.joinBuoyIds();
    }
  },
  methods: {
    joinBuoyIds: function(){
      return this.buoys.map(buoy => buoy.buoyid).join(', ');
    },
    invalid: function(){
      return !this.req.name;
    },
    submit: function(){
      fetchJson(this, 'PUT', '/locations/' + this.location.id, this.req).then(function(data){
        return window.location.reload();
      });
    },
    deleteLocation: function(){
      if(window.confirm('Are you sure you want to delete this location?')){
        fetchJson(this, 'DELETE', '/locations/' + this.location.id).then(() => this.$router.push('/'));
      }
    }
  }
};