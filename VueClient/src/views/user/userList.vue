<template>
  <div>
    <Message/>
    <v-btn @click="generatePdf" small outlined>PDF</v-btn>
    <vue-excel-xlsx class="btnExcel" :data="itemsUsers" :columns="excelColumnsUser" :filename="'user-table'" :sheetname="'users'">EXCEL</vue-excel-xlsx>
    <v-data-table
      :headers="headersUsers"
      :items="itemsUsers"
      :items-per-page="5"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar
          flat
        >
          
          <v-dialog
            v-model="dialog"
            max-width="800"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="primary"
                dark
                class="mb-2"
                v-bind="attrs"
                v-on="on"
                @click="resetNewItem"
                absolute
                right
              >
                New Item
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                    <v-form ref="form">
                        <v-row>
                            <v-col
                              cols="12"
                              sm="6"
                              md="4"
                            >                     
                                <v-text-field
                                    v-model="editedItem.fullName"
                                    label="Name"
                                    :rules="[rules.required]"
                                    clearable                       
                                ></v-text-field>                    
                            </v-col>                             
                            <v-col
                              cols="12"
                              sm="6"
                              md="4"
                            >                     
                              <v-select
                                v-model="roleSelect"
                                label="User Role"
                                :items="roleItems"
                                item-text="roleName"
                                item-value="userRoleId"
                                :rules="[rules.required]"                                                       
                                clearable
                                return-object                       
                              ></v-select>                                         
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >                     
                            <v-text-field
                              v-model="editedItem.mobile"
                              label="Mobile"
                              clearable                       
                            ></v-text-field>                    
                          </v-col>                
                        </v-row>
                        <v-row>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >                     
                            <v-text-field
                              v-model="editedItem.email"
                              label="Email"
                              :rules="[rules.emailRules]"
                              clearable                       
                            ></v-text-field>                    
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >                     
                            <v-text-field
                              v-model="editedItem.userName"
                              label="User Name"
                              :rules="[rules.required]" 
                              clearable                       
                            ></v-text-field>                    
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >                     
                            <v-text-field
                              v-model="editedItem.password"
                              label="Password"
                              :rules="[rules.required,rules.min]" 
                              :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                              :type="show ? 'text' : 'password'"
                              hint="At least 8 characters"
                              @click:append="show = !show"
                              v-if="isShowPassword"
                              clearable                       
                            ></v-text-field>                    
                          </v-col> 
                        </v-row>
                        <v-row>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
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
                                  v-model="editedItem.dateOfBirth"
                                  label="Date of Birth"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  v-bind="attrs"
                                  v-on="on"
                                  clearable
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="editedItem.dateOfBirth"
                                @input="birthMenu = false"
                              ></v-date-picker>
                            </v-menu>
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                            <v-file-input
                              v-model="imageFile"
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
                            md="4"
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
                    </v-form>                
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="close"
                >
                  Cancel
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="save"
                >
                  Save
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogPassword" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="headline">Reset Password</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-form ref="formReset">
                    <v-row>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field
                              v-model="resetPass"
                              label="Password"
                              :rules="[rules.required,rules.min]"                              
                              hint="At least 8 characters"                             
                              clearable                       
                            ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="closePassword"
                >
                  Cancel
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="resetPassword"
                >
                  Reset
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:[`item.dateOfBirth`]="{item}">
        <span>{{formatDateOfBirth(item)}}</span> 
      </template>
      <template v-slot:[`item.actions`]="{item}">
        <v-icon
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          class="mr-2"
          @click="editPassword(item)"
        >
          vpn_key
        </v-icon>
        <v-icon
          small
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn
          color="primary"
          @click="initialize"
        >
          Reset
        </v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import Message from '../../components/common/Message'
