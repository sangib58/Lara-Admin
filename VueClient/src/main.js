import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import store from './store/index'
import axios from 'axios'
import VueExcelXlsx from 'vue-excel-xlsx'
import Fullscreen from 'vue-fullscreen'

Vue.use(VueExcelXlsx)
Vue.use(Fullscreen)
Vue.config.productionTip = false;
Vue.prototype.$http=axios;

const token=localStorage.getItem('token');
if(token){
  Vue.prototype.$http.defaults.headers.common['Authorization'] =token
}

new Vue({
  store,
  vuetify,
  router,
  render: h => h(App)
}).$mount('#app')
