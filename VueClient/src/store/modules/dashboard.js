import axios from 'axios'
import config from './../../config'

const state={
    status:'',
    token:localStorage.getItem('token')||'',
    user:{},
    confirmation:{},
    roleUserCount:[],
    loginDateData:[],
    loginMonthData:[],
    loginYearData:[],
    loginBrowserData:[],
    menus:[],
    activeUserStatus:{},
    appBarColor:'',
    footerColor:'',
    bgColor:'grey lighten-3',
    siteTitle:'',
    description:'',
    footerText:'',
    visible:false,
    overlay:false,
    loading:false,
    clientInfo:{}
};

const getters={
    isLoggedIn:state=>!!state.token,
    authStatus:state=>state.status,
    userInfo:state=>state.user,
    menus:state=>state.menus,
    roleUserCount:state=>state.roleUserCount,
    loginDateData:state=>state.loginDateData,
    loginMonthData:state=>state.loginMonthData,
    loginYearData:state=>state.loginYearData,
    loginBrowserData:state=>state.loginBrowserData,
    activeUserStatus:state=>state.activeUserStatus,
    appBarColor:state=>state.appBarColor,
    footerColor:state=>state.footerColor,
    bgColor:state=>state.bgColor,
    siteTitle:state=>state.siteTitle,
    description:state=>state.description,
    footerText:state=>state.footerText,
    visible:state=>state.visible,
    overlay:state=>state.overlay,
    loading:state=>state.loading,
    clientInfo:state=>state.clientInfo,
};

const actions={
    fetchSigninInfo({commit},credential){
         return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Users/GetLoginInfo',credential)
            .then(function(response){
            if(response.status==200){
                const token = 'Bearer '+response.data.return.token
                localStorage.setItem('token', token)
                localStorage.setItem('loggedUserId', response.data.return.obj.id)
                localStorage.setItem('fullName', response.data.return.obj.name)
                axios.defaults.headers.common['Authorization'] = token
                commit('auth_success', response.data.return)              
            }
            resolve(response)        
            })
            .catch(function (error) {
                commit('auth_error')
                localStorage.removeItem('token')
                localStorage.removeItem('loggedUserId')
                localStorage.removeItem('fullName')
                delete axios.defaults.headers.common['Authorization']
                reject(error)
            })
         })
    },
    signOut({commit}){
        return new Promise((resolve) => {                   
            commit('signOut')
            localStorage.removeItem('token')
            localStorage.removeItem('loggedUserId')
            localStorage.removeItem('fullName')        
            delete axios.defaults.headers.common['Authorization']
            resolve()
        })
    },
    fetchSettings({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/GeneralSettings')
            .then((response)=>{
                commit('setSettings',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchMenu({commit},info) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Menu/GetSidebarMenu/${info.roleId}`)
            .then((response)=>{
                commit('setMenu',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchStatus({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/UserStatus')
            .then((response)=>{
                commit('setActiveStatus',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createLogHistory({commit},objLogHistory) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Users/CreateLoginHistory',objLogHistory)
            .then((response)=>{
                if(response.status==200){
                    localStorage.setItem('logCode',response.data.return.log_code)
                    commit('logHistory',response.data.return)
                }             
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateLogHistory({commit},logCode) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateLoginHistory',logCode)
            .then((response)=>{
                commit('logHistory',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchRoleWiseUser({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/GetRoleWiseUser')
            .then((response)=>{
                commit('roleUserCount',response.data.return)              
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchDateLoginCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Users/GetLogInSummaryByDate/${id}`)
            .then((response)=>{
                commit('loginCountDate',response.data.return)              
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchMonthLoginCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Users/GetLogInSummaryByMonth/${id}`)
            .then((response)=>{
                commit('loginCountMonth',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchYearLoginCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Users/GetLogInSummaryByYear/${id}`)
            .then((response)=>{
                commit('loginCountYear',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchBrowserLoginCount({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Users/GetSummaryByBrowser/${id}`)
            .then((response)=>{
                commit('loginCountBrowser',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changeAppBarColor({commit},val){
        commit('setAppBarColor',val)
    },
    changeFooterColor({commit},val){
        commit('setFooterColor',val)
    },
    changeBgColor({commit},val){
        commit('setBgColor',val)
    },
    changeSiteTitle({commit},obj){       
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateSiteTitle',obj)
            .then((response)=>{
                commit('setSiteTitle',obj)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changeSiteDescription({commit},obj){
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateDescription',obj)
            .then((response)=>{
                commit('setSiteDescription',obj)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changeFooterText({commit},obj){      
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateFooter',obj)
            .then((response)=>{
                commit('setFooterText',obj)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changeVisibility({commit}){
        commit('setVisibility')
    },
    applyOverlay({commit}){
        commit('setOverlay')
    },
    clearOverlay({commit},chkPassword){
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Users/Unlock',chkPassword)
            .then((response)=>{
                if(response.status==200){
                    commit('resetOverlay')
                }             
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    applyLoading({commit}){
        commit('setLoading')
    },
    cancelLoading({commit}){
        commit('resetLoading')
    },
    fetchClientInfo({commit}){
        axios.get('https://api.ipify.org?format=json')
        .then((response)=>{
            const { detect } = require('detect-browser');
            const browser = detect();
            const data={
                userIp:response.data.ip,
                browserName:browser.name,
                browserVersion:browser.version,
                platform:browser.os,
            }
            commit('setClientInfo',data)         
        })
    }
};

const mutations ={
    auth_request:(state)=>{
        state.status='loading'
    },
    auth_success:(state, data)=>{
        state.status = 'success'
        state.token = data.token
        state.user = data.obj
    },
    auth_error:(state)=>{
        state.status = 'error'
    },
    signOut:(state)=>{
        state.status = ''
        state.token = ''
        state.visible=false
        state.overlay=false
        state.loading=false
    },
    setSettings:(state,data)=>{
        state.siteTitle=data.title
        state.description=data.description
        state.footerText=data.footer
    },
    setMenu:(state,menus)=>{
        state.menus=menus
    },
    setActiveStatus:(state,activeUserStatus)=>{
        state.activeUserStatus=activeUserStatus
    },
    logHistory:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    roleUserCount:(state,data)=>{
        state.roleUserCount=data
    },
    loginCountDate:(state,data)=>{
        state.loginDateData=data
    },
    loginCountMonth:(state,data)=>{
        state.loginMonthData=data
    },
    loginCountYear:(state,data)=>{
        state.loginYearData=data
    },
    loginCountBrowser:(state,data)=>{
        state.loginBrowserData=data
    },
    setAppBarColor:(state,val)=>{
        state.appBarColor=val
    },
    setFooterColor:(state,val)=>{
        state.footerColor=val
    },
    setBgColor:(state,val)=>{
        state.bgColor=val
    },
    setSiteTitle:(state,obj)=>{
        state.siteTitle=obj.title
    },
    setSiteDescription:(state,obj)=>{
        state.description=obj.description
    },
    setFooterText:(state,obj)=>{
        state.footerText=obj.footer
    },
    setVisibility:(state)=>{
        state.visible=true
    },
    setOverlay:(state)=>{
        state.visible=false
        state.overlay=true
    },
    resetOverlay:(state)=>{
        state.visible=true
        state.overlay=false
    },
    setLoading:(state)=>{
        state.loading=true
    },
    resetLoading:(state)=>{
        state.loading=false
    },
    setClientInfo:(state,data)=>{
        state.clientInfo=data
    },
};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}