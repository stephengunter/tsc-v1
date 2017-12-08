class Student {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

    }
    static roleName(){
         return 'Student'
    }
    static title(){
       return 'Students'
    }
    static source(){
        return '/students'
    }
    static createUrl(){
         return this.source() + '/create' 
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
    static deleteUrl(id){
         return this.source() + '/' + id
    }
    static create(){
        let url = this.createUrl() 
      
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
    static store(form){
        let url =this.storeUrl() 
        let method='post'
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
    static updateDisplayOrder(id,up){
        let url =this.updateUrl(id) + '/update-order'
        let method='put'
        let form = new Form({                        
                         up: up
                    })
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
    static delete(id) {
        return new Promise((resolve, reject) => {
            let url =this.deleteUrl(id) 
            let form = new Form()
            form.delete(url)
                .then(response => {
                    resolve(true);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }
    static activeLabel(val,show_normal){
       
        if(Helper.isTrue(val)){
            if(!show_normal) return ''
            else   return `<span class="label label-info" > 正常 </span>`  
        
        }else{
            let style='label label-danger'  
            let text='已退出'  
            return `<span class="${style}" > ${text} </span>`  
        } 
        

    }
    static activeOptions(){
         return  [{
                    text: '正常',
                    value: '1'
                }, {
                    text: '已退出',
                    value: '0'
                }]
    }

    static getThead(){
        let thead= [
            //    {
            //        title: '編號',
            //        key: 'number',
            //        sort: false,
            //        static:true,
            //        default:true

            //    },
                {
                    title: '姓名',
                    key: 'user.profile.fullname',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '加入日期',
                    key: 'join_date',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '手機',
                    key: 'user.phone',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: 'Email',
                    key: 'user.email',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '',
                    key: 'active',
                    sort: false,
                    static:true,
                    default:true
                },]
               

              

         
           return thead
   }
   
   
}

export default Student