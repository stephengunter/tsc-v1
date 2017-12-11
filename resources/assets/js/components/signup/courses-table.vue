<template>
   <table class="table table-striped">
      <thead>
         <tr>
               <th>課程編號</th>
               <th>課程名稱</th>
               <th>上課時間</th>
               <th>課程期間</th>
               <th>課程費用</th>
               <th v-if="can_remove"></th>
         </tr>
      </thead>
      <tbody>
         <tr v-for="(course,index) in courses" :key="index" >
               <td>{{ course.number }}</td>
               <td>{{ course.fullname }}</td>
               <td v-html="course.classTimesText"></td>
               <td>{{ course.period }}</td>
               <td>{{ course.tuition | formatMoney}}</td>
               <td>
                  <button @click.prevent="removeItem(course.id)" v-if="can_remove" class="btn btn-danger btn-xs">
                     <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                  </button>
               </td>
         </tr>
      </tbody>
   </table>
</template>

<script>
export default {
   name: 'SignupCoursesTable',
   props: {
      courses:{
         type: Array,
         default: []
      },
      can_remove:{
         type: Boolean,
         default: true
      },
   },
   methods: {
      removeItem(id){
         
         this.$emit('remove-course',id)
      },
   }
}
</script>

