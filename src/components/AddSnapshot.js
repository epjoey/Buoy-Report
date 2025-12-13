import { fetchJson } from '../utils/fetchUtils.js';
import { parseSnapshot, QUALITIES, submitImage, WAVE_HEIGHTS_KEYS, WAVE_HEIGHTS } from '../utils/snapshotUtils.js'

export default {
  name: 'AddSnapshot',
  template: '#add-snapshot',
  props: ['location', 'snapshots'],
  data: function(){
    return {
      req: {hourOffset: 0},
      error: '',
      loading: false,
      hourOffsetRange: Array.from(Array(240).keys()),
      QUALITIES_KEYS: Object.keys(QUALITIES),
      QUALITIES: QUALITIES,
      WAVE_HEIGHTS_KEYS: WAVE_HEIGHTS_KEYS,
      WAVE_HEIGHTS: WAVE_HEIGHTS
    };
  },
  methods: {
    hourOffsetStr: function(o){
      return o === 0 ? 'Now' : (o + (o > 1 ? ' hours ago': ' hour ago'));
    },
    clearImage: function(){
      this.req.imagepath = '';
    },
    imageSelected: function(event){
      var files = event.target.files;
      var file = files[0];
      this.req.imagepath = file;
    },
    submit: function(){
      if(this.req.imagepath){
        submitImage(this, this.req.imagepath, this.postSnapshot);
      }
      else {
        this.postSnapshot();
      }
    },
    postSnapshot: function(imagepath){
      this.req.imagepath = imagepath || '';
      let vm = this;
      fetchJson(vm, 'POST', '/locations/' + this.location.id + '/snapshots', this.req).then(function(data){
        vm.snapshots.unshift(parseSnapshot(data.snapshot));
      });
    }
  }
};