<template>
    <div>
        <Message/>
        <v-form ref="form">
            <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-text-field
                        v-model="newPassword"
                        label="New Password"
                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                        :rules="[rules.required, rules.min]"
                        :type="show ? 'text' : 'password'"
                        hint="At least 8 characters"
                        @click:append="show = !show"
                        clearable
                    >
                    </v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-text-field
                        v-model="confirmPassword"
                        label="Confirm Password"
                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                        :rules="[rules.required, rules.min]"
                        :type="show ? 'text' : 'password'"
                        hint="At least 8 characters"
                        @click:append="show = !show"   
                        clearable                   
                    >
                    </v-text-field>
                </v-col>               
            </v-row>
            <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                >
                    <v-btn
                        color="primary"
                        @click="change"
                    >
                        Change
                    </v-btn>
                </v-col>
            </v-row>
        </v-form>
    </div>   
</template>

<script>
import Message from '../../components/common/Message'

export default {
    name:'Password',
    components:{
        Message
    },
    data(){
        return{
            rules:{
                required:value=>!!value||'Required',
                min: v => v.length >= 8 || 'Min 8 characters'
            },
            newPassword:'',
            confirmPassword:'',
            show: false,           
        }
    },
    methods:{
        change(){
            if(this.$refs.form.validate()){              
                const objUser={
                    userId:parseInt(localStorage.getItem('loggedUserId')),
                    password:this.newPassword
                }
                if(this.newPassword!=this.confirmPassword){
                    this.$root.$emit('message_from_parent','New and Confirm password not matched!')
                }else{
                    this.$store.dispatch('dashboard/applyLoading')
                    this.$store.dispatch('user/changePassword',objUser)
                    .then(response=>{
                    this.$store.dispatch('dashboard/cancelLoading')
                    if(response.status==200){
                            this.$root.$emit('message_from_parent',response.data.message)                    
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })
                }
            }
            
        }
    }
}
</script>