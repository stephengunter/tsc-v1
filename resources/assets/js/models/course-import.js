class CourseImport {


    static source() {
        return '/courses-import'
    }

    static storeUrl() {
        return this.source()
    }


    static store(form) {
        let url = this.storeUrl()
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

    static copy(form) {
        let url = this.storeUrl() + '/copy'

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


export default CourseImport;