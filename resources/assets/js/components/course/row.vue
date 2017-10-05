<template>
   <tr>
        <td v-if="remove">
            <button @click="removeItem(course.id,course.name)" class="btn btn-danger btn-xs">
             <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </td>
        <td v-if="select">
             <checkbox :default="been_selected"
             @selected="selected(course.id, course.number, course.name)"
              @unselected="unselected(course.id)" ></checkbox>
        </td>
        <td v-text="course.center.name"></td>   
        
        <td v-text="course.number"></td>   
        <td>
            <a herf="#" @click.prevent="selected(course.id)">
                   {{ course.name }}
            </a>
        </td> 
        <td v-if="!more">
            <a v-if="isParentGroup(course)" @click="onGroupSelected(course.id)" 
            v-html="$options.filters.okSign(true)" class="btn">
            </a>   
            <span v-if="isGroupSubCourse(course)" v-text="parentCourseName(course)" ></span>
  

        </td> 
        <td v-if="!more" v-html="getClassTimesText(course.class_times)"></td> 
        <td v-if="!more" v-html="period(course.begin_date , course.end_date)"></td> 
        <td v-if="!more" v-html="period(course.open_date , course.close_date)"></td> 
        <td v-if="!more" v-html="$options.filters.activeLabel(course.active)" ></td> 

        <td v-if="more"  v-html="teachersText(course.teachers)"></td> 
        <td v-if="more" v-text="course.credit_count"></td>
        <td v-if="more" v-text="course.weeks"></td>
        <td v-if="more" v-text="course.hours"></td>
        <td v-if="more">{{ course.tuition | formatMoney }}</td>
        <td v-if="more" v-text="course.materials">
        <td v-if="more">{{ course.cost | formatMoney }}</td>
        <td v-if="more" v-text="course.limit"></td>
        <td v-if="more" v-text="course.min"></td>
    </tr>                   
       
</template>
<script>
  
    export default {
        name: 'CourseRow',
        props: {
            course: {
               type: Object,
               default: null
            },
            more:{
               type: Boolean,
               default: false
            },
            remove:{
               type: Boolean,
               default: false
            },
            select:{
               type: Boolean,
               default: false
            },
            been_selected:{
                type: Boolean,
               default: false
            }
            
        },
        data() {
            return {
                thead:[],
            }
        },
        beforeMount(){
            
        },
        
        methods: {
            
            teachersText(teachers){
                 return Course.teachersText(teachers)             
            },
            getClassTimesText(class_times){
                return Course.getClassTimesText(class_times)             
            },
            isParentGroup(course){
                return Course.isParentGroup(course)
            },
            isGroupSubCourse(course){
                 return Course.isGroupSubCourse(course)
            },
            parentCourseName(course){
                 if(course.parentCourse) return course.parentCourse.name
                    return ''
            },
            onGroupSelected(id){
               this.$emit('group-selected',id)
            },
            period(begin,end){
               return Helper.period(begin,end)
            },
            removeItem(id,name){
                let values={
                    id:id,
                    name:name
                }
                this.$emit('remove-clicked',values)
            },
            selected(id,number,name){
                this.$emit('selected',id,number,name)
            },
            unselected(id){
                 this.$emit('unselected',id)
            }
            
            
        },
        
    }
</script>