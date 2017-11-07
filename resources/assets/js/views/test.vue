<template>
     <div>
         <button @click="beginEditPhoto(1)">upload</button>
         <button @click="beginEditPhoto(0)">remove</button>
         <photo-editor :user_id="photoEditorSettings.user_id" 
            :entity_type="photoEditorSettings.entity_type" :entity_id="photoEditorSettings.entity_id"
            :action="photoEditorSettings.action" :show="photoEditorSettings.show"
            @canceled="onCancelEditPhoto" @photo-updated="onPhotoUpdated"
            @photo-update-failed="onPhotoUpdateFailed">

         </photo-editor>
        
     </div>
</template>

<script>
   
    export default {
        name:'Test',
        components: {
          
        },
        data() {
            return {
                ready:false,
                
                photoEditorSettings:{
                    user_id:1,
                    entity_type:'user',
                    entity_id:14,
                    action:'upload',
                    show:false
                },
                showAlert:false,
                
                
           
                

                title_text:'課程審核'
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               
            },
            beginEditPhoto(val){
                if(val){
                    this.photoEditorSettings.action='upload'
                    this.photoEditorSettings.show=true
                }else{
                    this.photoEditorSettings.action='delete'
                    this.photoEditorSettings.show=true
                }
            },
            onCancelEditPhoto() {
                this.photoEditorSettings.show=false
            }, 
            onPhotoUpdated(photoId){
                this.onCancelEditPhoto()

                let msg = '刪除相片成功'
                if(photoId){
                    msg = '更新相片成功'
                }
                Helper.BusEmitOK(msg)
            },
            onPhotoUpdateFailed(photoId){
                this.onCancelEditPhoto()

                let title = '刪除相片失敗'
                if(photoId){
                    title = '更新相片失敗'
                }
                Helper.BusEmitError(error,title)    
            },
            
            
            
            
            
        }
    }
</script>
