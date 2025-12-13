import { fetchJson } from '../utils/fetchUtils.js';
import AddBuoy from './AddBuoy.js';

export default {
  name: 'Buoys',
  template: '#buoys',
  components: {
    AddBuoy,
  },
  data: function(){
    return {
      adding: false,
      buoys: []
    };
  },
  created: function(){
    fetchJson(this, 'GET', '/buoys').then(data => this.buoys = data.buoys);
  }
}