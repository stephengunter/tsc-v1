<template>
<div>
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                <h4 v-html="title"></h4>                
            </div>
            <div  class="form-inline" slot="header">
                <select  v-model="searchParams.center"  style="width:auto;" class="form-control selectWidth">
                      <option v-for="(item,index) in centerOptions" :key="index" :value="item.value" v-text="item.text"></option>
                </select>
    
            </div>
            <div>
                <button :disabled="!canSubmit" @click.prevent="submit" class="btn btn-success" >
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 審核通過
                </button>
                
            </div>
        </div>
        <div class="panel-body">
            <table v-if="loaded" class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <checkbox  :default="false"
                                @selected="checkAll"   @unselected="unCheckAll">                             
                            </checkbox>
                        </th>
                        <th v-for="(item,index) in thead" :key="index"> {{ item.title }} </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(teacher,index) in teacherList" :key="index">
                        <td>
                            
                            <checkbox :value="teacher.user_id" :default="beenChecked(teacher.user_id)"
                                @selected="onChecked"   @unselected="unChecked">
                             
                            </checkbox>
                        </td> 
                        <td><a herf="#" @click="onSelected(teacher.user_id)">{{teacher.user.profile.fullname}}</a> </td>
                        <td>
                          
                          <span v-if="isGroup(teacher)" v-html="$options.filters.okSign(true)"></span>
        
                        </td>
                        <td>{{teacher.user.phone}}</td>
                        <td>{{teacher.specialty}}</td>
                        
                        <td v-html="$options.filters.namesText(teacher.centerNames)"></td>
                        <td v-if="false" v-html="$options.filters.activeLabel(teacher.active)" ></td>
                        <td v-html="$options.filters.reviewedLabel(teacher.reviewed)" ></td> 
                        <td>{{ teacher.updated_at | tpeTime}}</td> 
                    </tr>
	                    
                   
                </tbody>  
            </table>
        </div>
    </div>

    <modal :showbtn="modalSettings.showBtn"  :show.sync="modalSettings.show" :large="true"  @closed="onCloseModal" effect="fade" width="800">   
    
        <div slot="modal-header" class="modal-header">
       
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onCloseModal">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
            
        </div>
        <div slot="modal-body" class="modal-body">
            <show v-if="selected"  :id="selected"
                :can_back="showSettings.can_back" :can_edit="showSettings.can_edit"
                @edit-review="onEditReview">      
                           
            </show>
        </div>
    </modal>

    <review-editor :showing="reviewEditor.show" :reviewed="reviewEditor.reviewed"
      @close="reviewEditor.show=false" @save="updateReview">
    </review-editor>

</div>    
</template>
    
<script>
    import Show from '../../components/teacher/show.vue'
    export default {
        name: 'TeacherReview',  
        components: {
            Show          
        },
        props: {
            version: {
                type: Number,
                default: 0
            },
            
        },
        data() {
            return {
                loaded:false,
                title:Helper.getIcon('Teachers')  + '  教師審核',
                thead:[{
                    title: '姓名/名稱',
                    key: 'name',
                    sort: false,
                    default: true
                },{
                    title: '群組',
                    key: 'group',
                    sort: false,
                    default: true
                }, {
                    title: '手機',
                    key: 'user.phone',
                    sort: false,
                    default: true
                }, {
                    title: '專長',
                    key: 'specialty',
                    sort: true,
                    default: true
                }, {
                    title: '所屬中心',
                    key: 'centers',
                    sort: false,
                    default: true

                }, {
                    title: '審核',
                    key: 'reviewed',
                    sort: true,
                    default: true
                }, {
                    title: '更新時間',
                    key: 'updated_at',
                    sort: true,
                    default: true
                }],
                searchParams:{
                    center : 0,
                   
                },

                checked_ids:[],

                teacherList:[],
                centerOptions:[],

                

                modalSettings:{
                    show:false,
                    showBtn:false

                },
                showSettings:{
                    can_edit:false,
                    can_back:false

                },

                reviewEditor:{
                    show:false,
                    reviewed:false
                },

                selected:0
                
            }
        },
        computed: {
            canSubmit() {
               return  this.checked_ids.length > 0
            },
            
        },
        watch: {
          version() {
             this.init()
          },
        
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                this.loaded=false
                this.selected=0
                this.checked_ids=[]
                this.teacherList=[]

                this.modalSettings.show=false
                this.reviewEditor.show=false
                
                this.fetchData()                
            },
            fetchData(){
                let center=this.searchParams.center
                let getData = TeacherReview.index(center)
                getData.then(data => {
                          
                        this.teacherList=data.teacherList
                        if(data.centerOptions.length){
                            this.centerOptions=data.centerOptions
                            this.searchParams.center=this.centerOptions[0].value
                        }
                        
                        this.loaded=true
                    }).catch( error => {
                        Helper.BusEmitError(error)           
                    })
            },
            isGroup(teacher){
                return Helper.isTrue(teacher.group)
            },
            beenChecked(id){
                return this.checked_ids.includes(id)
            },
            onChecked(id){
                if(!this.beenChecked(id))  this.checked_ids.push(id) 
            },
            unChecked(id){
                let index= this.checked_ids.indexOf(id)
                if(index >= 0)  this.checked_ids.splice(index, 1) 
                
            },
            checkAll(){
                if(this.teacherList.length)
                {
                    for(let i=0; i<this.teacherList.length ; i++){
                        this.onChecked(this.teacherList[i].user_id)
                    }
                }
            },
            unCheckAll(){
                this.checked_ids=[]
            },
            onSelected(id){
                this.selected=id
                this.modalSettings.show=true
               
            },
            submit(){
                let save = TeacherReview.store(this.checked_ids)
                save.then(data => {
                        this.$emit('saved')
                    }).catch( error => {
                        Helper.BusEmitError(error,'存檔失敗')           
                    })
            },
            onCloseModal(){
                this.modalSettings.show=false
            },
            onEditReview(){
                this.reviewEditor.show=true     
            },
            updateReview(val){
                let id = this.selected 
                let review= val
                let save= TeacherReview.update(id,review)

                save.then(teacher => {
                    Helper.BusEmitOK('存檔成功')
                    this.init()
                   
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')
                    this.showReviewEditor=false   
                })
            }
            
            
        },

    }
</script>