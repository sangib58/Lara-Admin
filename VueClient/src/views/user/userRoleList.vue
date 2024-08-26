<template>
  <div>
    <Message/>
    <v-btn @click="generatePdf" small outlined>PDF</v-btn>
    <vue-excel-xlsx class="btnExcel" :data="itemsUserRole" :columns="excelColumnsRole" :filename="'role-table'" :sheetname="'roles'">EXCEL</vue-excel-xlsx>
    <v-data-table
      :headers="headersUserRole"
      :items="itemsUserRole"
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
                                    v-model="editedItem.roleName"
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
                                <v-text-field
                                    v-model="editedItem.roleDesc"
                                    label="Description"
                                    clearable                       
                                ></v-text-field>                    
                            </v-col>  
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >                     
                            <v-select
                                v-model="menuGroupSelect"
                                label="Menu Group"
                                :items="menuGroupItems"
                                item-text="menuGroupName"
                                item-value="menuGroupID"
                                :rules="[rules.required]"                                                       
                                clearable
                                return-object                       
                            ></v-select>                                         
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
        </v-toolbar>
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
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'userRoleList',
    components:{
      Message
    },
    data(){
      return{
        menuGroupSelect: null,
        menuGroupItems:[],
        rules:{
          required:value=>!!value||'Required'
        },
        dialog:false,
        dialogDelete:false,
        headersUserRole:[
          {text:'Name',value:'roleName'},
          {text:'Description',value:'roleDesc'},
          {text:'Menu Group',value:'menuGroupName'},
          { text: 'Actions', value: 'actions', sortable: false },
        ],
        itemsUserRole:[],
        editedIndex:-1,
        editedItem:{
          userRoleId:'',
          roleName:'',
          roleDesc:'',
          menuGroupName:'',
          menuGroupID:''
        },
        defaultItem:{
          userRoleId:'',
          roleName:'',
          roleDesc:'',
          menuGroupName:'',
          menuGroupID:''
        },
        excelColumnsRole : [
          {label: "Role",field: "roleName"},
          {label: 'Description', field: 'roleDesc' },
          {label: 'Menu Group', field: 'menuGroupName' }           
        ],
      }
    },
    methods:{
      generatePdf(){
        const doc = new jsPDF()
        doc.autoTable({
          body: this.itemsUserRole,
          columns: [
            { header: 'Role', dataKey: 'roleName' },
            { header: 'Description', dataKey: 'roleDesc' },
            { header: 'Menu Group', dataKey: 'menuGroupName' },
          ],
        })
        doc.save('role-table.pdf')
      },
      initialize(){
        this.$store.dispatch('dashboard/applyLoading')
        this.$store.dispatch('user/fetchUserRoles')
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          this.itemsUserRole=response.data.return
        })
        .catch((err)=>{
            console.log(err)
        })
      },
      getMenuGroups(){
        this.$store.dispatch('user/fetchMenuGroups')
        .then((response)=>{
            //console.log(response.data.data)
            this.menuGroupItems=response.data.return
        })
        .catch((err)=>{
            console.log(err)
        })
      },
      resetNewItem(){
        this.getMenuGroups()
      },
      editItem(item){
        //console.log(item)
        this.getMenuGroups()
        this.menuGroupSelect= {menuGroupName: item.menuGroupName, menuGroupID: item.menuGroupID}
        this.editedIndex=this.itemsUserRole.indexOf(item)
        this.editedItem=Object.assign({},item)
        this.dialog=true
      },
      deleteItem(item){
        this.editedItem = Object.assign({}, item)
        this.dialogDelete=true
      },
      deleteItemConfirm () {
        this.$store.dispatch('user/deleteUserRole',this.editedItem.userRoleId)
        .then(response=>{
            //console.log(response)
            if(response.status==200){
            this.$root.$emit('message_from_parent',response.data.message)
            this.$store.dispatch('user/fetchUserRoles')
            .then((response)=>{
                this.itemsUserRole=response.data.return                  
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
            this.menuGroupSelect=null             
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
      save () {
        if(this.$refs.form.validate()){
          if (this.editedIndex > -1) {
            //edit
            const objUserRole={
              roleId:this.editedItem.userRoleId,
              roleName:this.editedItem.roleName,
              roleDesc:this.editedItem.roleDesc,
              menuGrpId:this.menuGroupSelect.menuGroupID,
              updatedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            this.$store.dispatch('user/updateUserRole',objUserRole)
            .then(response=>{
              //console.log(response)
              if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.message)
              this.$store.dispatch('user/fetchUserRoles')
              .then((response)=>{
                  this.close()
                  this.itemsUserRole=response.data.return
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
            } else {
            //insert
            const objUserRole={
              roleName:this.editedItem.roleName,
              roleDesc:this.editedItem.roleDesc,
              menuGrpId:this.menuGroupSelect.menuGroupID,
              addedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            this.$store.dispatch('user/createUserRole',objUserRole)
            .then(response=>{
              //console.log(response)
              if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.message)
                this.$store.dispatch('user/fetchUserRoles')
                .then((response)=>{
                  this.close()
                  this.itemsUserRole=response.data.return
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
      },
    computed:{
      formTitle(){
        return this.editedIndex===-1?'New Item':'Edit Item'
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
      this.getMenuGroups()
    }
}
</script>

<style scoped>

</style>