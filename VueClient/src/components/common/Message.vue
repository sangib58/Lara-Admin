<template>
    <v-snackbar v-model="snackbar" :timeout="timeout" top>
        <span>{{msg}}</span>
        <template v-slot:action="{ attrs }">
            <v-btn color="blue" text v-bind="attrs" @click="snackbar=false">close</v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    name:'Message',
    data(){
        return{
            snackbar: false,
            msg: '',
            timeout: 5000,
        }
    },
    mounted(){
        this.$root.$on('message_from_parent',(msg)=>{
            this.snackbar=true
            this.msg=msg
        })
    }
}
</script>