import './bootstrap'

   
new Vue({

    el: '#app',
    data() {
        return {
            loaded:false,
            isAuth:false,
            showAlert: false,
            showLogin:false,
            alertSetting: {
                type: 'success',
                title: '資料儲存成功',
                text: '',
                dismissable: false,
                duration: 2500,
                class: 'fa fa-check-circle-o'
            },

            showUpdatedBy:false,
            editor:{
                updated_by:'',
                title:'最後更新者'
            }
        }
    },
    computed:{
        showView(){
            
            return true
        }
    },
    created() {
        document.getElementsByTagName("body")[0].removeAttribute("style");
        Bus.$on('login',this.beginLogin)

       
        Bus.$on('errors',this.showErrorMsg)
        Bus.$on('okmsg',this.showSuccessMsg)       
         
        Bus.$on('onShowEditor', function(updated_by,title){
                 this.editor.updated_by=updated_by
                 if(title) this.editor.title=title
                 this.showUpdatedBy=true
              }.bind(this));
     
    },
    beforeMount() {
           this.init()
    },
    

    methods: {
        init(){
            
            this.isAuth=false

            this.showUpdatedBy=false
            this.updated_by=''

          
        },
        setAuth(auth){
            this.isAuth=auth
        },
        beginLogin(){
            this.showLogin=true
        },
        logined(){
            this.showLogin=false
        },
        closeAlert() {
            this.showAlert = false;
        },
        setAlertText(msg) {
            let title = msg.title ? msg.title : '處理您的要求時發生錯誤'
            let text = msg.text
            this.alertSetting.title = title
            this.alertSetting.text = text
        },
        showErrorMsg(error) {
            let msg = {}
            if (error.status == 500) {
                msg = {
                    title: '處理您的要求時發生錯誤',
                    text: '系統暫時無回應，請稍後再試'
                }
            }else if(error.status == 404){
                msg = {
                    title: '查無資料',
                    text: ''
                }
            }else {
                msg = {
                    title: error.title,
                    text: error.text
                }
            }
            this.setAlertText(msg);
            this.alertSetting.class = 'fa fa-exclamation-circle'
            this.alertSetting.type = 'danger'

            this.showAlert = true;
            this.showModal = false;
        },
        showSuccessMsg(msg) {
            this.setAlertText(msg);
            this.alertSetting.class = 'fa fa-check-circle-o'
            this.alertSetting.type = 'success'

            this.showAlert = true;
            this.showModal = false;
        },

        
        endShowUpdatedBy(){
            this.showUpdatedBy=false
        },

        
        

    },
    

})