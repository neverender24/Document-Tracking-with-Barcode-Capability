require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
import locale from 'element-ui/lib/locale/lang/en'
import 'vue-search-select/dist/VueSearchSelect.css'
// import CKEditor from '@ckeditor/ckeditor5-vue';
import VueBarcode from '@xkeshi/vue-barcode';
import {
  store
} from './store/store'

import JsonExcel from "vue-json-excel";

Vue.component("downloadExcel", JsonExcel);

Vue.use(ElementUI, { locale })
Vue.use(VueRouter)
Vue.use(require('vue-moment'));
// Vue.use( CKEditor );
Vue.component(VueBarcode.name, VueBarcode);

Vue.config.productionTip = false

let Navbar = require('./components/navbar.vue');
let FastTrack = require('./components/fast_track.vue');
let QrTrack = require('./components/qr_track.vue');
let AllDocuments = require('./components/document/index.vue');

let Receive = require('./components/route/receive.vue');
let Release = require('./components/route/release.vue');

let ReleasedDocuments = require('./components/reports/ReleasedDocuments.vue');
let ReceivedDocuments = require('./components/reports/ReceivedDocuments.vue');
let UnactedDocuments = require('./components/reports/UnactedDocuments.vue');
let Summary = require('./components/reports/Summary.vue');

// Settings
let ChangePassword = require('./components/change_password.vue');
let GenerateBarcodes = require('./components/generate_barcodes.vue');
let AddNotes = require('./components/add_notes.vue');
let Users = require('./components/Users.vue');
let Office = require('./components/Office/Office.vue');

let Updates = require('./components/document/Updates.vue');

const routes = [
  {
    path: '/',
    component: AllDocuments
  },
  { path: '/all-documents', component: AllDocuments },
  { path: '/receive', component: Receive },
  { path: '/release', component: Release },
  { path: '/released-documents', component: ReleasedDocuments },
  { path: '/received-documents', component: ReceivedDocuments },
  { path: '/unacted-documents', component: UnactedDocuments },
  { path: '/change-password', component: ChangePassword },
  { path: '/add-notes', component: AddNotes },
  { path: '/generate-barcodes', component: GenerateBarcodes },
  { path: '/summary', component: Summary },
  { path: '/update-settings', component: Updates },
  { path: '/users', component: Users },
  { path: '/office', component: Office },
]

const router = new VueRouter({ 
  //mode: 'history',
  routes,
})

const app = new Vue({
    el: '#app',
    router,
    data: {
      list: {},
      document:{
        barcode:'',
        document_id: '',
        document_type_id: ''
      },
      user:{
        office_id: '',
        user_id: '',
        user_name: '',
        office_name: ''
      }
	  },
    components: {Navbar, FastTrack, QrTrack},
    store: store,
    
});
