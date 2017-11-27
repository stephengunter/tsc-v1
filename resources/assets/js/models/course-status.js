class CourseStatus {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        

    }
    static title(){
       return 'Statuses'
    }
    static source(){
        return '/statuses'
    }
    static storeUrl(){
         return this.source()
    }
    static showUrl(id){
         return this.source() + '/' + id
    }
    static editUrl(id){
         return this.showUrl(id) +  '/edit'
    }
    static updateUrl(id){
        return this.showUrl(id)
    }
    
    static show(id){
        return new Promise((resolve, reject) => {
            let url = this.showUrl(id) 
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
    }
    static edit(id){
        let url = this.editUrl(id) 
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
    }
    static update(form , id){
         let url =this.updateUrl(id) 
         let method='put'
        return new Promise((resolve, reject) => {
            form.submit(method,url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }
   
    static getSignupLabel(data){
        let label=''
        switch (Number(data.signup)) {
            case -3 :
            label = '<span class="label label-danger">報名已額滿</span>'
            break
            case -2 :
            label = '<span class="label label-default">報名已截止</span>'
            break
            case -1 :
                label = '<span class="label label-default">報名尚未開始</span>'
            break
            case 0 :
                label = '<span class="label label-danger">報名已停止</span>'
            break
            case 1 :
                label = '<span class="label label-success">報名進行中</span>'
            break
           

        }
        return label
    }
    static getRegisterLabel(data){       
      
        if(Number(data.register)){
             return '<span class="label label-success">已完成</span>'
        }else{
            return '<span class="label label-default">未完成</span>'
        }
        
    }
    
    static getClassLabel(data){
        let label=''
        switch (Number(data.class)) {
            case -1 :
                label = '<span class="label label-default">尚未開課</span>'
            break
            case 0 :
                label = '<span class="label label-danger">停止開課</span>'
            break
            case 1 :
                label = '<span class="label label-success">進行中</span>'
            break
            case 2 :
                label = '<span class="label label-default">已結束</span>'
            break

        }
        return label
    }
    
    
   
    

}


export default CourseStatus;