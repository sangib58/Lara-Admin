<template>
    <v-container>
      <v-layout row wrap>
        <v-flex xs12>
          <v-card height="400" color="transparent" flat>
            <div class="display-3 mt-5">Entry Restricted</div>
            <div class="grey--text lighten-5">
              You have no access here.You are trying something which access has limited for you. 
            </div>
            <div class="paragraph-text mt-2">
              <br />
              <br />Try going back to sign in page by clicking <router-link to="/">here</router-link>.                        
            </div>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>  
</template>

<script>
export default {
    name:'Forbidden',
    created(){
      this.$store.dispatch('dashboard/signOut')
      //this.$store.dispatch('dashboard/cancelLoading')
    }
}
</script>

<style scoped>
.paragraph-text {
    font-size: 18px;
}
</style>