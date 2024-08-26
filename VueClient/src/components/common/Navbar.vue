<template>
    <nav>
        <v-app-bar app light :color="getAppBarColor">
            <v-app-bar-nav-icon @click="drawer=!drawer"></v-app-bar-nav-icon>
            <v-spacer></v-spacer>
            <v-btn @click="applyLock" icon>
                <v-icon>screen_lock_portrait</v-icon>
            </v-btn>
            <v-btn @click="toggle" icon>
                <v-icon>fullscreen</v-icon>
            </v-btn>           
            <v-menu offset-y>
                <template v-slot:activator="{on}">
                    <v-btn text v-on="on" color="grey">
                        <v-badge :content="browseCount" :value="browseCount" color="primary" overlap>
                            <v-icon>notifications_active</v-icon>
                        </v-badge>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item-group>                   
                        <v-list-item v-for="item in browseFilter" :key="item.logInTime" inactive>                          
                            <v-list-item-icon>
                                <v-icon>send</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    login time:{{item.logInTime}}, IP:{{item.userIp}}, Browser:{{item.browser}}, OS:{{item.platform}}
                                </v-list-item-title>
                            </v-list-item-content>                  
                        </v-list-item>
                    </v-list-item-group>
                </v-list>              
            </v-menu>
            <v-menu offset-y>
                <template v-slot:activator="{on}">
                    <v-btn text v-on="on" color="grey">
                        <v-icon left>expand_more</v-icon>
                        <span>Options</span>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item v-for="link in links" :key="link.text" :to="link.route">
                        <v-list-item-title>{{link.text}}</v-list-item-title>
                    </v-list-item>
                </v-list>              
            </v-menu>
            <v-btn @click.stop="dialog=true" icon>
                <v-icon>logout</v-icon>
            </v-btn>
        </v-app-bar>
        <v-dialog v-model="dialog" max-width="290" dark persistent>
            <v-card>
                <v-card-title class="headline">Want to leave?</v-card-title>
                <v-card-text>Press Signout to leave</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" text @click="dialog=false">Stay here</v-btn>
                    <v-btn color="dark" text @click="signOut">Signout</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-navigation-drawer 
            v-model="drawer"
            app
            dark
            :color="getAppBarColor">
             
            <v-list>
                <v-list-item two-line>
                    <v-list-item-avatar>
                        <img :src=getSrc>
                    </v-list-item-avatar>

                    <v-list-item-content>
                        <v-list-item-title class="text-capitalize">{{getFullName}}</v-list-item-title>
                        <v-list-item-subtitle><span class="grey--text">logged in</span></v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item to="/dashboard">
                    <v-list-item-icon>
                        <v-icon>mdi-home</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Dashboard</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-group v-for="item in menus" :key="item.title" :prepend-icon="item.icon" no-action>
                    <template v-slot:activator>
                        <v-list-item-title v-text="item.title"></v-list-item-title>                   
                    </template>
                    <v-list-item v-for="child in item.childItems" :key="child.title" :to="child.route">
                        <v-list-item-content>
                            <v-list-item-title v-text="child.title"></v-list-item-title> 
                        </v-list-item-content>                                            
                    </v-list-item>
                </v-list-group>
            </v-list>
        </v-navigation-drawer>
    </nav>
</template>

<script>
export default {
    name:'Navbar',
    data(){
        return{
            val:'',
            drawer:true,
            links: [
                { text: 'Profile', route: '/user/profile' },
                { text: 'Password', route: '/user/password' },
            ],
            dialog:false,
            roleId:null,
            token:null,
            menus:null,
            itemsBrowse:null,
            browseFilter:null,
            browseCount:null
        }
    },
    methods:{
        applyLock(){
            this.$store.dispatch('dashboard/applyOverlay')
        },
        toggle(){
            this.$fullscreen.toggle()
        },
        signOut(){
            this.$store.dispatch('dashboard/signOut')
            .then(()=>{
                this.$router.push({name:'SignIn'})
            })
            .catch((err)=>{
                console.log(err)
                this.$router.push({name:'SignIn'})
            })
        },
        getAppMenu(){
            const info={
                roleId:this.$store.getters['dashboard/userInfo'].user_role_id
            }
            this.$store.dispatch('dashboard/fetchMenu',info)
            .then((response)=>{
                if(response.status==200){               
                    this.menus=response.data.return
                }
            })
            .catch((err)=>console.log(err))
        },
        getBrowseData(){
            this.$store.dispatch('user/fetchNotificationList')
            .then((response)=>{
                this.itemsBrowse=response.data.return
                this.browseFilter=this.itemsBrowse.filter(function(obj){
                    return obj.UserId==parseInt(localStorage.getItem('loggedUserId'))
                })
                this.browseCount=this.browseFilter.length
            })
            .catch((err)=>{
                console.log(err)
            })
        }
    },
    computed:{
        getSrc:function(){
            return this.$store.getters['dashboard/userInfo'].image_path==null?'':this.$store.getters['dashboard/userInfo'].image_path.replace('/\\','/')
        },
        getFullName:function(){
            return localStorage.getItem('fullName')
        },
        getAppBarColor:function(){
            return this.$store.getters['dashboard/appBarColor']
        },
    },
    created(){
        this.getAppMenu()
        this.getBrowseData()
    }
}
</script>