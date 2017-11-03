class TeacherReview {


    static source() {
        return '/teachers-review'
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

    static index(center) {
        let url = this.source()
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

    static store(teacher_ids) {

        let url = this.storeUrl()
        let method = 'post'
        let form = new Form({
            teacher_ids: teacher_ids
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

    static update(id, reviewed) {
        let url = this.updateUrl(id)
        let form = new Form({
            reviewed: reviewed
        })
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


export default TeacherReview;