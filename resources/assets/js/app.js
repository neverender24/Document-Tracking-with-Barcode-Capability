require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuelidate from 'vuelidate'
import ElementUI from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
import locale from 'element-ui/lib/locale/lang/en'


Vue.use(ElementUI, { locale })
Vue.use(Vuelidate)
Vue.use(VueRouter)
Vue.use(require('vue-moment'));


let Navbar = require('./components/navbar.vue');
let FastTrack = require('./components/fast_track.vue');
let AllDocuments = require('./components/document/index.vue');

let Receive = require('./components/route/receive.vue');
let Release = require('./components/route/release.vue');

let ReleasedDocuments = require('./components/reports/ReleasedDocuments.vue');
let ReceivedDocuments = require('./components/reports/ReceivedDocuments.vue');
let UnactedDocuments = require('./components/reports/UnactedDocuments.vue');

let ChangePassword = require('./components/change_password.vue');

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
  { path: '/change-password', component: ChangePassword },
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
    components: {Navbar, FastTrack},
    
});
