<template>
    <tr>
        
        <td v-if="single_select">
            single_select
            <!-- <checkbox :default="been_selected"
              @selected="selected(teacher.id, teacher.number, teacher.name)"
              @unselected="unselected(teacher.id)" >
            </checkbox> -->
        </td>
        <td v-if="multi_select">
            <checkbox :default="been_selected"
              @selected="checked(teacher.user_id)"
              @unselected="unchecked(teacher.user_id)" >
            </checkbox>
        </td> 
        
        <td>
            <a v-if="details_link" href="#" @click.prevent="details(teacher.user_id)">
                {{ teacher.user.profile.fullname }}
            </a> 
            <span v-else>{{ teacher.user.profile.fullname }}</span>
        </td>
        <td >
            
            <span v-if="isGroup(teacher)" v-html="$options.filters.okSign(true)"></span>

        </td>
        <td v-show="!group"> {{ teacher.user.phone }} </td>
        <td v-show="!group"> {{ teacher.specialty }} </td>
        
        <td v-html="$options.filters.namesText(teacher.centerNames)"></td>
        <td v-if="false" v-html="$options.filters.activeLabel(teacher.active)" ></td>
        <td v-html="$options.filters.reviewedLabel(teacher.reviewed)" ></td> 
        <td>{{ teacher.updated_at | tpeTime}}</td> 

        <td v-if="can_remove">
            <button @click="removeItem" class="btn btn-danger btn-xs">
               <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </td>
        
    </tr>                   
       
</template>
<script>
  
    export default {
        name: 'TeacherRow',
        props: {
            group:{
               type: Boolean,
               default: false
            },
            index:{
               type: Number,
               default: 0
            },
            viewing:{
               type: Number,
               default: 0
            },
            teacher: {
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
            can_select_group:{
                type: Boolean,
                default: true
            }
            
        },
        data() {
            return {
               
            }
        },
        beforeMount(){
            
        },
        
        methods: {
            
            teachersText(teachers){
                 return Teacher.teachersText(teachers)             
            },
            categoriesText(categories){
                 return Teacher.categoriesText(categories)             
            },
            getClassTimesText(class_times){
                return Teacher.getClassTimesText(class_times)             
            },
            canSelectGroup(teacher){
                if(!this.can_select_group) return false
                return this.isGroup(teacher)
            },
            isGroup(teacher){
                return Helper.isTrue(teacher.group)
            },
            hasParent(teacher){
                return Teacher.hasParent(teacher)
            },
            parentTeacherName(teacher){
                 if(teacher.parentTeacher) return teacher.parentTeacher.name
                    return ''
            },
            activeLabel(active){
                return Teacher.activeLabel(active)
            },
            period(begin,end){
               return Helper.period(begin,end)
            },
            details(id){
                this.$emit('details',id)
            },
            removeItem(){
                let values={
                    id:this.teacher.user_id,
                    name:this.teacher.user.profile.fullname
                }
                this.$emit('remove-clicked',values)
            },
            onGroupSelected(){
                let parent = 0
                if(Teacher.hasParent(this.teacher)){
                    parent=this.teacher.parent
                }else{
                    parent =this.teacher.id
                }
                
                this.$emit('group-selected',parent)
            },
            onSelected(){
                 this.$emit('selected',this.teacher)
            },
            checked(id){
                this.$emit('checked',id)
            },
            unchecked(id){
                 this.$emit('unchecked',id)
            },
            hasError(key){
                if(this.teacher.numberError) return true
                return false
            },
            clearNumberError(index){
                this.$emit('clear-error',index , 'number')
            },
            
            
        },
        
    }
</script>