import config from '../../config'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'userList',
    components:{
        Message
    },
    data(){
        return{
          show: false,
          imageFile:null,
          previewImage:null,
          selectedFile:null,
          birthMenu: false,
          roleSelect: null,
          roleItems:[],
          rules:{
            required:value=>!!value||'Required',
            min: v => v.length >= 8 || 'Min 8 characters',
            emailRules:v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
          },
          dialog:false,
          dialogDelete:false,
          dialogPassword:false,
          resetPass:'',
          headersUsers:[
            {text:'Name',value:'fullName'},
            {text:'Role',value:'roleName'},
            {text:'Mobile',value:'mobile'},
            {text:'Email',value:'email'},
            {text:'Date of Birth',value:'dateOfBirth'},
            {text:'User Name',value:'userName'},
            { text: 'Actions', value: 'actions', sortable: false },
          ],
          itemsUsers:[],
          editedIndex:-1,
          editedItem:{
            userId:'',
            userRoleId:'',
            roleName:'',
            fullName:'',
            mobile:'',
            email:'',
            imagePath:'',
            dateOfBirth:null,
            userName:'',
            password:'',
          },
          defaultItem:{
            userId:'',
            userRoleId:'',
            roleName:'',
            fullName:'',
            mobile:'',
            email:'',
            imagePath:'',
            dateOfBirth:null,
            userName:'',
            password:'',
          },
          excelColumnsUser : [
            {label: "Name",field: "fullName"},
            {label: "Role",field: "roleName"},
            {label: "Mobile",field: "mobile"},
            {label: "Email",field: "email"},
            {label: "Date of Birth",field: "dateOfBirth"},
            {label: "Username",field: "userName"},           
          ],
        }
    },
    methods:{
      generatePdf(){
        const doc = new jsPDF()
        doc.autoTable({
          body: this.itemsUsers,
          columns: [
            { header: 'Name', dataKey: 'fullName' },
            { header: 'Role', dataKey: 'roleName' },
            { header: 'Mobile', dataKey: 'mobile' },
            { header: 'Email', dataKey: 'email' },
            { header: 'Date of Birth', dataKey: 'dateOfBirth' },
            { header: 'Username', dataKey: 'userName' },
          ],
        })
        doc.save('user-table.pdf')
      },
      initialize(){
        this.$store.dispatch('dashboard/applyLoading')
        this.$store.dispatch('user/fetchUsers')
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          this.itemsUsers=response.data.return
        })
        .catch((err)=>{
            console.log(err)
        })
      },
      getUserRoles(){
        this.$store.dispatch('user/fetchUserRoles')
        .then((response)=>{
          this.roleItems=response.data.return
        })
        .catch((err)=>{
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
          this.editedItem.imagePath=''
        }                      
      },
      resetNewItem(){
        this.getUserRoles()
      },
      formatDateOfBirth(item){
        return item.dateOfBirth==null?null:item.dateOfBirth.substr(0,10)
      },
      editItem(item){
        item.dateOfBirth=item.dateOfBirth==null?null:item.dateOfBirth.substr(0,10)
        this.previewImage=item.imagePath!=null?item.imagePath.replace('/\\','/'):''
        this.roleSelect= {roleName: item.roleName, userRoleId: item.userRoleId}
        this.editedIndex=this.itemsUsers.indexOf(item)
        this.editedItem=Object.assign({},item)
        this.dialog=true
      },
      editPassword(item){
        //console.log(item)
        this.editedItem=Object.assign({},item)
        this.dialogPassword=true
      },
      deleteItem(item){
        this.editedItem = Object.assign({}, item)
        this.dialogDelete=true
      },
      deleteItemConfirm () {
        this.$store.dispatch('user/deleteUser',this.editedItem.userId)
        .then(response=>{
          //console.log(response)
          if(response.status==200){
            this.$root.$emit('message_from_parent',response.data.message)
            this.$store.dispatch('user/fetchUsers')
            .then((response)=>{
                this.itemsUsers=response.data.return                  
            })
            .catch((err)=>{
                console.log(err)
            })
          }else if(response.status==202){
            this.$root.$emit('message_from_parent',response.data.message)
          }
        })
        .catch(err=>{
          console.log(err)
        })
        this.closeDelete()
      },
      close(){
        this.dialog=false
        this.$nextTick(()=>{
          this.roleSelect=null
          this.selectedFile=null
          this.imageFile=null
          this.previewImage=null
          this.editedItem=Object.assign({},this.defaultItem)
          this.editedIndex=-1
        })
      },
      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },
      closePassword(){
        this.dialogPassword=false
      },
      resetPassword(){
        if(this.$refs.formReset.validate()){
          const objUser={
            userId:this.editedItem.userId,
            password:this.resetPass
          }
          //console.log(objUser)
          this.$store.dispatch('user/changePassword',objUser)
          .then(response=>{
          if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.message)                    
            }
          })
          .catch(err=>{
            console.log(err)
          })
          this.dialogPassword=false
          this.resetPass=''
        }
      },
      save(){
        if(this.$refs.form.validate()){
          if(this.selectedFile!=null){
            let fd=new FormData()
            fd.append('image',this.selectedFile)
            this.$store.dispatch('user/uploadImage',fd)
            .then(response=>{
              //console.log(response)
              if(response.status==200){
                this.editedItem.imagePath=config.hostname+'/'+response.data.return                           
                this.insertOrUpdateUser()
              }
            })
            .catch(err=>{
              console.log(err)
            })
          }else{
              this.insertOrUpdateUser()
          }
        }
      },
      insertOrUpdateUser(){    
        this.editedItem.dateOfBirth=this.editedItem.dateOfBirth==null?null:this.editedItem.dateOfBirth.substr(0,10)                
        this.previewImage=this.editedItem.imagePath!=null?this.editedItem.imagePath:''
        this.imagePath=this.editedItem.imagePath!=null?this.editedItem.imagePath:''
        if (this.editedIndex > -1){
          //console.log('edit')
          const objUser={
            userId:this.editedItem.userId,
            roleId:this.roleSelect.userRoleId,
            name:this.editedItem.fullName,
            mobile:this.editedItem.mobile,
            email:this.editedItem.email,
            imagePath:this.editedItem.imagePath,
            dateOfBirth:this.editedItem.dateOfBirth,
            username:this.editedItem.userName,
            //password:this.editedItem.password,
            updatedBy:parseInt(localStorage.getItem('loggedUserId')),
          }
          this.$store.dispatch('user/updateUser',objUser)
          .then(response=>{
            //console.log(response)
          if(response.status==200){
            this.$root.$emit('message_from_parent',response.data.message)
            this.$store.dispatch('user/fetchUsers')
            .then((response)=>{
              this.close()
              this.itemsUsers=response.data.return
            })
            .catch((err)=>{
              console.log(err)
            })
          }else if(response.status==202){
              this.$root.$emit('message_from_parent',response.data.message)
            }
          })
          .catch(err=>{
            console.log(err)
          })
        }else{
          const objUser={
            roleId:this.roleSelect.userRoleId,
            name:this.editedItem.fullName,
            mobile:this.editedItem.mobile,
            email:this.editedItem.email,
            imagePath:this.editedItem.imagePath,
            dateOfBirth:this.editedItem.dateOfBirth,
            username:this.editedItem.userName,
            password:this.editedItem.password,
            addedBy:parseInt(localStorage.getItem('loggedUserId')),
          }
          this.$store.dispatch('user/createUser',objUser)
          .then(response=>{
            //console.log(response)
          if(response.status==200){
            this.$root.$emit('message_from_parent',response.data.message)
            this.$store.dispatch('user/fetchUsers')
            .then((response)=>{
              this.close()
              this.itemsUsers=response.data.return
            })
            .catch((err)=>{
              console.log(err)
            })
          }else if(response.status==202){
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
      formTitle:function(){
        return this.editedIndex===-1?'New Item':'Edit Item'
      },
      isShowPassword:function(){
        return this.editedIndex===-1?true:false
      }
    },
    watch:{
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },
    created(){
      this.initialize()
      this.getUserRoles()
    }
}
</script>