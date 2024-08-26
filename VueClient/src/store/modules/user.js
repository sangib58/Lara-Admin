import axios from 'axios'
import config from './../../config'

const state={
    users:[],
    userRoles:[],
    menuGroups:[],
    browses:[],
    confirmation:{},
    imagePath:'',
    singleUser:{}
};
const getters={
    userList:state=>state.users,
    RoleList:state=>state.userRoles, 
    menuGroupList:state=>state.menuGroups,
    confirmation:state=>state.confirmation,
    imagePath:state=>state.imagePath,
    singleUser:state=>state.singleUser,
};
const actions={
    fetchUsers({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/GetUserList')
            .then((response)=>{
                commit('setUsers',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createUser({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Users/CreateUser',objUser)
            .then((response)=>{
                commit('newUser',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateUser({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateUser',objUser)
            .then((response)=>{
                commit('editUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteUser({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Users/DeleteSingleUser/${id}`)
            .then((response)=>{
                commit('deleteSingleUser',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchUserRoles({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/GetUserRoleList')
            .then((response)=>{
                commit('setUserRoles',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchMenuGroups({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Menu/GetMenuGroupList')
            .then((response)=>{
                commit('setMenuGroups',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    createUserRole({commit},objUserRole) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Users/CreateUserRole',objUserRole)
            .then((response)=>{
                commit('newUserRole',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateUserRole({commit},objUserRole) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateUserRole',objUserRole)
            .then((response)=>{
                commit('editUserRole',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    deleteUserRole({commit},id) {
        return new Promise((resolve,reject)=>{
            axios.delete(config.hostname+`/api/Users/DeleteSingleRole/${id}`)
            .then((response)=>{
                commit('deleteSingleUserRole',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    changePassword({commit},objUser) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/ChangeUserPassword',objUser)
            .then((response)=>{
                commit('passwordChange',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    uploadImage({commit},objFormData) {
        return new Promise((resolve,reject)=>{
            axios.post(config.hostname+'/api/Users/Upload',objFormData)
            .then((response)=>{
                commit('imageUpload',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    updateUserProfile({commit},objUserProfile) {
        return new Promise((resolve,reject)=>{
            axios.put(config.hostname+'/api/Users/UpdateUserProfile',objUserProfile)
            .then((response)=>{
                commit('updateProfile',response.data)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchSingleUser({commit},userId) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+`/api/Users/GetSingleUser/${userId}`)
            .then((response)=>{
                commit('singleUser',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchBrowseList({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/GetBrowseList')
            .then((response)=>{
                commit('setBrowseList',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
    fetchNotificationList({commit}) {
        return new Promise((resolve,reject)=>{
            axios.get(config.hostname+'/api/Users/GetNotificationList')
            .then((response)=>{
                commit('setBrowseList',response.data.return)
                resolve(response)
            })
            .catch((error)=>{
                reject(error)
            })
        })
    },
};
const mutations={
    setUsers:(state,users)=>{
        state.users=users
    },
    newUser:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editUser:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleUser:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    setUserRoles:(state,userRoles)=>{
        state.userRoles=userRoles
    },
    setMenuGroups:(state,menuGroups)=>{
        state.menuGroups=menuGroups
    },
    newUserRole:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    editUserRole:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    deleteSingleUserRole:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    passwordChange:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    imageUpload:(state,data)=>{
        state.imagePath=data.dbPath
    },
    updateProfile:(state,confirmation)=>{
        state.confirmation=confirmation
    },
    singleUser:(state,data)=>{
        state.singleUser=data
    },
    setBrowseList:(state,data)=>{
        state.browses=data
    },
};

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}