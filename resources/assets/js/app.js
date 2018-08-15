require('./bootstrap');
window.Vue = require('vue');

import Vue from 'vue'
import VueRouter from 'vue-router'
import Snotify from 'vue-snotify'
import 'vue-snotify/styles/material.css'
import Vuelidate from 'vuelidate'

import VueGoodTable from 'vue-good-table';
// import the styles 
import 'vue-good-table/dist/vue-good-table.css'
Vue.use(VueGoodTable);

Vue.use(Vuelidate)
Vue.use(VueRouter)
Vue.use(Snotify)

let Navbar = require('./components/navbar.vue');
let Subnavbar = require('./components/subnavbar.vue'); 
let ViewDocuments = require('./components/document/index.vue');
let AddDocuments = require('./components/document/create.vue');
let EditDocuments = require('./components/document/edit.vue');
let AllDocuments = require('./components/document/AllDocuments.vue');

let DocumentTypes = require('./components/document_type/index.vue');
let AddDocumentTypes = require('./components/document_type/create.vue');
let EditDocumentTypes = require('./components/document_type/edit.vue');

let Route = require('./components/route/index.vue');
let Receive = require('./components/route/receive.vue');
let Release = require('./components/route/release.vue');

let ReleasedDocuments = require('./components/reports/ReleasedDocuments.vue');
let ReceivedDocuments = require('./components/reports/ReceivedDocuments.vue');
let UnactedDocuments = require('./components/reports/UnactedDocuments.vue');

let ChangePassword = require('./components/change_password.vue');

const routes = [
  { path: '/view-documents', component: ViewDocuments },
  { path: '/all-documents', component: AllDocuments },
  { path: '/add-documents', component: AddDocuments },
  { path: '/edit-document', component: EditDocuments },
  { path: '/document-types', component: DocumentTypes },
  { path: '/add-document-type', component: AddDocumentTypes },
  { path: '/edit-document-type', component: EditDocumentTypes },
  { path: '/view-routes', component: Route },
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
    components: {Navbar, Subnavbar},
    
});
