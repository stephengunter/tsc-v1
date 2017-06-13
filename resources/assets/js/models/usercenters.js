class UserCenters {
    constructor(user,role) {
        this.id=user
        this.role=role

        this.searchParams={
            user:user,
            role:role
        }

        this.deleteParams={
            center:0,
            role:role
        }

        



    }
     source(){
        return '/user-centers'
    }
     createUrl(){
        let url= this.source() + '/create' 
     
        return Helper.buildQuery(url,this.searchParams)
    }
     storeUrl(){
         return this.source()         
    }
     showUrl(id){
         return this.source() + '/' + id
    }
     editUrl(id){
         return this.showUrl(id) +  '/edit'
    }
   
     deleteUrl(center){
        this.deleteParams.center=center
        let url= this.source() + '/' + this.id
       
        return Helper.buildQuery(url,this.deleteParams)
    }
     create(){
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
     index(){
        return new Promise((resolve, reject) => {
            let url = Helper.buildQuery(this.source(),this.searchParams)

            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
    }
     store(form){
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
    
    delete(center) {
        return new Promise((resolve, reject) => {
            let url =this.deleteUrl(center) 
          
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
    

    


}


export default UserCenters;