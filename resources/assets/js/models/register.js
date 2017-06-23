 class Register {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

    }
    static title(){
       return 'Registers'
    }
    static source(){
        return '/course-registers'
    }
    static createUrl(course){
         return this.source() + '/create?course=' + course
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
    static create(course){
        return new Promise((resolve, reject) => {
            let url = this.createUrl(course) 
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
    static getThead(show_updated){
         let thead= [
                {
                    title: '編號',
                    key: 'number',
                    sort: false,
                    static:true,
                    default:true

                },
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
                    title: '',
                    key: 'active',
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

                },
                ]


                if(show_updated){
                    thead.push({
                    title: '最後更新',
                    key: 'updated_by',
                    sort: false,
                    default:true
                 })
                }

               

          
            return thead
    }
    
   
    

}


export default Register;