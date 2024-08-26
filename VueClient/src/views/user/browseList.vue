<template>
    <v-data-table
      :headers="headersBrowse"
      :items="itemsBrowse"
      class="elevation-1"
    >
    </v-data-table>
</template>

<script>
export default {
    name:'BrowseList',
    data(){
        return{
            headersBrowse:[
                {text:'Name',value:'fullName'},
                {text:'User Name',value:'username'},
                {text:'LogIn Time',value:'logInTime'},
                {text:'LogOut Time',value:'logOutTime'},
                {text:'IP',value:'userIp'},
                {text:'Browser',value:'browser'},
                {text:'Browser Version',value:'browserVersion'},
                {text:'OS',value:'platform'}
            ],
            itemsBrowse:[]
        }
    },
    methods:{
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('user/fetchBrowseList')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsBrowse=response.data.return
            })
            .catch((err)=>{
                console.log(err)
            })
        },
    },
    created(){
        this.initialize()
    }
}
</script>