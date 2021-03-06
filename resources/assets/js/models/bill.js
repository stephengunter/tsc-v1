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

     static discountOptions(course_id,  course_count, date=''){
        return new Promise((resolve, reject) => {
            let url = this.source() + '/discount-options' 
            url+='?course_id=' + course_id
            url+='&course_count=' + course_count
            if(date) url += '&date=' + date
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
     }
 
    
 
    

     statusLabel(){
        
        let text=this.statusText
        let style='label label-default'
        if(this.status) style='label label-success'
        
        return `<span class="${style}" > ${text} </span>`
    }

    payed(){
        return parseInt(this.status)==1
    }
 
     
 
     
 
 }
 
 
 export default Bill;