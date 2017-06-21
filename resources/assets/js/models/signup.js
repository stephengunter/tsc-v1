class Signup {
  constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        this.formatedCourseName = Signup.getFormatedCourseName(data.course)
   
        this.formatedTuition=Signup.formatTuition(data.discount, data.points)

        this.statusText=Signup.getStatusText(data.status)

      
        
        this.discountText=Signup.formatDiscountText(data.discount , data.points)

  }
    static title(){
       return 'Signups'
    }
    static source(){
        return '/signups'
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
    static create(courseId,userId){
        let url = this.createUrl() 
        url += '?course=' + courseId
        if(userId){
            url += '&user_id=' + userId
        }
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

    static indexOptions(){
        let url =this.source() + '/index-options' 
        return new Promise((resolve, reject) => {
                     axios.get(url)
                    .then(response => {
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })   //End Promise
    }
    static statusOptions(){
        let url =this.source() + '/status-options' 
        return new Promise((resolve, reject) => {
                     axios.get(url)
                    .then(response => {
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })   //End Promise
    }
    static newUserCreate(){
        let url = this.source() + '/new-user'
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
    static newUserStore(form){
        let url = this.source() + '/new-user'
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

  static getThead(canSelect){
    let thead= [
                {
                    title: '姓名',
                    key: 'fullname',
                    sort: false,
                    static:true,
                    default:true

                },{
                title: '報名日期',
                    key: 'date',
                    sort: true,
                    static:true,
                    default:true

                },{
                    title: '網路報名',
                    key: 'net_signup',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '狀態',
                    key: 'status',
                    sort: true,
                    static:true,
                    default:true

                },{
                    title: '課程名稱',
                    key: 'course',
                    sort: false,
                    static:true,
                    default:true

                }, {
                    title: '課程費用',
                    key: 'tuition',
                    sort: true,
                    default:true
                }, {
                    title: '折扣',
                    key: 'discount',
                    sort: true,
                    default:true
                 }
                ]

                if(canSelect){
                   let selectColumn={
                    title: '',
                    key: '',
                    sort: false,
                    static:true,
                    default:true
                   }
                   thead.splice(0, 0, selectColumn);
                }

          
            return thead
    }
    static formatTuition(discount,points){
        let formated_tuition = Helper.formatMoney(this.tuition) + ' 元'
        if(!discount) return formated_tuition
        

        formated_tuition +=' &nbsp ( ' 
        formated_tuition += discount  + ' &nbsp  ' 
        formated_tuition += this.getPointsText(points)  + ' 折優惠' 
        formated_tuition +=' )'

        return formated_tuition
        
    }

    static formatDiscountText(discount,points){
        if(!discount) return ''
       return this.getPointsText(points) + ' 折 / ' + discount    
    }

    static getPointsText(points){
         let pointsText= points
        if(parseInt(points) % 10 == 0 ){
            pointsText = parseInt(points) / 10 
        }
        return pointsText
    }

    static getStatusText(status){
        status=parseInt(status)
        if(status==0) return '待繳費'
        if(status==1) return '已繳費'
        if(status==-1) return '已取消'
            return ''
    }
    static getStatusStyle(status){
        status=parseInt(status)
        if(status==0) return 'default'
        if(status==1) return 'info'
        if(status==-1) return 'warning'

            return ''
    }

    static getFormatedCourseName(course,text){
        return Course.getFormatedCourseName(course,text)
    } 
    


    statusLabel(){
        let status=this.status
        let text=Signup.getStatusText(status)
        let style='label label-' + Signup.getStatusStyle(status)
        
        return `<span class="${style}" > ${text} </span>`
    }




    

    

}


export default Signup;