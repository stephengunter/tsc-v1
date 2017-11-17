class SignupInfo {
    constructor(data) {

        for (let property in data) {
            this[property] = data[property];
        }

        this.canNetSignup = '可'
        if (Helper.tryParseInt(data.net_signup) < 1) {
            this.canNetSignup = '否'
        }

        this.cost = Helper.formatMoney(data.cost)

        this.tuition = Helper.formatMoney(data.tuition)

        // this.materials = Helper.replaceAll(this.materials, '<br>', '\n')


    }

    static source() {
        return '/course-signup-infoes'
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








}


export default SignupInfo;