import { fetchJson } from '../utils/fetchUtils.js';
import { parseBuoyData } from '../utils/snapshotUtils.js'

export default {
  name: 'BuoyData',
  template: '#buoy-data',
  props: ['buoy', 'type', 'location'],
  data: function(){
    return {
      tables: []
    }
  },
  methods: {
    load: function(){
      const BUOY_DATA_ROWS_PER_TABLE = 24;
      let offset = this.tables.length * BUOY_DATA_ROWS_PER_TABLE;
      let url = '/buoys/' + this.buoy.buoyid + '/data?type=' + this.type + '&offset=' + offset;
      let vm = this;
      fetchJson(this, 'GET', url).then(function(data){
        if(data.rows.length){
          vm.tables.push({
            rows: parseBuoyData(data.rows, vm.type)
          });
        }
        else {
          vm.$forceUpdate();
        }
      });
    },
    formatDate: function(time){
      time = time.clone();
      return time.tz(this.location && this.location.timezone || 'UTC').format('M/D h:mm a');
    }
  },
  created: function(){
    this.load();
  }
};