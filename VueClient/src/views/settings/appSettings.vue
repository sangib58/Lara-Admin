<template>
    <div>
        <v-container v-if="roleId==1">
            <v-row>
                <v-col cols="12" sm="4" class="pl-2"><span class="font-weight-bold">General Settings</span></v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="4" class="pl-2">
                    <v-text-field label="Site Name" v-model="title" v-on:keyup="setTitle"></v-text-field>
                </v-col>
                <v-col cols="12" sm="8" class="pl-2">
                    <v-text-field label="Welcome Message" v-model="description" v-on:keyup="setDescription"></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4" class="pl-2">
                    <v-text-field label="Footer Text" v-model="footerText" v-on:keyup="setFooterText"></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="4" class="pl-2"><span class="font-weight-bold">Color Settings</span></v-col>
            </v-row>
        </v-container>
        <v-container>
            <v-row>
                <v-col cols="12" sm="4" class="pl-2">
                    <v-select
                        :items="colors"
                        label="App Bar"
                        dense
                        clearable
                        v-on:change="setAppBarColor"
                    >
                    </v-select>
                </v-col>
                <v-col cols="12" sm="4" class="pl-2">
                    <v-select
                        :items="colors"
                        label="Footer"
                        dense
                        clearable
                        v-on:change="setFooterColor"
                    >
                    </v-select>
                </v-col>
                <v-col cols="12" sm="4" class="pl-2">
                    <v-select
                        :items="bodyColors"
                        label="Body"
                        dense
                        clearable
                        v-on:change="setBodyColor"
                    >
                    </v-select>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {
    name:'AppSettings',
    data(){
        return{
            colors:['default','blue-grey darken-1','blue-grey darken-2','blue-grey darken-3','blue-grey darken-4','grey darken-3'],
            bodyColors:['default','grey lighten-1','grey lighten-2','grey lighten-3','grey lighten-4','grey lighten-5'],
            title:'',
            description:'',
            footerText:''
        }
    },
    computed:{
        roleId:function(){
            return this.$store.getters['dashboard/userInfo'].user_role_id
        }
    },
    methods:{
        setAppBarColor(val){
            this.$store.dispatch('dashboard/changeAppBarColor',val) 
        },
        setFooterColor(val){
            this.$store.dispatch('dashboard/changeFooterColor',val) 
        },
        setBodyColor(val){          
            if(val==null || val=='default'){
                val='grey lighten-3'
            }
            this.$store.dispatch('dashboard/changeBgColor',val) 
        },
        setTitle(){   
            const obj={title:this.title}         
            this.$store.dispatch('dashboard/changeSiteTitle',obj) 
        },
        setDescription(){     
            const obj={description:this.description}       
            this.$store.dispatch('dashboard/changeSiteDescription',obj) 
        },
        setFooterText(){ 
            const obj={footer:this.footerText}            
            this.$store.dispatch('dashboard/changeFooterText',obj) 
        },
    },
    created(){
        this.title=this.$store.getters['dashboard/siteTitle']
        this.description=this.$store.getters['dashboard/description']
        this.footerText=this.$store.getters['dashboard/footerText']
    }
}
</script>