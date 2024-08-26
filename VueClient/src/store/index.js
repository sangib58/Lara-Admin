import Vuex from 'vuex'
import Vue from 'vue'
import dashboard from './modules/dashboard.js'
import user from './modules/user'
import menu from './modules/menu'
import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

export default new Vuex.Store({
    modules:{
        dashboard,
        user,
        menu
    },
    plugins:[createPersistedState()],
});