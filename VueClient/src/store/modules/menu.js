import axios from 'axios'
import config from './../../config'

const state={
    menuGroup:[],
    appMenu:[],
    parentMenu:[],
    confirmation:{}
};
const getters={
    menuList:state=>state.appMenu,
    parentMenu:state=>state.parentMenu,
    menuGroupList:state=>state.menuGroup,
    confirmation:state=>state.confirmation 
};
const actions={
    fetchMenu({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Menu/GetMenuList')
            .then((response)=>{
                commit('setMenu',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchParentMenu({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Menu/GetParentMenuList')
            .then((response)=>{
                commit('setParentMenu',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createMenu({commit},objMenu) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Menu/CreateMenu',objMenu)
            .then((response)=>{
                commit('newMenu',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateMenu({commit},objMenu) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Menu/UpdateMenu',objMenu)
            .then((response)=>{
                commit('editMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteMenu({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Menu/DeleteSingleMenu/${id}`)
            .then((response)=>{
                commit('deleteSingleMenu',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchMenuGroup({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Menu/GetMenuGroupList')
            .then((response)=>{
                commit('setMenuGroup',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createMenuGroup({commit},objMenuGroup) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Menu/CreateMenuGroup',objMenuGroup)
            .then((response)=>{
                commit('newMenuGroup',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateMenuGroup({commit},objMenuGroup) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Menu/UpdateMenuGroup',objMenuGroup)
            .then((response)=>{
                commit('editMenuGroup',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteMenuGroup({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Menu/DeleteSingleMenuGroup/${id}`)
            .then((response)=>{
                commit('deleteSingleMenuGroup',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchAllMenu({commit},menuGroupId) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Menu/GetAllMenu/${menuGroupId}`)
            .then((response)=>{
                commit('getMenus',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    assignNewMenu({commit},objMenuOperation) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Menu/MenuAssign',objMenuOperation)
            .then((response)=>{
                commit('menuAssign',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
};
const mutations={
    setMenu:(state,appMenu)=>{
        state.appMenu=appMenu
    },
    setParentMenu:(state,parentMenu)=>{
        state.parentMenu=parentMenu
    },
    setMenuGroup:(state,menuGroup)=>{
        state.menuGroup=menuGroup
    },
    newMenuGroup:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editMenuGroup:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleMenuGroup:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    newMenu:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editMenu:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleMenu:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    getMenus:(state,appMenu)=>{
        state.appMenu=appMenu
    },
    menuAssign:(state,confirmation)=>{
        state.confirmation=confirmation
    },

};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}