<template>
  <fullscreen ref="fullscreen" @change="fullscreenChange">
    <v-app>    
      <Navbar v-if="isVisible==true"/>
      <v-main :class="getBgColor">
        <v-container fluid>
          <router-view></router-view>
        </v-container>
      </v-main>  
      <Footer v-if="isVisible==true"/>
      <Loading/>
      <CheckPassword/>          
    </v-app>
  </fullscreen>
</template>



<script>
import Navbar from './components/common/Navbar'
import Footer from './components/common/Footer'
import router from './router/index'
import CheckPassword from '../src/views/user/checkPassword'
import Loading from './components/common/Loading'

export default {
  name:'App',
  data(){
    return{
      fullscreen: false,
    } 
  },
  components:{
    Navbar,
    Footer,
    CheckPassword,
    Loading
  },
  methods:{
    fullscreenChange (fullscreen) {
      this.fullscreen = fullscreen
    },
  },
  computed:{
    isVisible:function(){
      return this.$store.getters['dashboard/visible']
    },
    getBgColor:function(){
      return this.$store.getters['dashboard/bgColor']
    },
  },
  created(){
    this.$http.interceptors.response.use(undefined, function (err) {
      //console.log(err.response)
      if(err.response.status===401){
        router.push({name:'Unauthorized'})
      }else if(err.response.status===403){
        router.push({name:'Forbidden'})
      }else if(err.response.status==400){
        router.push({name:'Exception'})
      }else if(err.response.status==422){
        router.push({name:'Exception'})
      }else{
        router.push({name:'SignIn'})
      }
    })

    if(this.$route.path=='/'){
      this.$store.dispatch('dashboard/signOut')
    }
  }
}
</script>

<style>
  body{
    position: relative;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    overflow: auto;
  }
  .btnExcel {
    border: 1px solid black;
    background-color: #EEEEEE;
    color: black;
    padding: 3px 8px;
    font-size: 13px;
    border-radius: 5px;
    cursor: pointer;
  }
</style>