<template>
    <table class="table table-striped">
        <thead>
            <tr>
                <th v-for="item in thead" v-if="item.default" v-bind:style="{ width: item.width }" >
                    {{item.title}}
                </th>
               
            </tr>
        </thead>
        <tbody>
           <tr v-for="course in courses">
                <td v-if="remove">
                    <button @click="removeItem(course.id,course.name)" class="btn btn-danger btn-xs">
                     <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </button>
                </td>
                <td v-if="select">
                     <checkbox @selected="selected(course.id)" @unselected="unselected(course.id)" ></checkbox>
                </td>
                <td v-text="course.center.name"></td>   
                
                <td v-text="course.number"></td>   
                <td v-text="course.name"></td> 
                <td v-if="!more" v-html="teachersText(course.teachers)"></td> 
                <td v-if="!more" v-html="getClassTimesText(course.class_times)"></td> 
                <td v-if="!more" v-html="period(course.begin_date , course.end_date)"></td> 
                <td v-if="!more" v-html="period(course.open_date , course.close_date)"></td> 
                <td v-if="!more" v-html="$options.filters.activeLabel(course.active)" ></td> 

                <td v-if="more" v-text="course.credit_count"></td>
                <td v-if="more" v-text="course.weeks"></td>
                <td v-if="more" v-text="course.hours"></td>
                <td v-if="more">{{ course.tuition | formatMoney }}</td>
                <td v-if="more" v-text="course.materials">
                <td v-if="more">{{ course.cost | formatMoney }}</td>
                <td v-if="more" v-text="course.limit"></td>
                <td v-if="more" v-text="course.min"></td>
            </tr>                   
        </tbody>
    </table>
</template>
<script>
  
    export default {
        props: ['courses','more','remove','select'],
        name: 'CourseTable',
        components:{
             'checkbox':CheckBox,
        },
        watch: {
            'more': 'changeView'
        },
        data() {
            return {
                thead:[],
            }
        },
        beforeMount(){
            this.thead=CourseScripts.getThead()
             let head={
                    title: '',
                    key: '',
                    sort: false,
                    static:true,
                    default:true
                }
            if(this.remove){
                this.thead.splice(0, 0, head);
            }
            if(this.select){
              
                this.thead.splice(0, 0, head);
            }
        },
        
        methods: {
            
            teachersText(teachers){
                 return CourseScripts.teachersText(teachers)             
            },
            getClassTimesText(class_times){
                return CourseScripts.getClassTimesText(class_times)             
            },
            showCreateBtn() {
                if (this.createText)   return true
                return false
            },
            period(begin,end){
               return Helper.period(begin,end)
            },
            changeView(){
                for (var i = this.thead.length - 1; i >= 0; i--) {
                    if(!this.thead[i].static){
                        this.thead[i].default = !this.thead[i].default
                    }
                    
                }
            },
            removeItem(id,name){
                let values={
                    id:id,
                    name:name
                }
                this.$emit('removeClicked',values)
            },
            selected(id){
                this.$emit('selected',id)
            },
            unselected(id){
                 this.$emit('unselected',id)
            }
            
            
        },
        
    }
</script>