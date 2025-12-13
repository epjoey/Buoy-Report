import { fetchJson } from '../utils/fetchUtils.js';

//************************************************
export default {
  name: 'AddLocation',
  template: '#add-location',
  data: function(){
    return {
      req: {},
      error: '',
      loading: false
    }
  },
  methods: {
    invalid: function(){
      return !this.req.name;
    },
    submit: function(){
      fetchJson(this, 'POST', '/locations', this.req)
        .then(data => this.$router.push('/locations/' + data.locationId));
    }
  }
};