<template>
    <modal :showBtn="true"  :show.sync="showing" @ok="onConfirmed"  @closed="onClose" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onClose">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="message"> </h3>
        </div>
    </modal>
    
</template>
<script>
    export default {
        name:'DeleteConfirm',
        props: {
            showing:{
               type: Boolean,
               default: true
            },  
            message: {
              type: String,
              default: '確定要刪除嗎?'
            },
        },
        methods: {
            onClose(){
                this.$emit('close')
            },
            onConfirmed(){
                this.$emit('confirmed')
            }
            
        }
    }
</script>
