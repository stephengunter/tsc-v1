class ScheduleImport {


    static source() {
        return '/schedules-import'
    }

    static storeUrl() {
        return this.source()
    }

    static createUrl() {
        return this.source() + '/create'
    }

    static create(course) {
        let url = this.createUrl()
        url += '?course=' + course
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

    static excelImport(form) {
        let url = this.storeUrl() + '/excel'
        let method = 'post'
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








}


export default ScheduleImport;