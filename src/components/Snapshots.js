import { fetchJson } from '../utils/fetchUtils.js';
import { parseSnapshot } from '../utils/snapshotUtils.js'
import AddSnapshot from './AddSnapshot.js'
import Snapshot from './Snapshot.js'

export default {
  name: 'Snapshots',
  template: '#snapshots',
  components: {
    AddSnapshot,
    Snapshot,
  },
  props: ['location'],
  data: function(){
    return {
      page: 0,
      isLastPage: false,
      snapshots: []
    };
  },
  created: function(){
    this.load();
  },
  methods: {
    load: function(){
      let url = this.location ? '/locations/' + this.location.id + '/snapshots' : '/snapshots';
      let vm = this;
      fetchJson(this, 'GET', url + '?page=' + (this.page + 1)).then(function(data){
        let snapshots = data.snapshots.rows;
        snapshots.forEach(function(snapshot){
          const snap = parseSnapshot(snapshot);
          vm.snapshots.push(snap);
        });
        vm.page += 1;
        vm.isLastPage = snapshots.length < 10;
      });
    }
  }
};