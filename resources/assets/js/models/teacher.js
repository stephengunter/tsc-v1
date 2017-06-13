class Teacher {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }
        

    }
    static role(){
        return 'Teacher'
    }
    static title(){
       return 'Teachers'
    }
    static source(){
        return '/teachers'
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
    static create(userId){
        let url = this.createUrl() 
        if(userId){
            url += '?user=' + userId
        }
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
    
    static statusOptions(){
        let url =this.source() + '/status-options' 
        return new Promise((resolve, reject) => {
                     axios.get(url)
                    .then(response => {
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })   //End Promise
    }
    static newUserCreate(){
        let url = this.source() + '/new-user'
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
    static newUserStore(form){
        let url = this.source() + '/new-user'
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

  static getThead(canSelect){
    let thead=  [{
                        title: '姓名',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
                        title: '手機',
                        key: 'user.phone',
                        sort: false,
                        default:true
                    }, {
                        title: '專長',
                        key: 'specialty',
                        sort: true,
                        default:true
                    }, {
                        title: '所屬中心',
                        key: 'centers',
                        sort: false,
                        default:true

                    }, {
                        title: '狀態',
                        key: 'active',
                        sort: true,
                        default:true
                    },{
                        title: '審核',
                        key: 'reviewed',
                        sort: true,
                        default:true
                    }, {
                        title: '更新時間',
                        key: 'updated_at',
                        sort: true,
                        default:true
                    }]

                if(canSelect){
                   let selectColumn={
                    title: '',
                    key: '',
                    sort: false,
                    static:true,
                    default:true
                   }
                   thead.splice(0, 0, selectColumn);
                }

          
            return thead
    }
    

}


export default Teacher;