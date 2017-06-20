class CourseStatus {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        this.signupLabel=CourseStatus.getSignupLabel(data)
        this.registerLabel=CourseStatus.getRegisterLabel(data)
        this.classLabel=CourseStatus.getClassLabel(data)

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
   
    static signupLabel(data){
        status=Helper.tryParseInt(data.signup)
        label=''
        switch (status) {
            case -1 :
                label = '<span class="label label-default">未開始</span>'
            break
            case 0 :
                label = '<span class="label label-danger">已停止</span>'
            break
            case 1 :
                label = '<span class="label label-success">進行中</span>'
            break
            case 2 :
                label = '<span class="label label-default">已截止</span>'
            break

        }
        return label
    }
    static registerLabel(data){       
        status=Helper.tryParseInt(data.register)
        label=''
        switch (status) {
           
            case 0 :
                label = '<span class="label label-default">未完成</span>'
            break
            case 1 :
                label = '<span class="label label-success">已完成</span>'
            break
           
        }
        return label
    }
    
    static classLabel(data){
        status=Helper.tryParseInt(data.class)
        label=''
        switch (status) {
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