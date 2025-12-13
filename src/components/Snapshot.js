import { fetchJson } from '../utils/fetchUtils.js';
import UpdateSnapshot from './UpdateSnapshot.js'

export default {
  name: 'Snapshot',
  template: '#snapshot',
  components: {
    UpdateSnapshot
  },
  props: ['snapshot', 'snapshots'],
  data: function(){
    return {
      isUpdating: false,
      loading: false
    };
  },
  methods: {
    deleteSnapshot: function(){
      if(window.confirm('Are you sure you want to delete this snapshot?')){
        var vm = this;
        fetchJson(vm, 'DELETE', '/snapshots/' + this.snapshot.id).then(function(){
          vm.snapshots.splice(vm.snapshots.indexOf(vm.snapshot), 1);
        });
      }
    }
  }
};