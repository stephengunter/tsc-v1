<template>
<p class="updated" v-if="!entity.updated_by">
    {{   entity.updated_at|tpeTime  }}
</p>
<p class="updated" v-else>
   <a  href="#" @click.prevent="showUpdatedBy" >
      {{   entity.updated_at|tpeTime  }}
   </a>
</p> 
</template>


<script>
  export default {
      name: 'Updated',
      props: {
          entity: {
             type: Object,
             default: null
          },
      },
      
      
      methods: {
          showUpdatedBy(){
                Bus.$emit('onShowEditor',parseInt(this.entity.updated_by))
          },
      }
  }
</script>
<style scoped>
  .updated{
      font-size: 0.8em;
  }
</style>