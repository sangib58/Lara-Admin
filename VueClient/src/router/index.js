import Vue from 'vue'
import VueRouter from 'vue-router'
import SignIn from '../views/signin/SignIn.vue'
import Unauthorized from '../views/error/Unauthorized.vue'
import Forbidden from '../views/error/Forbidden.vue'
import NotFound from '../views/error/NotFound.vue'
import Exception from '../views/error/Exception.vue'
import Dashboard from '../views/dashboard/DashBoard.vue'
import MenuList from '../views/menu/menuList.vue'
import MenuGroupList from '../views/menu/menugroupList.vue'
import UserList from '../views/user/userList.vue'
import UserRoleList from '../views/user/userRoleList.vue'
import Profile from '../views/user/profile.vue'
import Password from '../views/user/password.vue'
import AppSettings from '../views/settings/appSettings.vue'
import BrowseList from '../views/user/browseList.vue'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'


Vue.use(VueRouter)

const routes = [
  {
    path:'/',
    name:'SignIn',
    component:SignIn
  },
  {
    path: '/unauthorized',
    name: 'Unauthorized',
    component: Unauthorized
  },
  {
    path: '/forbidden',
    name: 'Forbidden',
    component: Forbidden
  },
  {
    path: '/notFound',
    name: 'NotFound',
    component: NotFound
  },
  {
    path: '/exception',
    name: 'Exception',
    component: Exception
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/menu/menuList',
    name: 'MenuList',
    component: MenuList
  },
  {
    path: '/menu/menuGroupList',
    name: 'MenuGroupList',
    component: MenuGroupList
  },
  {
    path: '/user/userList',
    name: 'UserList',
    component: UserList
  },
  {
    path: '/user/userRoleList',
    name: 'UserRoleList',
    component: UserRoleList
  },
  {
    path: '/user/profile',
    name: 'Profile',
    component: Profile
  },
  {
    path: '/user/password',
    name: 'Password',
    component: Password
  },
  {
    path: '/user/browseList',
    name: 'BrowseList',
    component: BrowseList
  },
  {
    path: '/settings/appSettings',
    name: 'AppSettings',
    component: AppSettings
  },
]

const router = new VueRouter({
  mode: 'history',
  hashbang: false,
  base: process.env.BASE_URL,
  routes
})
router.beforeEach((to, from, next) => {
  NProgress.start()
  NProgress.inc(0.1);
  //NProgress.set(0.1)
  if(to.name==null){
    next({name:'NotFound'})
  }else if(to.name!='SignIn' && localStorage.getItem('token')==null){
    next({name:'SignIn'})
  }else{
    next()
  }
})
router.afterEach(() => {
  setTimeout(() => NProgress.done(), 500)
})

export default router
