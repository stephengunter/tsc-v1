class User {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        let userUpdated = Moment(data.updated_at)
        let profileUpdated = Moment(data.profile.updated_at)
        if (userUpdated.isAfter(profileUpdated)){
            this.updated_at=data.profile.updated_at
            this.updated_by=data.profile.updated_by
        }


        this.titleText=''
        if (data.profile.title){
            this.titleText = data.profile.title.name
        }
       

        

    }
    static title(){
       return 'Users'
    }
    static titleHtml(){
       return Helper.getTitleHtml(this.title())
    }
    static source(){
        return '/users'
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
      
        // switch(role) {
        //     case 'Teacher':
        //         url='/teachers/' + id + '/update-user'
        //        break;
        //     case 'Admin':
        //         url='/admins/' + id + '/update-user'
        //         break;
        //     case 'Owner':
        //         url='/admins/' + id + '/update-user'
        //         break;
        //     case 'Volunteer':
        //         url='/volunteers/' + id + '/update-user'
        //         break;
        //     case 'Student':
        //         url='/students/' + id + '/update-user'
        //         break;
        //     default:
        //         url='/users/' + id 
        // }
               
     
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
    static update(form , id, role){
         let url =this.updateUrl(id , role) 
         let method='put'
        return new Promise((resolve, reject) => {
            form.submit(method,url)
                .then(user => {
                    resolve(user);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }
    static updateUserContactInfo(userId, contactInfoId) {
        let form = new Form({
            contact_info: contactInfoId
        })
        let url ='/users/' + userId + '/update-contactinfo'
        let method = 'put'
        return new Promise((resolve, reject) => {
            form.submit(method,url)
                .then(saved => {
                    resolve(saved);
                })
                .catch(error => {
                    reject(error);
                })
        })
       
    }
    static updateUserPhoto(userId, photoId) {
        let form = new Form({
            photo_id: photoId
        })
        let url ='/users/' + userId + '/update-photo'
        let method = 'put'
        return new Promise((resolve, reject) => {
            form.submit(method,url)
                .then(saved => {
                    resolve(saved);
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
    static roleCanAdd(user_id) {
        return new Promise((resolve, reject) => {
            let url='/user-roles/' + user_id + '/edit'
            axios.get(url)
                .then(response => {
                    resolve(response.data.roles)
                })
                .catch( error => {
                     reject(error)
                })
        })
    }
    static findUsers(email,phone){
        return new Promise((resolve, reject) => {
            let form=new Form({
                   user:{
                      email: email,
                      phone: phone
                   }
               })
            let url= this.source() +  '/find-users'
            form.post(url)
            .then(data => {
               resolve(data)                              
            })
            .catch(error => {
                reject(error)
            })
        })
    }

    setUpdatedBy(){
        let userUpdated = Moment(this.updated_at)
        let profileUpdated = Moment(this.profile.updated_at)
        if (userUpdated.isAfter(profileUpdated)){
            this.updated_at=profile.updated_at
            this.updated_by=this.user.updated_by
        }else{
            this.updated_at=this.user.profile.updated_at
            this.updated_by=this.user.profile.updated_by
        }
    }
    static genderOptions() {
        return CommonService.genderOptions()
    }


}


export default User;