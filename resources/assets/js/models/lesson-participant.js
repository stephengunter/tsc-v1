class LessonParticipant {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        

    }
    
    static source(){
        return '/lesson-participants'
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
    static index(role){
        return new Promise((resolve, reject) => {
            let url = this.sourse()
            let roleName='Student'
            if(role) roleName=role
            url += '?role=' + roleName
            axios.get(query)
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
    
    static getStatusLabel(data){
        let label=''
        switch (Number(data.status)) {
            case -1 :
                label = '<span class="label label-danger">缺席</span>'
            break
            case 0 :
                label = '<span class="label label-warning">請假</span>'
            break
            case 1 :
                label = ''
            break
           

        }
        return label
    }
    
    
   
    

}


export default LessonParticipant;