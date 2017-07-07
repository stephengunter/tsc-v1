class Notice {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        

    }
    static title(){
       return 'Notices'
    }
    static source(){
        return '/notices'
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
                     reject(error)
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

  static getThead(){
    let thead= [{
                    title: '標題',
                    key: 'title',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '內容',
                    key: 'content',
                    sort: true,
                    static:true,
                    default:true

                },{
                    title: '網頁',
                    key: 'public',
                    sort: true,
                    static:true,
                    default:true

                }, {
                    title: 'Email',
                    key: 'courses',
                    sort: true,
                    default:true
                }, {
                    title: '建檔日期',
                    key: 'created_at',
                    sort: true,
                    default:true
                }, ]

                
            return thead
    }
  
    
   
    

}


export default Notice;