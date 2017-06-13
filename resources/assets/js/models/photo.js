class Photo {
    static source(){
        return '/photoes'
    }
    
    static storeUrl(){
         return this.source()
    }
    static showUrl(id){
         return this.source() + '/' + id
    }
    
    static store(form){
        let url =this.storeUrl() 
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
                .then(address => {
                    resolve(address);
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
    static default(key) {
        let path='/images/default-' + key + '.png'
        let photo= {
            path:path,
            default:true
        }
        return photo
    }
    


}


export default Photo;