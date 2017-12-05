class Bill {
   constructor(data) {
        
         for (let property in data) {
             this[property] = data[property];
         }
   }
     static title(){
        return 'Bills'
     }
     static source(){
         return '/bills'
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
         url += '?user=' + userId
         
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

     static discountOptions(centerId){
        return new Promise((resolve, reject) => {
            let url = this.source() + '/discount-options' 
            url+='?center=' + centerId
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
     }
 
    
 
 
 
     
 
     
 
 }
 
 
 export default Bill;