<template>
  <div>
    <Message/>
    <v-btn @click="generatePdf" small outlined>Pdf</v-btn>
    <vue-excel-xlsx class="btnExcel" :data="itemsMenu" :columns="excelColumnsMenu" :filename="'menu-table'" :sheetname="'menus'">EXCEL</vue-excel-xlsx>
    <v-data-table
      :headers="headersMenu"
      :items="itemsMenu"
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
                          v-model="editedItem.menuTitle"
                          label="Menu Title"
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
                          v-model="parentSelect"
                          label="Parent"
                          :items="parentItems"
                          item-text="text"
                          item-value="id"
                          :rules="[rules.required]"                        
                          v-on:change="chkOption"
                          clearable
                          return-object                       
                        ></v-select>                                         
                      </v-col>                      
                      <v-col
                        cols="12"
                        sm="6"
                        md="4"  
                        v-if="chkUrlVisibility==true"                                       
                      >                     
                        <v-text-field
                          v-model="editedItem.url"
                          label="URL"
                          :rules="[rules.required]"                        
                          clearable                  
                        ></v-text-field>   

                      </v-col>                                      
                    </v-row>
                    <v-row
                      v-if="chkOthersVisibility==true"
                    >         
                      <v-col
                        cols="12"
                        sm="6"
                        md="4"
                      >                     
                        <v-text-field
                          v-model="editedItem.sortOrder"
                          label="Order No."
                          type="number"
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
                          v-model="editedItem.iconClass"
                          label="Icon(Material design icon)" 
                          :rules="[rules.required]"
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
      <template v-slot:[`item.isSubMenu`]="{item}">
        <span>{{subMenuText(item)}}</span> 
      </template>
      <template v-slot:[`item.sortOrder`]="{item}">
        <span>{{OrderText(item)}}</span> 
      </template>
      <template v-slot:[`item.parentMenuName`]="{item}">
        <span>{{parentMenuText(item)}}</span> 
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
    name:'MenuList',
    components:{
        Message
    },
    data(){
        return{     
          isUrlVisible:false,
          isOtherVisible:false,     
          parentSelect: null,
          parentItems:[],
          rules:{
            required:value=>!!value||'Required'
          },
          dialog:false,
          dialogDelete:false,
          headersMenu:[
            {text:'Menu Title',value:'menuTitle'},
            {text:'URL',value:'url'},
            {text:'Is Sub-Menu?',value:'isSubMenu'},
            {text:'Order No.',value:'sortOrder'},
            {text:'Icon-Class',value:'iconClass'},
            {text:'Parent',value:'parentMenuName'},
            {text: 'Actions', value: 'actions', sortable: false },
          ],
          itemsMenu:[],
          editedIndex:-1,
          editedItem:{
            menuID:'',
            menuTitle:'',
            url:'',
            isSubMenu:'',
            sortOrder:'',
            iconClass:'',           
          },
          defaultItem:{
            menuID:'',
            menuTitle:'',
            url:'',
            isSubMenu:'',
            sortOrder:'',
            iconClass:'',
          },
          excelColumnsMenu : [
            {label: "Menu Title",field: "menuTitle"},
            {label: 'URL', field: 'url' },
            {label: 'Is Sub Menu?', field: 'isSubMenu' },
            {label: 'Menu Order', field: 'sortOrder' },
            {label: 'Icon name(material)', field: 'iconClass' },
            {label: 'Parent Menu Name', field: 'parentMenuName' },             
          ],
        }
    },
    methods:{
      generatePdf(){
        const doc = new jsPDF()
        doc.autoTable({
          body: this.itemsMenu,
          columns: [
            { header: 'Menu Title', dataKey: 'menuTitle' },
            { header: 'URL', dataKey: 'url' },
            { header: 'Is Sub Menu?', dataKey: 'isSubMenu' },
            { header: 'Menu Order', dataKey: 'sortOrder' },
            { header: 'Icon name(material)', dataKey: 'iconClass' },
            { header: 'Parent Menu Name', dataKey: 'parentMenuName' },
          ],
        })
        doc.save('menu-table.pdf')
      },
      initialize(){
        this.$store.dispatch('dashboard/applyLoading')
        this.$store.dispatch('menu/fetchMenu')
        .then((response)=>{
          this.$store.dispatch('dashboard/cancelLoading')
          this.itemsMenu=response.data.return
        })
        .catch((err)=>{
          console.log(err)
        })
      },
      getParentMenu(){
        this.$store.dispatch('menu/fetchParentMenu')
        .then((response)=>{
          this.parentItems=response.data.return
          //console.log(this.parentItems)
        })
        .catch((err)=>{
            console.log(err)
        })
      },
      chkOption(obj){
        if(obj==null){
          this.isUrlVisible=false
          this.isOtherVisible=false
        }else{
          //console.log(obj.id)
          if(obj.id==0){
            this.isUrlVisible=false
            this.isOtherVisible=true
          }else{
            this.isUrlVisible=true
            this.isOtherVisible=false
          }
        }       
      },
      subMenuText(item){
        if(item.isSubMenu==0){
          return 'no'
        }else{
          return 'yes'
        }
      },
      OrderText(item){
        if(item.sortOrder==0){
          return ''
        }else{
          return item.sortOrder
        }
      },
      parentMenuText(item){
        //console.log(item)
        if(item.parentMenuName==null){
          return 'Root'
        }else{
          return item.parentMenuName
        }
      },
      resetNewItem(){
        this.getParentMenu()
        this.isUrlVisible=false
        this.isOtherVisible=false
      },
      editItem(item){
        //console.log(item)
        this.getParentMenu()
        if(item.parentID==0){
          this.isOtherVisible=true
          this.isUrlVisible=false
        }else{
          this.isUrlVisible=true
          this.isOtherVisible=false
        }
        this.parentSelect= {text: item.parentMenuName, id: item.parentID}
        this.editedIndex=this.itemsMenu.indexOf(item)
        this.editedItem=Object.assign({},item)
        this.dialog=true
      },
      deleteItem(item){
        //this.editedIndex = this.itemsMenu.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete=true
      },
      deleteItemConfirm () {
        this.$store.dispatch('menu/deleteMenu',this.editedItem.menuID)
        .then(response=>{
          //console.log(response)
          if(response.status==200){
            this.$root.$emit('message_from_parent',response.data.message)
            this.$store.dispatch('menu/fetchMenu')
            .then((response)=>{
                this.itemsMenu=response.data.return                  
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
          this.parentSelect=null
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
      save (){
        //console.log(this.parentSelect.id)
        if(this.$refs.form.validate()){
          if(this.parentSelect.id==0){
            this.editedItem.url=''
            this.editedItem.isSubMenu=1
          }else{             
            this.editedItem.iconClass=''
            this.editedItem.isSubMenu=0
          }

          if(this.editedIndex>-1){
            //console.log(this.parentSelect)
            const objMenu={
              menuId:this.editedItem.menuID,
              parentId:this.parentSelect.id,
              menuTitle:this.editedItem.menuTitle,
              url:this.editedItem.url,
              isSubMenu:this.editedItem.isSubMenu,
              sortOrder:this.parentSelect.id==0?parseInt(this.editedItem.sortOrder):0,
              iconClass:this.editedItem.iconClass,
              updatedBy:parseInt(localStorage.getItem('loggedUserId'))
            }
            this.$store.dispatch('menu/updateMenu',objMenu)
            .then(response=>{  
              //console.log(response)            
              if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.message)
                this.$store.dispatch('menu/fetchMenu')
                .then((response)=>{
                  this.close()
                  this.itemsMenu=response.data.return
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
            const objMenu={
              parentId:this.parentSelect.id,
              menuTitle:this.editedItem.menuTitle,
              url:this.editedItem.url,
              isSubMenu:this.editedItem.isSubMenu,
              sortOrder:this.parentSelect.id==0?parseInt(this.editedItem.sortOrder):0,
              iconClass:this.editedItem.iconClass,
              addedBy:parseInt(localStorage.getItem('loggedUserId'))
            }
            this.$store.dispatch('menu/createMenu',objMenu)
            .then(response=>{
              if(response.status==200){
                this.$root.$emit('message_from_parent',response.data.message)
                this.$store.dispatch('menu/fetchMenu')
                .then((response)=>{
                  this.close()
                  this.itemsMenu=response.data.return
                })
                .catch((err)=>{
                  console.log(err)
                })
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
      }
    },
    computed:{
      formTitle:function(){
        return this.editedIndex===-1?'New Item':'Edit Item'
      },
      chkUrlVisibility:function(){
        return this.isUrlVisible
      },
      chkOthersVisibility:function(){
        return this.isOtherVisible
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
      this.getParentMenu()
    }
}
</script>