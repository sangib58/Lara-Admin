<template>
    <v-container>
      <v-layout row wrap>
        <v-flex xs12>
          <v-card height="400" color="transparent" flat>
            <div class="display-3 mt-5">Page not found</div>
            <div class="grey--text lighten-5">
              The page you are trying to get to never existed in this reality,
              or has migrated to a parallel universe.
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
    name:'NotFound',
    created(){
        this.$store.dispatch('dashboard/signOut')
    }
}
</script>
<style scoped>
  .paragraph-text {
    font-size: 18px;
  }
</style>