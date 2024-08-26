<template>
    <div class="login-page">      
        <v-card elevation="10" class="login-box" shaped>
            <Message/>
            <v-card-text class="text-center">
                <h2 class="black--text">{{title}}</h2>
                <p class="grey--text">{{description}}</p>           
            </v-card-text>

            <v-card-text>
                <v-form ref="form">
                    <v-text-field 
                        label="Username" 
                        v-model="username" 
                        append-icon="person"
                        :rules="[rules.required]"
                    >
                    </v-text-field>
                    <v-text-field 
                        label="Password" 
                        v-model="password" 
                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                        :rules="[rules.required, rules.min]"
                        :type="show ? 'text' : 'password'"                            
                        hint="At least 8 characters"                                      
                        @click:append="show = !show"
                    >
                    </v-text-field>
                    <v-spacer></v-spacer>
                    <v-btn :loading="loading" @click="submit" rounded dark>Sign In</v-btn>
                    <div class="mt-4 text-center font-weight-bold">
                        <a class="text-decoration-none text-uppercase" @click="adminCred">Admin</a> 
                        <a class="text-decoration-none text-uppercase pl-6" @click="userCred">User</a>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>      
    </div>               
</template>

<script>
import Message from '../../components/common/Message'

export default {
    name:'Signin',
    components:{
        Message
    },
    data(){
        return{
            username:'',
            password:'',
            show: false,
            rules: {
                required: value => !!value || 'Required.',
                min: v => v.length >= 8 || 'Min 8 characters'
            },
            loading:false,
            userInfo:null,
            msg:'',
            userId:null
        }
    },
    computed:{
        title:function(){
            return this.$store.getters['dashboard/siteTitle']
        },
        description:function(){
            return this.$store.getters['dashboard/description']
        },
    },
    methods:{
        adminCred(){
            this.username='admin@2021'
            this.password='admin@2021'
        },
        userCred(){
            this.username='user@2021'
            this.password='user@2021'
        },
        submit(){
            if(this.$refs.form.validate()){
                this.loading=true
                const credential={
                    username:this.username,
                    password:this.password
                }
                this.$store.dispatch('dashboard/fetchSigninInfo',credential)
                .then(response=>{                                              
                        if(response.status==200){
                            this.userId=parseInt(localStorage.getItem('loggedUserId'))                                                     
                            const objLogHistory={
                                userId:this.userId,
                                token:localStorage.getItem('token'),
                                userIp:this.$store.getters['dashboard/clientInfo'].userIp,
                                browserName:this.$store.getters['dashboard/clientInfo'].browserName==null?'others':this.$store.getters['dashboard/clientInfo'].browserName,
                                browserVersion:this.$store.getters['dashboard/clientInfo'].browserVersion,
                                platform:this.$store.getters['dashboard/clientInfo'].platform                          
                            }
                            this.$store.dispatch('dashboard/createLogHistory',objLogHistory)
                            .then((response)=>{
                                if(response.status==200){
                                    this.$store.dispatch('dashboard/changeVisibility')
                                    this.$router.push({name:'Dashboard'})                                                                     
                                }
                            })                                                                                
                        }else if(response.status==202){                          
                            this.loading=false
                            this.$root.$emit('message_from_parent',response.data.message)
                            localStorage.removeItem('token')
                            localStorage.removeItem('loggedUserId')
                            localStorage.removeItem('logCode')
                        }                      
                    })
                .catch(err => {                   
                    if(this.$store.getters['dashboard/authStatus']==='error'){
                        this.loading=false                      
                        this.msg='Error established in network connection!'
                        this.$root.$emit('message_from_parent',this.msg)
                    }
                    console.log(err)
                })  
            }
        },
    },
    created(){
        this.$store.dispatch('dashboard/signOut') 
        this.$store.dispatch('dashboard/fetchSettings')
        this.$store.dispatch('dashboard/fetchClientInfo')      
        if(localStorage.getItem('logCode')!=null){
            const objLogCode={
                token:localStorage.getItem('logCode')
            }
            this.$store.dispatch('dashboard/updateLogHistory',objLogCode)
        }
    } 
}
</script>

<style scoped>
    .login-page {
        align-items: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 85vh;
    }
    
    .login-box {
        width: 380px;
    }
</style>