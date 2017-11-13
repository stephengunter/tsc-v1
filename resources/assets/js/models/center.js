class Center {
    constructor(data) {

        for (let property in data) {
            this[property] = data[property];
        }

        this.addressText = ''
        if (data.contactInfo) {
            this.addressText = data.contactInfo.addressA
        }

    }
    static title() {
        return 'Centers'
    }
    static source() {
        return '/centers'
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
    static create() {
        let url = this.createUrl()

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
    static index(oversea) {
        return new Promise((resolve, reject) => {
            let url = this.source() + '?oversea=' + oversea
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
    static
    import (form) {
        let url = this.source() + '-import'
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
    static updateDisplayOrder(centers) {
        let form = new Form({
            centers: centers
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
    static updatePhoto(centerId, photoId) {
        let form = new Form({
            photo_id: photoId
        })
        let url = '/centers/' + centerId + '/update-photo'
        let method = 'put'
        return new Promise((resolve, reject) => {
            form.submit(method, url)
                .then(saved => {
                    resolve(saved);
                })
                .catch(error => {
                    reject(error);
                })
        })

    }
    static options() {
        let url = this.source() + '/options'
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

    static overseaOptions() {
        return [{
            text: '台灣',
            value: 0
        }, {
            text: '海外',
            value: 1
        }]
    }














}


export default Center;