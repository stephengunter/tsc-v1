 class Admission {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

    }
    static title(){
       return 'Admissions'
    }
    static source(){
        return '/admissions'
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
    static getThead(canSelect){
         let thead= [
                {
                    title: '',
                    key: '',
                    sort: false,
                    static:true,
                    default:true

                },
                {
                    title: '姓名',
                    key: 'signup.user.profile.fullname',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '狀態',
                    key: 'status',
                    sort: false,
                    static:true,
                    default:true
                },{
                    title: '報名日期',
                    key: 'signup.date',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '課程費用',
                    key: 'signup.tuition',
                    sort: false,
                    default:true
                }, {
                    title: '折扣',
                    key: 'signup.discount',
                    sort: false,
                    default:true
                 },{
                    title: '最後更新',
                    key: 'updated_by',
                    sort: false,
                    default:true
                 }
                ]

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


export default Admission;