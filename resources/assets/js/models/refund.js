class RefundScripts {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        this.charge = Helper.formatMoney(data.charge)
        this.tuition = Helper.formatMoney(data.tuition)
        this.cost = Helper.formatMoney(data.cost)
        this.total = Helper.formatMoney(data.total)
        this.points =Helper.formatMoney(Number(data.points).toFixed(2)) 

        this.statusLabelHtml=RefundScripts.statusLabel(data.status)

    }

   static title(){
       return 'Refunds'
    }
    static source(){
        return '/refunds'
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
    static create(signupId){
        let url = this.createUrl() 
        url += '?signup=' + signupId
        
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
   


    static getThead(canSelect){
    let thead= [
                {
                    title: '單號',
                    key: 'number',
                    sort: false,
                    static:true,
                    default:true

                },
                {
                title: '申請日期',
                    key: 'date',
                    sort: true,
                    static:true,
                    default:true

                },{
                    title: '狀態',
                    key: 'status',
                    sort: true,
                    static:true,
                    default:true

                },
                {
                    title: '姓名',
                    key: 'fullname',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '課程',
                    key: 'course',
                    sort: false,
                    static:true,
                    default:true

                }, {
                    title: '應退金額',
                    key: 'total',
                    sort: false,
                    default:true
                }, {
                    title: '收付方式',
                    key: 'pay_by',
                    sort: false,
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

    static countRefundRatio(done,total){
        if(done==0) return 0.9
        let oneThird=Math.ceil(total/3)
        if(done <= oneThird) return 0.5
            
        return 0
    }
    static getStatusText(status){
        status=parseInt(status)
        if(status<0) return '待審核'
        if(status==0) return '審核中'
        if(status>0) return '已完成'

            return ''
    }
    static getStatusStyle(status){
        status=parseInt(status)
        if(status<0) return 'default'
        if(status==0) return 'info'
        if(status>0) return 'success'

            return ''
    }
    

    static statusLabel(status)
    {
        let text=RefundScripts.getStatusText(status)
        let style='label label-' + RefundScripts.getStatusStyle(status)
        
        return `<span class="${style}" > ${text} </span>`
    }
  
   

    

}


export default RefundScripts;