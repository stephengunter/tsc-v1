<template>
  <div class="panel-body">
      <div class="row">
         <div class="col-sm-4">
            <h3>應繳金額： {{ bill.amount | formatMoney }} <span v-html="getDiscountText()"></span></h3>
            
         </div> 
         <div class="col-sm-8">
            <h3>已繳金額： <span style="color:red"> {{ bill.amount | formatMoney }} </span>

              &nbsp; &nbsp;
             <span v-if="bill.payed()" class="label label-success"> 已繳費 </span>
             <span v-else class="label label-success"> 已繳費 </span>
            
            
            </h3>
            
         </div> 
         
      </div>  <!-- End Row -->
      <div class="row">
         <div class="col-sm-12">
            <h3>報名課程：</h3>
            <table class="table table-striped">
               <thead>
                  <tr>
                        <th>報名日期</th>
                        <th>課程名稱</th>
                        <th>課程費用</th>
                  </tr>
               </thead>
               <tbody>
                  <tr v-for="(signup,index) in bill.signups" :key="index" >
                        <td>{{ signup.date }}</td>
                        <td>{{ signup.course.fullname }}</td>
                        
                        <td>{{ signup.tuition | formatMoney}}</td>
                       
                  </tr>
               </tbody>
             </table>
         </div>
      </div>  <!-- End Row -->
      <div class="row">
         <div class="col-sm-12">
            <h3>繳費紀錄：</h3>
            <table class="table table-striped">
               <thead>
                  <tr>
                        <th>繳費日期</th>
                        <th>金額</th>
                        <th>繳費方式</th>
                        <th>備註</th>
                  </tr>
               </thead>
               <tbody>
                  <tr v-for="(tuition,index) in bill.tuitions" :key="index" >
                        <td>{{ tuition.date }}</td>
                        <td>{{ tuition.amount | formatMoney}}</td>
                        <td>{{ tuition.paywayText }}</td>
                        <td>{{ tuition.ps }}</td>
                       
                  </tr>
               </tbody>
             </table>
         </div>
      </div>  <!-- End Row -->
   </div>  
</template>
<script>
   export default {
      name: 'ShowBill',
      props: {
         bill:{
            type:Object,
            default:null
         }
      },
      methods: { 
         getDiscountText(){
            return ''
         }
      }
   }
</script>