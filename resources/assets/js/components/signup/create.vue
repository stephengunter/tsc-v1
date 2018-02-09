<template>
  
</template>

<script>
export default {
   name: 'CreateSignup',
   props: {
      course_id:{
         type: Number,
         default: 0
      },
      user_id:{
         type: Number,
         default: 0
      },
      can_pay:{
         type: Boolean,
         default: true
      },
   },
}
</script>

