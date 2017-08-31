 class Score {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

    }
    static title(){
       return 'Scores'
    }
    static source(){
        return '/scores'
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
    static index(course){
         return new Promise((resolve, reject) => {
            let url = this.source() + '?course=' + course
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
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
    static import(form){
        let url = this.source() + '/import'
        return new Promise((resolve, reject) => {
             axios.post(url, form)
                .then(response => {
                     resolve(response.data);
                })
                .catch(error => {
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
                    title: '學員編號',
                    key: 'score.student.number',
                    sort: true,
                    static:true,
                    default:true

                },
                {
                    title: '學員姓名',
                    key: 'score.student.name',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '成績',
                    key: 'score.points',
                    sort: false,
                    static:true,
                    default:true
                },
                {
                    title: '備註',
                    key: 'score.ps',
                    sort: false,
                    static:true,
                    default:true
                },
                {
                    title: '',
                    key: '',
                    sort: false,
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


export default Score;