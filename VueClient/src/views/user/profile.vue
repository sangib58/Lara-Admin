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
                        v-model="name"
                        label="Name"                      
                        :rules="[rules.required]"                                                                    
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
                        v-model="mobile"
                        label="Mobile"                                                                                                                 
                        clearable
                    >
                    </v-text-field>
                </v-col>               
            </v-row>
            <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-text-field
                        v-model="email"
                        label="Email" 
                        :rules="[rules.emailRules]"                                                                                                               
                        clearable
                    >
                    </v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-menu
                        v-model="birthMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="dateOfBirth"
                                label="Date of Birth"
                                prepend-icon="mdi-calendar"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                clearable
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="dateOfBirth"
                            @input="birthMenu = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>               
            </v-row>
            <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-file-input
                        accept="image/*"
                        label="Profile Picture"
                        prepend-icon="mdi-camera"
                        @change="onFileSelected"                       
                        show-size
                    >
                    </v-file-input>                    
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    md="6"
                >
                    <v-img                       
                        :src="previewImage"
                        max-height="100"
                        max-width="150"
                        contain                      
                    >
                    </v-img>
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
                        @click="save"
                    >
                        save
                    </v-btn>
                </v-col>
            </v-row>
        </v-form>
    </div>   
</template>

<script>
import Message from '../../components/common/Message'
import config from '../../config'

export default {
    name:'Profile',
    components:{
        Message
    },
    data(){
        return{
            rules:{
                required:value=>!!value||'Required',
                emailRules:v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'             
            },
            birthMenu: false,
            imagePath:'',
            name:'',
            mobile:'',
            email:'',
            dateOfBirth:null,
            previewImage:null,
            selectedFile:null,           
        }
    },
    methods:{
        initialize(){  
            this.$store.dispatch('dashboard/applyLoading')        
            this.$store.dispatch('user/fetchSingleUser',parseInt(localStorage.getItem('loggedUserId')))
            .then(response=>{
                this.$store.dispatch('dashboard/cancelLoading')             
                this.name=response.data.return.name
                this.email=response.data.return.email
                this.mobile=response.data.return.mobile               
                this.dateOfBirth=response.data.return.date_of_birth==null?null:response.data.return.date_of_birth.substr(0,10)                
                this.previewImage=response.data.return.image_path!=null?response.data.return.image_path.replace('/\\','/'):''
                this.imagePath=this.previewImage
            })
            .catch(err=>{
                console.log(err)
            })
        },
        onFileSelected(e){           
            if(e!=null){
                this.selectedFile=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedFile)
                reader.onload=e=>{
                    this.previewImage=e.target.result
                }
            }else{               
                this.selectedFile=null
                this.previewImage=null
                this.imagePath=''
            }                      
        },
        updateUser(){
            this.$store.dispatch('dashboard/applyLoading')
            const objUserProfile={
                userId:parseInt(localStorage.getItem('loggedUserId')),
                name:this.name,
                mobile:this.mobile,
                email:this.email,
                imagePath:this.imagePath,
                dateOfBirth:this.dateOfBirth,
                updatedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            this.$store.dispatch('user/updateUserProfile',objUserProfile)
            .then(response=>{
                this.$store.dispatch('dashboard/cancelLoading')
                if(response.status==200){
                    this.$root.$emit('message_from_parent',response.data.message)
                }
            })
            .catch(err=>{
                console.log(err)
            })
        },
        save(){
            if(this.$refs.form.validate()){
                if(this.selectedFile!=null){
                    let fd=new FormData()
                    fd.append('image',this.selectedFile)
                    this.$store.dispatch('user/uploadImage',fd)
                    .then(response=>{
                        if(response.status==200){
                            this.imagePath=config.hostname+'/'+response.data.return                           
                            this.updateUser()
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })
                }else{
                    this.updateUser()
                }
            }          
        }
    },
    created(){
        this.initialize()      
    }
}
</script>