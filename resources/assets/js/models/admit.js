 class Admit {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

    }
    static title(){
       return 'Admits'
    }
    static source(){
        return '/admits'
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
    
    
   
    

}


export default Admit;