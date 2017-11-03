<template>
   <tr> 
        <!--  Static Columns -->
        <td v-if="can_remove">
            <button @click="removeItem(course.id,course.name)" class="btn btn-danger btn-xs">
               <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </td>
        <td v-if="single_select">
            single_select
            <!-- <checkbox :default="been_selected"
              @selected="selected(course.id, course.number, course.name)"
              @unselected="unselected(course.id)" >
            </checkbox> -->
        </td>
        <td v-if="multi_select">
            <checkbox :default="been_selected"
              @selected="checked(course.id)"
              @unselected="unchecked(course.id)" >
            </checkbox>
        </td>
        <!-- 開課中心 -->
        <td v-text="course.center.name"></td>  

        <!-- 編號 -->
        <td v-if="editting_number" >  
            {{  course.default_number }}     
            <input @keydown="clearErrorMsg(index)" type="text" name="course.custom_number" class="form-control" v-model="course.custom_number">
    
            <small class="text-danger" v-text="course.error"></small>             
        </td> 
        <td v-else>
           {{ course.number }}
        </td>
        
        <!-- 名稱 -->
        <td  v-if="details_link">
            <a herf="#" @click.prevent="details(course.id)">
                   {{ course.name }}
            </a>
        </td> 
        <td  v-else>
            {{ course.name }}
        </td>
        <!-- End Static Columns -->



        <!-- Default Columns -->
        
        <!-- 課程分類 -->
        <td v-show="viewing==0"  v-html="categoriesText(course.categories)">
        </td> 

        <!-- 群組課程 -->
        <td v-show="viewing==0">
            <a v-if="isGroup(course)" @click="onGroupSelected" 
                v-html="$options.filters.okSign(true)" class="btn btn-sm">
            </a>   
            <span v-if="hasParent(course)" v-text="parentCourseName(course)" ></span>
        </td> 

        <!-- 上課時間 -->
        <td  v-show="viewing==0" v-html="getClassTimesText(course.class_times)">
        </td> 
        <!-- 課程日期 -->
        <td v-show="viewing==0" v-html="period(course.begin_date , course.end_date)">
        </td> 
        <!-- 審核 -->
        <td  v-show="viewing==0" v-html="$options.filters.reviewedLabel(course.reviewed)" >
        </td>

        <!-- End Default Columns --> 

        <!-- viewing==1 --> 

        <!-- 教師 --> 
        <td v-show="viewing==1"  v-html="teachersText(course.teachers)">
        </td> 
        <!-- 學分數 --> 
        <td v-show="viewing==1" v-text="course.credit_count">
        </td>
        <!-- 學分單價 --> 
        <td v-show="viewing==1" >
            {{ course.credit_price | formatMoney }}
        </td>
        <!-- 週數 --> 
        <td v-show="viewing==1" v-text="course.weeks">            
        </td>
        <!-- 時數 --> 
        <td v-show="viewing==1" v-text="course.hours">            
        </td>

        <!-- 學費 --> 
        <td v-show="viewing==1">
            {{ course.tuition | formatMoney }}            
        </td>


        <!-- viewing==2 --> 
        <!-- 材料 --> 
        <td v-show="viewing==2" >
            {{ course.materials}}
        </td>    
        <!-- 材料費 --> 
        <td v-show="viewing==2">
            {{ course.cost | formatMoney }}   材料費
        </td>

        <!-- 報名日期 --> 
        <td v-show="viewing==2" v-html="period(course.open_date , course.close_date)">            
        </td>

        <!-- 人數上限 --> 
        <td  v-show="viewing==2" >
            {{ course.limit}}
        </td>
        <!-- 最低人數 --> 
        <td  v-show="viewing==2" >
             {{ course.min}}
        </td>
        
    </tr>                   
       
</template>
<script>
  
    export default {
        name: 'CourseRow',
        props: {
            index:{
               type: Number,
               default: 0
            },
            viewing:{
               type: Number,
               default: 0
            },
            course: {
               type: Object,
               default: null
            },
            details_link:{
               type: Boolean,
               default: true
            },
            can_remove:{
               type: Boolean,
               default: false
            },
            single_select:{
               type: Boolean,
               default: false
            },
            multi_select:{
               type: Boolean,
               default: false
            },
            been_selected:{
                type: Boolean,
                default: false
            },
            editting_number:{
                type: Boolean,
                default: false
            },
            
        },
        data() {
            return {
               
            }
        },
        beforeMount(){
            
        },
        
        methods: {
            
            teachersText(teachers){
                 return Course.teachersText(teachers)             
            },
            categoriesText(categories){
                 return Course.categoriesText(categories)             
            },
            getClassTimesText(class_times){
                return Course.getClassTimesText(class_times)             
            },
            isGroup(course){
                return Course.isGroup(course)
            },
            hasParent(course){
                return Course.hasParent(course)
            },
            parentCourseName(course){
                 if(course.parentCourse) return course.parentCourse.name
                    return ''
            },
            
            period(begin,end){
               return Helper.period(begin,end)
            },
            details(id){
                this.$emit('details',id)
            },
            removeItem(id,name){
                let values={
                    id:id,
                    name:name
                }
                this.$emit('remove-clicked',values)
            },
            onGroupSelected(){
                let parent = 0
                if(Course.hasParent(this.course)){
                    parent=this.course.parent
                }else{
                    parent =this.course.id
                }
                
                this.$emit('group-selected',parent)
            },
            onSelected(){
                 this.$emit('selected',this.course)
            },
            checked(id){
                this.$emit('checked',id)
            },
            unchecked(id){
                 this.$emit('unchecked',id)
            },
            clearErrorMsg(index){
                this.$emit('clear-error',index)
            },
            
            
        },
        
    }
</script>