<template>
  <div>
    <Message/>
    <v-btn @click="generatePdf" small outlined>PDF</v-btn>
    <vue-excel-xlsx class="btnExcel" :data="itemsMenuGroup" :columns="excelColumnsMenuGroup" :filename="'menuGroup-table'" :sheetname="'menuGroups'">EXCEL</vue-excel-xlsx>
    <v-data-table
      :headers="headersMenuGroup"
      :items="itemsMenuGroup"
      :items-per-page="5"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar
          flat
        >
          
          <v-dialog
            v-model="dialog"
            max-width="600"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="primary"
                dark
                class="mb-2"
                v-bind="attrs"
                v-on="on"
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
                  <v-row>
                    <v-col
                      cols="12"
                      sm="6"
                      md="6"
                    >
                      <v-form ref="form">
                        <v-text-field
                          v-model="editedItem.menuGroupName"
                          label="Menu Group"
                          :rules="[rules.required]"
                          clearable                       
                        ></v-text-field>
                      </v-form>
                    </v-col>                 
                  </v-row>
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
          <v-dialog v-model="dialogMenu" max-width="600">
            <v-card>
              <v-treeview
                activatable
                :items="items"               
              >
                <template slot="label" slot-scope="{ item }">
                  <v-chip :color="getColor(item)"><a @click="assignToGroup(item)">{{ openDialog(item) }}</a></v-chip>                 
                </template>
              </v-treeview>
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
          class="mr-2"
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
        <v-icon
          small
          @click="getMenus(item)"
        >
          menu
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
    name:'MenuGroupList',
    components:{
      Message
    },
    data(){
        return{
          items:[],
          rules:{
            required:value=>!!value||'Required'
          },         
          dialog:false,
          dialogDelete:false,
          dialogMenu:false,
          headersMenuGroup:[
            {text:'Menu Group',value:'menuGroupName'},
            {text: 'Actions', value: 'actions', sortable: false },
          ],
          itemsMenuGroup:[],
          editedIndex:-1,
          editedItem:{
            menuGroupID:'',
            menuGroupName:''
          },
          defaultItem:{
            menuGroupID:'',
            menuGroupName:''
          },
          excelColumnsMenuGroup : [
            {label: "Menu Group",field: "menuGroupName"}           
          ],
        }
    },
    methods:{
      generatePdf(){
        const doc = new jsPDF()
        doc.autoTable({
          body: this.itemsMenuGroup,
          columns: [
            { header: 'Menu Group', dataKey: 'menuGroupName' }
          ],
        })
        doc.save('menuGroup-table.pdf')
      },
      openDialog(item){
        return item.title
      },
      getColor(item){
        if(item.isSelected==true){
          return 'lime'
        }else{
          return ''
        }
      },
      assignToGroup(item){
        if(item.isSelected!=null){
          const objMenuOperation={
            menuId:item.id,
            menuGroupId:item.groupId,
            isSelected:item.isSelected,
            addedBy:parseInt(localStorage.getItem('loggedUserId')),
          }
          this.$store.dispatch('menu/assignNewMenu',objMenuOperation)
          .then(response=>{
            if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.message)
              if(response.data.message=='assigned'){
                item.isSelected=true
                this.getColor(item)
              }else if(response.data.message=='un-assigned'){
                item.isSelected=false
                this.getColor(item)
              }
            }else if(response.status==202){
              this.$root.$emit('message_from_parent',response.data.message)
            }
          })
          .catch(err=>{
            console.log(err)
          })
        }else{
          this.$root.$emit('message_from_parent','Click to child only')
        }
      },
      initialize(){
        this.$store.dispatch('dashboard/applyLoading')
        this.$store.dispatch('menu/fetchMenuGroup')
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          this.itemsMenuGroup=response.data.return
        })
        .catch((err)=>{
          console.log(err)
        })
      },
      editItem(item){
        //console.log(item)
        this.editedIndex=this.itemsMenuGroup.indexOf(item)
        this.editedItem=Object.assign({},item)
        this.dialog=true
      },
      deleteItem(item){       
        this.editedItem = Object.assign({}, item)
        this.dialogDelete=true
      },
      deleteItemConfirm () {
        this.$store.dispatch('menu/deleteMenuGroup',this.editedItem.menuGroupID)
        .then(response=>{
            //console.log(response)
            if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.message)
              this.$store.dispatch('menu/fetchMenuGroup')
              .then((response)=>{
                this.itemsMenuGroup=response.data.return                  
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
      getMenus(item){
        this.$store.dispatch('dashboard/applyLoading')
        this.dialogMenu=true
        this.$store.dispatch('menu/fetchAllMenu',item.menuGroupID)
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          if(response.status==200){
            this.items=response.data.return
          }
        })
        .catch((err)=>console.log(err))
      },
      close(){
        this.dialog=false
        this.$nextTick(()=>{
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
        //console.log(this.editedIndex)
        if(this.$refs.form.validate()){
          if (this.editedIndex > -1) {
          //edit
          const objMenuGroup={
            menuGrpId:this.editedItem.menuGroupID,
            menuGrpName:this.editedItem.menuGroupName,
            updatedBy:parseInt(localStorage.getItem('loggedUserId')),
          }
          this.$store.dispatch('menu/updateMenuGroup',objMenuGroup)
          .then(response=>{
            //console.log(response)
            if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.message)
              this.$store.dispatch('menu/fetchMenuGroup')
              .then((response)=>{
                this.close()
                this.itemsMenuGroup=response.data.return
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
          const objMenuGroup={
            menuGrpName:this.editedItem.menuGroupName,
            addedBy:parseInt(localStorage.getItem('loggedUserId')),
          }
          this.$store.dispatch('menu/createMenuGroup',objMenuGroup)
          .then(response=>{
            if(response.status==200){
              this.$root.$emit('message_from_parent',response.data.message)
              this.$store.dispatch('menu/fetchMenuGroup')
              .then((response)=>{
                this.close()
                this.itemsMenuGroup=response.data.return
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
    }
}
</script>