class Teacher {
    constructor(data) {

        for (let property in data) {
            this[property] = data[property];
        }


    }
    static role() {
        return 'Teacher'
    }
    static title() {
        return 'Teachers'
    }
    static source() {
        return '/teachers'
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
    static
    import (form) {
        let url = this.source() + '/import'
        return new Promise((resolve, reject) => {
            axios.post(url, form)
                .then(response => {
                    resolve(response.data);
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
    static updateReview(id, reviewed) {
        let url = this.storeUrl() + '/update-review'
        let form = new Form({
            id: id,
            reviewed: reviewed
        })
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
    static groupTeachers(id) {
        let url = this.source() + '/group-teachers'
        url += '?id=' + id
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
    static removeTeacherFromGroup(group_id, teacher_id) {
        let url = this.updateUrl(group_id)
        url += '/remove-group-teacher'
        let method = 'put'
        let form = new Form({
            teacher_id: teacher_id
        })
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
    static options(params) {
        let url = this.source() + '/options'
        if (params.course) {
            url += '?course=' + params.course
        } else if (params.center) {
            url += '?center=' + params.center
        }
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

    static unreviewedList(center) {
            let url = this.source() + '/review'
            url += '?center=' + center
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
        // static updateReviewList(teachers) {
        //     let url = this.source() + '/review'
        //     let form = new Form({
        //         teachers: teachers
        //     })
        //     let method = 'post'
        //     return new Promise((resolve, reject) => {
        //         form.submit(method, url)
        //             .then(data => {
        //                 resolve(data);
        //             })
        //             .catch(error => {
        //                 reject(error);
        //             })
        //     })
        // }


    static getThead(canSelect) {
        let thead = [{
            order: 1,
            title: '姓名',
            key: 'name',
            static: true,

        }, {
            order: 2,
            title: '群組',
            key: 'group',
            sort: true,
            static: true,
        }, {
            order: 3,
            title: '手機',
            key: 'user.phone',
            sort: false,
            default: true
        }, {
            order: 4,
            title: '專長',
            key: 'specialty',
            sort: true,
            default: true
        }, {
            order: 5,
            title: '所屬中心',
            key: 'centers',
            static: true,
            sort: false,

        }, {
            order: 6,
            title: '審核',
            key: 'reviewed',
            static: true,
            sort: true,

        }, {
            order: 7,
            title: '更新時間',
            key: 'updated_at',
            static: true,
            sort: true,

        }]

        if (canSelect) {
            let selectColumn = {
                title: '',
                key: '',
                sort: false,
                static: true,
                default: true
            }
            thead.splice(0, 0, selectColumn);
        }


        return thead
    }


}


export default Teacher;