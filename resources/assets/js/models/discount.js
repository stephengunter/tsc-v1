class Discount {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

       

    }
    static pointText(points){
        if(!points) return '無折扣'
       
        return String(points).replace('0','') + ' 折' 
    }
    
    static title(){
       return 'Discounts'
    }
    static source(){
        return '/discounts'
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
    static index(center_id){
        return new Promise((resolve, reject) => {
            let url = this.source() + '?center=' + center_id
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
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
    static updateDisplayOrder(discounts) {
        let form = new Form({
            discounts: discounts
        })
        return new Promise((resolve, reject) => {

            let url = this.storeUrl() + '/display-order'
            form.post(url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })

    }
    static pointOptions(){
                let options = []
                let max = 95
                let min=35
                for (var i = max; i >= min; i-=5) {
                    let option = {
                        text: i,
                        value: i
                    }
                    options.push(option)
                }

                return options

    }
    static options(course_id,date){
        let url =this.source() + '/options' 
        url+= '?course=' + course_id
        url+= '&date=' + date
        return new Promise((resolve, reject) => {
                     axios.get(url)
                    .then(response => {
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })  
    }
    static countTuition(course_id,discount_id,date){
        let url =this.source() + '/count-tuition' 
        url+= '?course=' + course_id
        url+= '&discount=' + discount_id
        url+= '&date=' + date
        return new Promise((resolve, reject) => {
                     axios.get(url)
                    .then(response => {
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })  
    }
   

   

  




    

    

}


export default Discount;