<template>
    <v-overlay :value="isLoading" :opacity="opacity">
        <v-progress-circular indeterminate></v-progress-circular>
    </v-overlay>
</template>

<script>

export default {
    name:'Loading',
    data(){
        return{
            opacity:0.6,          
        }
    },
    computed:{
        isLoading:function(){
            return this.$store.getters['dashboard/loading']
        }
    }

}
</script>