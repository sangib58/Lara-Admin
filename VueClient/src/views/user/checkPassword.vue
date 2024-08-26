<template>
    <div>
        <Message/>
        <v-overlay :value="isUnlock" :opacity="opacity">
            <v-form ref="form">
                <v-container class="text-center">
                    <v-row no-gutters>
                        <v-col cols="12"><img :src=getImg></v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12"><v-text-field :rules="[rules.required]" v-model="password" label="Type password" outlined></v-text-field></v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12"><v-btn outlined @click="cancelOverlay"><v-icon x-large>vpn_key</v-icon></v-btn></v-col>
                    </v-row>
                </v-container>         
            </v-form>
        </v-overlay>
    </div>
</template>

<script>
import Message from '../../components/common/Message'

export default {
    name:'CheckPassword',
    components:{
        Message
    },
    data(){
        return{
            opacity:0.95,
            password:'',
            rules: {
                required: value => !!value || 'Required.'
            }
        }
    },
    methods:{
        cancelOverlay(){
            if(this.$refs.form.validate()){
                const chkPassword={
                    password:this.password,
                    hashedPassword:this.$store.getters['dashboard/userInfo'].password
                }
                this.$store.dispatch('dashboard/clearOverlay',chkPassword)
                .then(response=>{
                    if(response.status==200){          
                        this.password=''
                    }
                    else if(response.status==202){
                        this.$root.$emit('message_from_parent',response.data.message)
                    }
                })
                .catch(err=>{
                    console.log(err)
                })
            }
        }
    },
    computed:{
        isUnlock:function(){
            return this.$store.getters['dashboard/overlay']
        },
        getImg:function(){
            return this.$store.getters['dashboard/userInfo'].image_path==null?'':this.$store.getters['dashboard/userInfo'].image_path.replace('/\\','/')
        },
    }

}
</script>