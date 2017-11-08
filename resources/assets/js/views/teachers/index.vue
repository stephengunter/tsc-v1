<template>
<div>
    <div class="panel panel-default" >
        <div class="panel-heading">
            <options-filter :params="params" :options="options"
              @ready="onFilterReady"  @param-changed="onParamChanged"
              @clear-parent="clearParent">
            </options-filter>
        </div> <!--  End Head  -->
    </div>
     
    <teacher-list v-if="ready" :search_params="params" :version="version"  
        :no_page="listSettings.no_page"   :can_select="listSettings.can_select"
        :hide_create="hide_create" 
       
        @details="OnDetails"
        @data-loaded="onTeachersLoaded"
        @selected="onSelected" @begin-create="onBeginCreate"
        @group-selected="onGroupSelected">
    </teacher-list>

</div>

</template>

<script>
    import TeacherList from '../../components/teacher/list.vue'
    

    export default {
        name: 'TeacherIndex',       
        components: {
            'teacher-list':TeacherList
        },
        props: {
            center_options:{
                type: Array,
                default: []
            },
            version: {
                type: Number,
                default: 0
            },
            hide_create:{
                type: Boolean,
                default: false
            },
            group:{
                type: Boolean,
                default: false
            }

        },
        data() {
            return {
                
                ready:false,
                parentTeacher:null,
               

                params:{
                    center:0,
                    group:this.group,
                },
                

                listSettings:{
                    no_page:false,
                  
                    can_select:false,
                    hide_create:false,
                }
                
             
            }
        },
        beforeMount() {
            this.options={
                centerOptions:this.center_options
            }
            
            this.params.group = this.group ? 1 : 0 
            
            this.init()
        },
        computed:{
            
        }, 
        methods: {
            init(){
                this.listSettings.hide_create=this.hide_create
            },
            onFilterReady(params){
                for(let property in params){
                    this.params[property]=params[property]
                }

                this.ready=true
                
            },
            onTeachersLoaded(data){
               this.parentTeacher = data.parentTeacher
            },
            onParamChanged(params){
                for(let property in params){
                    this.params[property]=params[property]
                } 
               
            },
            clearParent(params){
                this.parentTeacher=null
                this.onParamChanged(params)
            },
            OnDetails(id){
                this.$emit('details',id)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onGroupSelected(id){
                this.params.parent=id  
            },
            onBeginCreate(){
                let parent=0
                if(this.parentTeacher) parent= this.parentTeacher.id
                this.$emit('begin-create',parent)
            },
            
            
            
        },

    }
</script>

