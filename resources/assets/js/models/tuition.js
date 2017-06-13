class Tuition {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

    }
    static title(refund){
        if(refund) return 'BackTuitions'
       return 'Tuitions'
    }
    static source(refund){
         if(refund) return '/back-tuitions'
        return '/tuitions'
    }
    static createUrl(refund){
         return this.source(refund) + '/create' 
    }
    static storeUrl(refund){
         return this.source(refund)
    }
    static showUrl(id,refund){
         return this.source(refund) + '/' + id
    }
    static editUrl(id,refund){
         return this.showUrl(id,refund) +  '/edit'
    }
    static updateUrl(id,refund){
        return this.showUrl(id,refund)
    }
    static deleteUrl(id,refund){
         return this.source(refund) + '/' + id
    }
    static create(signupId,refund){
        let url = this.createUrl(refund) 
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
    static store(form,refund){
        let url =this.storeUrl(refund) 
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
    static show(id,refund){
        return new Promise((resolve, reject) => {
            let url = this.showUrl(id,refund) 
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error);
                })
           
        })
    }
    static edit(id,refund){
        let url = this.editUrl(id,refund) 
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
    static update(form , id,refund){
         let url =this.updateUrl(id,refund) 
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
    static delete(id,refund) {
        return new Promise((resolve, reject) => {
            let url =this.deleteUrl(id,refund) 
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

    

  static getThead(canSelect,refund){
    let thead = [{
                    title: refund ? '退款日期' : '繳費日期',
                    static:true,
                    default:true

                },{
                    title: '金額',
                    static:true,
                    default:true

                },{
                    title: refund ? '退款方式' : '繳費',
                    static:true,
                    default:true

                }, {
                    title: '最後更新',
                    default:true
                }, {
                    title: '匯款銀行',
                    default:false
                }, {
                    title: refund ? '收款人戶名' : '匯款人戶名',
                    default:false
                }, {
                    title: refund ? '收款人帳號' : '匯款人帳號',
                    default:false
                }]

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
    



    

    

}


export default Tuition;