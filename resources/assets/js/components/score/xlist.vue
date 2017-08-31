<template>
<div>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder" :default_direction="defaultDirection"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :no_page="no_page" :show_title="show_title"  :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="current_version"  
       @dataLoaded="onDataLoaded">

       <!-- <label  slot="btn"  class="btn btn-sm btn-warning  btn-default btn-file">
          匯入
          <input type="file" name="scores_file" style="display: none;"  
           @change="onFileChange" >
       </label>
        -->
         <template scope="props">
            <!-- <row  :student="props.item"   > 
               
            </row> -->
             <tr>
                <td><a herf="#" @click="selected(props.item.id)">{{ props.item.title }}</a> </td>
                <td v-text="textContent(props.item.content)"></td>
                <td v-html="$options.filters.okSign(props.item.public)" ></td> 
                <td  v-html="$options.filters.okSign(props.item.emails)" ></td> 
               
             
                </td>
                
                <td>{{ props.item.created_at | strTime }}</td>  
            </tr>
         </template>

    </data-viewer>

    

</div>
</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'ScoreList',
        components: {
           Row,
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            creating:{
              type: Boolean,
              default: false
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_select:{
               type: Boolean,
               default: false
            },
            no_page:{
               type: Boolean,
               default: false
            },
            show_title:{
               type: Boolean,
               default: true
            },
        },
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon(Score.title())  + '  學員成績',
                loaded:false,

                source:Score.source(),
                
                
                current_version:0,
                             
                createText: '',
                thead:[],
                filter: [],
                defaultSearch:'id',
                defaultOrder:'number',
                defaultDirection:'asc',
                
                searchParams:{
                   course:0
                },
             
                hasData:false,
              

                rowSettings:{
                    creating:false,
                    can_select:false,
                    show_updated:false,
                    can_edit:true
                },

                 files: [],
             
            }
        },
        computed: {
            isSaving() {
              return false
            },
        },
        watch: {
          version() {
             this.current_version+=1
          },
          course_id() {
             this.current_version+=1
          }
        },
        methods: {
            init() {
                
                this.thead=Score.getThead(this.rowSettings.show_updated)
                this.searchParams.course=this.course_id 
                
                
            },
            beginImport(){
               alert('beginImport')
            },
            onFileChange(e) {
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.files = e.target.files;

                this.submitImport();
            },
            submitImport() {
                this.submitting = true
                let form = new FormData();
               

                for (let i = 0; i < this.files.length; i++) {
                    form.append('score_file', this.files[i]);
                }

                let store=Score.import(form)
                store.then(result => {
                        // this.$emit('uploaded', photo)
                        // this.removeImage()
                        // this.submitting = false
                    })
                    .catch(error => {
                        // this.removeImage()
                        // Helper.BusEmitError(error,'上傳失敗')
                        // this.submitting = false
                    })
            },
            onDataLoaded(data){
                // this.course=data.course
                // this.$emit('loaded',data)

            }, 
            
            
            
           
        },

    }
</script>

<style scoped>
  .btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
</style>