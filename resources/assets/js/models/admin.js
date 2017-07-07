class Admin {
    constructor(data) {

        for (let property in data) {
            this[property] = data[property];
        }

    }
    static role() {
        return 'Admin'
    }
    static title() {
        return 'Admins'
    }
    static source() {
        return '/admins'
    }
    static createUrl() {
        return this.source() + '/create'
    }
    static storeUrl() {
        return this.source()
    }
    static showUrl(id) {
        return this.source() + '/' + id
    }
    static editUrl(id) {
        return this.showUrl(id) + '/edit'
    }
    static updateUrl(id) {
        return this.showUrl(id)
    }
    static deleteUrl(id) {
        return this.source() + '/' + id
    }
    static create(userId) {
        let url = this.createUrl()
        if (userId) {
            url += '?user=' + userId
        }
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }
    static store(form) {
        let url = this.storeUrl()
        let method = 'post'
        return new Promise((resolve, reject) => {
            form.submit(method, url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }
    static show(id) {
        return new Promise((resolve, reject) => {
            let url = this.showUrl(id)
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }
    static edit(id) {
        let url = this.editUrl(id)
        return new Promise((resolve, reject) => {

            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }
    static update(form, id) {
        let url = this.updateUrl(id)
        let method = 'put'
        return new Promise((resolve, reject) => {
            form.submit(method, url)
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
            let url = this.deleteUrl(id)
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

    static indexOptions() {
        let url = this.source() + '/index-options'
        return new Promise((resolve, reject) => {
                axios.get(url)
                    .then(response => {
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            }) //End Promise
    }
    static newUserCreate() {
        let url = this.source() + '/new-user'
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }
    static newUserStore(form) {
        let url = this.source() + '/new-user'
        let method = 'post'
        return new Promise((resolve, reject) => {
            form.submit(method, url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }

    static getThead() {
        let thead= [{
                    title: '姓名',
                    key: 'profile.fullname',
                    sort: false,
                    default:true,
                    width:'20%'
                },{
                    title: '角色',
                    key: 'admin.role',
                    sort: true,
                    default:true,
                    width:'10%'
                }, {
                    title: '手機',
                    key: 'phone',
                    default:true,
                    sort: true
                   
                }, {
                     title: '所屬中心',
                     key: 'centers',
                     default:true,
                     sort: false
                }, {
                     title: '狀態',
                     key: 'active',
                     default:true,
                     sort: false
                }, {
                     title: '最後更新',
                     key: 'updated_at',
                     default:true,
                     sort: false
                }]

        


        return thead
    }


}


export default Admin;