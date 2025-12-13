import { fetchJson } from '../utils/fetchUtils.js';
import { parseSnapshot, QUALITIES, submitImage, WAVE_HEIGHTS_KEYS, WAVE_HEIGHTS } from '../utils/snapshotUtils.js'

export default {
  name: 'UpdateSnapshot',
  template: '#update-snapshot',
  props: ['snapshot'],
  data: function(){
    return {
      req: (({ quality, waveheight, text, imagepath }) => ({ quality, waveheight, text, imagepath }))(this.snapshot),
      error: '',
      loading: false,
      QUALITIES_KEYS: Object.keys(QUALITIES),
      QUALITIES: QUALITIES,
      WAVE_HEIGHTS_KEYS: WAVE_HEIGHTS_KEYS,
      WAVE_HEIGHTS: WAVE_HEIGHTS
    }
  },
  methods: {
    clearImage: function(){
      this.req.imagepath = '';
    },
    imageSelected: function(event){
      var files = event.target.files;
      var file = files[0];
      this.req.imagepath = file;
    },
    submit: function(){
      if(this.req.imagepath && this.req.imagepath !== this.snapshot.imagepath){
        submitImage(this, this.req.imagepath, this.updateSnapshot);
      }
      else {
        this.updateSnapshot(this.snapshot.imagepath);
      }
    },
    updateSnapshot: function(imagepath){
      this.req.imagepath = imagepath || ''; // `undefined` will omit imagepath from the post, so it won't get cleared.
      var vm = this;
      fetchJson(this, 'PUT', '/snapshots/' + this.snapshot.id, this.req).then(function(data){
        Object.assign(vm.snapshot, parseSnapshot(data.snapshot));
        vm.$emit('update-snapshot:close');
      });
    }
  }
};