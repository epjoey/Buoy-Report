(()=>{function t(t,o){var e="/favorites/"+o.id,n=o.$isFavorite;t[n?"$fetchDelete":"$fetchPost"](e,{}).then((function(e){o.$isFavorite=!n,t.$forceUpdate()}))}["POST","PUT","GET","DELETE"].forEach((t=>{var o;Vue.prototype["$fetch"+(o=t,o.charAt(0).toUpperCase()+o.slice(1).toLowerCase())]=function(o,e){return function(t,o,e,n){t.loading=!0;let i={method:o,headers:{"Content-Type":"application/json"}};return"POST"!==o&&"PUT"!==o||(i.body=JSON.stringify(n)),fetch("/api"+e,i).then((t=>t.json())).then((function(o){if(t.loading=!1,o.error)throw o.error;return o})).catch((function(o){t.loading=!1,t.error=o,t.$forceUpdate()}))}(this,t,o,e)}})),Vue.component("locations",{template:"#locations",data:function(){return{adding:!1,locations:[],locationSearchText:""}},methods:{isFavorite:function(t){return t.filter((t=>t.$isFavorite))},matchesSearchText:function(t){return!this.locationSearchText.length||t.name.toLowerCase().includes(this.locationSearchText.toLowerCase())},toggleFavorite:function(o){t(this,o)}},created:function(){var t=this;this.$fetchGet("/locations").then((function(o){t.locations=o.locations}))}}),Vue.component("add-location",{template:"#add-location",data:function(){return{req:{},error:"",loading:!1}},methods:{invalid:function(){return!this.req.name},submit:function(){this.$fetchPost("/locations",this.req).then((t=>this.$goTo("/locations/"+t.locationId)))}}});const o={NNW:[337.5,360],NNE:[0,22.5],NE:[22.5,67.5],E:[67.5,112.5],SE:[112.5,157.5],S:[157.5,202.5],SW:[202.5,247.5],W:[247.5,292.5],NW:[292.5,337.5]};function e(t){return"MM"===t?"MM":(3.28084*parseFloat(t)).toFixed(1)}function n(t){return"MM"===t?"MM":(2.23694*parseFloat(t)).toFixed(1)}function i(t,o,e,n,i){return Vue.prototype.$moment(t+"-"+o+"-"+e+"T"+n+":"+i+":00Z")}function a(t){return"MM"===t?"MM":parseFloat(t).toFixed(1)}function s(t){return 360==t||0==t?"N":Object.keys(o).find((e=>{const n=o[e];return t>=n[0]&&t<n[1]}))}function r(t){var o={};return o.time=i(t[0],t[1],t[2],t[3],t[4]),o.windDirection=t[5],o.windDirectionAbbr=s(o.windDirection),o.windSpeed=n(t[6]),o.gust=n(t[7]),o.waveHeight=e(t[8]),o.wavePeriod=a(t[9]),o.waveDirection=t[11],o.waveDirectionAbbr=s(o.waveDirection),o}function u(t){var o={};return o.time=i(t[0],t[1],t[2],t[3],t[4]),o.waveHeight=e(t[5]),o.swellHeight=e(t[6]),o.swellPeriod=a(t[7]),o.meanWaveDirection=parseInt(t[14]),o.swellDirection=t[10],o.windWaveSummary=e(t[8])+" / "+a(t[9]),o.windWaveDirection=t[11],o}function h(t){return t.waveheight=e(t.waveheight),t.swellheight=e(t.swellheight),t.swellperiod=a(t.swellperiod),t.windWaveSummary=e(t.windwaveheight)+" / "+a(t.windwaveperiod),t}function c(t){return t.$waveHeight=t.waveheight?l[t.waveheight]:"",t.$qualityText=t.quality?d[t.quality]:"",t.$imagePath=t.imagepath,t.$imagePath&&!t.$imagePath.startsWith("http")?t.$imagePath="https://www.buoyreport.com/uploads/"+t.imagepath:t.$imagePath&&(t.$imagePath=t.$imagePath.replace("/image/upload/","/image/upload/c_scale,w_680/")),t.$buoyData=(t.buoyData||[]).map(h),t.$by=t.email?t.email.split("@")[0]:0,t}Vue.component("buoy-data",{template:"#buoy-data",props:["buoy","type","location"],data:function(){return{tables:[]}},methods:{load:function(){let t=24*this.tables.length,o="/buoys/"+this.buoy.buoyid+"/data?type="+this.type+"&offset="+t,e=this;this.$fetchGet(o).then((function(t){var o,n;t.rows.length&&e.tables.push({rows:(o=t.rows,n=e.type,o&&o.length?("#YY"===o[0][0]&&"#yr"===o[1][0]&&(o=o.slice(2)),o.map("wave"===n?u:r)):[])})}))},formatDate:function(t){return(t=t.clone()).tz(this.location&&this.location.timezone||"UTC").format("M/D h:mm a")}},created:function(){this.load()}});const l={1.5:"1-2'",2.5:"2-3'",3.5:"3-4'",5:"4-6'",7:"6-8'",9:"8-10'",11:"10-12'",13.5:"12-15'",17.5:"15-20'",25:"20-30'"};let p=Object.keys(l).sort(((t,o)=>parseFloat(t)-parseFloat(o)));p.unshift(null);var d={1:"Terrible",2:"Mediocre",3:"OK",4:"Fun",5:"Great"};function f(t,o,e){t.loading=!0;let n="production"===Vue.$nodeEnv?"buoyreport":"buoyreport_dev";"the devil's loophole"===t.location.name.toLowerCase()&&(n="art");var i=new FormData;i.append("file",o),i.append("upload_preset","production"===Vue.$nodeEnv?"buoyreport":"buoyreport_dev"),i.append("folder",n);var a=new XMLHttpRequest;a.onload=function(){t.loading=!1;var o=JSON.parse(a.responseText);o.secure_url?e(o.secure_url):(t.error="Error uploading image",console.warn(o),t.$forceUpdate())},a.open("post","https://api.cloudinary.com/v1_1/duq2wnb9p/image/upload"),a.send(i)}Vue.component("add-snapshot",{template:"#add-snapshot",props:["location","snapshots"],data:function(){return{req:{hourOffset:0},error:"",loading:!1,hourOffsetRange:Array.from(Array(240).keys()),QUALITIES_KEYS:Object.keys(d),QUALITIES:d,WAVE_HEIGHTS_KEYS:p,WAVE_HEIGHTS:l}},methods:{hourOffsetStr:function(t){return 0===t?"Now":t+(t>1?" hours ago":" hour ago")},clearImage:function(){this.req.imagepath=""},imageSelected:function(t){var o=t.target.files[0];this.req.imagepath=o},submit:function(){this.req.imagepath?f(this,this.req.imagepath,this.postSnapshot):this.postSnapshot()},postSnapshot:function(t){this.req.imagepath=t||"";let o=this;this.$fetchPost("/locations/"+this.location.id+"/snapshots",this.req).then((function(t){o.snapshots.unshift(c(t.snapshot))}))}}}),Vue.component("snapshots",{template:"#snapshots",props:["location"],data:function(){return{page:0,isLastPage:!1,snapshots:[]}},created:function(){this.load()},methods:{load:function(){let t=this.location?"/locations/"+this.location.id+"/snapshots":"/snapshots",o=this;this.$fetchGet(t+"?page="+(this.page+1)).then((function(t){let e=t.snapshots.rows;e.forEach((function(t){o.snapshots.push(c(t))})),o.page+=1,o.isLastPage=e.length<10}))}}}),Vue.component("snapshot",{template:"#snapshot",props:["snapshot","snapshots"],data:function(){return{isUpdating:!1,loading:!1}},methods:{deleteSnapshot:function(){if(window.confirm("Are you sure you want to delete this snapshot?")){var t=this;this.$fetchDelete("/snapshots/"+this.snapshot.id).then((function(){t.snapshots.splice(t.snapshots.indexOf(t.snapshot),1)}))}}}}),Vue.component("update-snapshot",{template:"#update-snapshot",props:["snapshot"],data:function(){return{req:(({quality:t,waveheight:o,text:e,imagepath:n})=>({quality:t,waveheight:o,text:e,imagepath:n}))(this.snapshot),error:"",loading:!1,QUALITIES_KEYS:Object.keys(d),QUALITIES:d,WAVE_HEIGHTS_KEYS:p,WAVE_HEIGHTS:l}},methods:{clearImage:function(){this.req.imagepath=""},imageSelected:function(t){var o=t.target.files[0];this.req.imagepath=o},submit:function(){this.req.imagepath?f(this,this.req.imagepath,this.updateSnapshot):this.updateSnapshot()},updateSnapshot:function(t){this.req.imagepath=t||"";var o=this;this.$fetchPut("/snapshots/"+this.snapshot.id,this.req).then((function(t){Object.assign(o.snapshot,c(t.snapshot)),o.$emit("update-snapshot:close")}))}}}),Vue.component("my-snapshots",{template:"#my-snapshots"}),Vue.component("location",{template:"#location",data:function(){let t=window.location.pathname.match(/([\d]+)/g);return{locationId:t?parseInt(t[0]):null,location:null,buoys:[],loading:!1}},methods:{screenWidth:function(){return window.screen.width}},created:function(){var t=this;this.locationId&&(this.$fetchGet("/locations/"+this.locationId).then((function(o){t.location=o.location,t.$root.$emit("location:location",t.location)})),this.$fetchGet("/locations/"+this.locationId+"/buoys").then((t=>{this.buoys=t.buoys})))},destroyed:function(){this.$root.$emit("location:location",null)}}),Vue.component("update-location",{template:"#update-location",props:["location","buoys","snapshots"],data:function(){let t=Object.assign({},this.location);return t.buoys=this.joinBuoyIds(),{req:t,error:"",loading:!1}},watch:{buoys:function(){this.req.buoys=this.joinBuoyIds()}},methods:{joinBuoyIds:function(){return this.buoys.map((t=>t.buoyid)).join(", ")},invalid:function(){return!this.req.name},submit:function(){this.$fetchPut("/locations/"+this.location.id,this.req).then((function(t){return window.location.reload()}))},deleteLocation:function(){window.confirm("Are you sure you want to delete this location?")&&this.$fetchDelete("/locations/"+this.location.id).then((()=>this.$goTo("/")))}}}),Vue.component("buoys",{template:"#buoys",data:function(){return{adding:!1,buoys:[]}},created:function(){this.$fetchGet("/buoys").then((t=>this.buoys=t.buoys))}}),Vue.component("add-buoy",{template:"#add-buoy",data:function(){return{req:{},error:"",loading:!1}},methods:{invalid:function(){return!this.req.buoyid},submit:function(){this.$fetchPost("/buoys",this.req).then((function(t){window.location.href="/buoys/"+t.buoy.buoyid}))}}}),Vue.component("buoy",{template:"#buoy",data:function(){let t=window.location.pathname.match(/buoys\/([\d]+)/g);return t=t?t[0].split("/")[1]:null,{buoyId:t,buoy:null}},created:function(){var t=this;this.$fetchGet("/buoys/"+this.buoyId).then((function(o){t.buoy=o.buoy,t.$root.$emit("buoy:buoy",t.buoy),t.$forceUpdate()}),(function(){t.$root.$emit("buoy:buoy",null)}))},destroyed:function(){this.$root.$emit("buoy:buoy",null)}}),Vue.component("update-buoy",{template:"#update-buoy",props:["buoy"],data:function(){return{name:this.buoy.name,error:"",loading:!1}},methods:{invalid:function(){return!this.name},submit:function(){let t=this;this.$fetchPut("/buoys/"+this.buoy.buoyid,{name:this.name}).then((function(o){t.buoy.name=t.name,t.$forceUpdate()}))},deleteBuoy:function(){window.confirm("Are you sure you want to delete buoy #"+this.buoy.buoyid+"?")&&this.$fetchDelete("/buoys/"+this.buoy.buoyid).then((()=>this.$goTo("/buoys")))}}}),Vue.component("about",{template:"#about"}),new Vue({el:"#app",data:{currentPath:window.location.pathname,isMenuOpen:!1,buoy:null,location:null},mounted:function(){let t=this;var o=window.history.pushState;window.history.pushState=function(t){return window.onpushstate({state:t}),o.apply(history,arguments)},window.onpopstate=window.onpushstate=function(o){console.log("state change",o.state),t.currentPath=o.state?o.state.path:"/",window.scrollTo(0,0),t.isMenuOpen=!1,t.$forceUpdate()},Vue.prototype.$goTo=function(t){window.history.pushState({path:t},"",t)},window.addEventListener("click",(t=>{let{target:o}=t;for(;o&&"A"!==o.tagName;)o=o.parentNode;if(o&&o.matches("a:not([href*='://'])")&&o.href){const{altKey:e,ctrlKey:n,metaKey:i,shiftKey:a,button:s,defaultPrevented:r}=t;if(i||e||n||a||r||void 0!==s&&0!==s)return;if(o.href.match("/login|/logout"))return;if(o.getAttribute){const t=o.getAttribute("target");if(/\b_blank\b/i.test(t))return}const u=new URL(o.href).pathname;window.location.pathname!==u&&t.preventDefault&&(t.preventDefault(),window.history.pushState({path:u},"",u))}})),t.$on("buoy:buoy",(t=>this.buoy=t)),t.$on("location:location",(t=>this.location=t))},methods:{toggleFavorite:function(o){t(this,o)},scrollTo:function(t){document.getElementById(t).scrollIntoView()}}})})();