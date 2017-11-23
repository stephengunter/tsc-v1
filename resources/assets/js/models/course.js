class Course {
    constructor(data) {

        for (let property in data) {
            this[property] = data[property];
        }

        this.teachersText = Course.teachersText(data.teachers)
        this.categoriesText = Course.categoriesText(data.categories)
        this.classTimesText = Course.getClassTimesText(data.class_times)



        this.canNetSignup = Course.canNetSignup(this)

        this.isCredit = Course.isCredit(this)
        this.hasParent = Course.hasParent(this)
        this.isGroup = Course.isGroup(this)
        this.groupAndParent = Course.groupAndParent(this)
        this.hasReviewedBy = Course.hasReviewedBy(this)

        this.activeLabel = Course.activeLabel(this.active)

    }
    static title() {
        return 'Courses'
    }
    static source() {
        return '/courses'
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
    static create(parent) {
        let url = this.createUrl()
        if (parent) {
            url += '?parent=' + parent
        }

        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error)
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
        let url = this.storeUrl() + '/import'
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
    static updateNumbers(form) {
        return new Promise((resolve, reject) => {

            let url = this.storeUrl() + '/update-numbers'
            form.post(url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })

    }
    static updatePhoto(courseId, photoId) {
        let form = new Form({
            photo_id: photoId
        })
        let url = '/courses/' + courseId + '/update-photo'
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
    static subCourses(parent) {
        let url = this.source() + '/sub-courses'
        url += '?parent=' + parent
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data);
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
        })
    }
    static options(searchParams) {
        let url = this.source() + '/options'
        url = Helper.buildQuery(url, searchParams)
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }
    static groupOptions(searchParams) {
        let url = this.source() + '/group-options'
        url = Helper.buildQuery(url, searchParams)
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error);
                })
        })
    }

    static search(name, options) {
        let url = this.source() + '/search'
        url += '?name=' + name
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    if (options) {
                        resolve(this.toOptions(response.data.courseList))
                    } else {
                        resolve(response.data)
                    }
                })
                .catch(error => {
                    reject(error);
                })
        })
    }
    static canNetSignup(course) {
        return Helper.isTrue(course.net_signup)
    }
    static isCredit(course) {
        return parseInt(course.credit_count) > 0
    }
    static isGroup(course) {
        return Helper.isTrue(course.group)
    }
    static groupAndParent(course) {
        let group = this.isGroup(course)
        let parent = parseInt(course.parent)
        return group && parent == 0

    }
    static isParentGroup(course) {
        return this.groupAndParent(course)
    }
    static hasParent(course) {
        return parseInt(course.parent) > 0
    }
    static isGroupSubCourse(course) {
        let credit_count = parseInt(course.credit_count)
        let parent = parseInt(course.parent)
        return credit_count > 0 && parent > 0
    }
    static hasReviewedBy(course) {
        if (!course.reviewed_by) return false
        return parseInt(course.reviewed_by) > 0
    }
    static mustText(mustVal) {
        if (Helper.isTrue(mustVal)) return '必修'
        return '選修'
    }

    static getThead(canSelect) {
        let thead = [{

                title: '開課中心',
                key: 'center',
                sort: false,
                static: true,
                default: true

            }, {

                title: '編號',
                key: 'number',
                sort: false,
                static: true,
                default: true

            }, {

                title: '名稱',
                key: 'name',
                sort: true,
                static: true,
                default: true

            },
            //  End Static Columns
            {
                view: 0,
                title: '課程分類',
                key: 'categories',
                sort: false,
                default: true

            }, {
                view: 0,
                title: '群組課程',
                key: 'group',
                sort: true,
                default: true
            }, {
                view: 0,
                title: '上課時間',
                key: 'time',
                sort: false,
                default: true
            }, {
                view: 0,
                title: '課程日期',
                key: 'begin_date',
                sort: true,
                default: true

            }, {
                view: 0,
                title: '審核',
                key: 'reviewed',
                sort: true,
                default: true
            }, {
                view: 0,
                title: '狀態',
                key: 'active',
                sort: true,
                default: true
            },
            // End Default Columns
            {
                view: 1,
                title: '教師',
                key: 'teacherNames',
                sort: false,
                default: false
            }, {
                view: 1,
                title: '學分數',
                key: 'credit_count',
                sort: true,
                default: false
            }, {
                view: 1,
                title: '學分單價',
                key: 'credit_price',
                sort: true,
                default: false
            }, {
                view: 1,
                title: '週數',
                key: 'weeks',
                sort: true,
                default: false
            }, {
                view: 1,
                title: '時數',
                key: 'hours',
                sort: true,
                default: false
            }, {
                view: 1,
                title: '學費',
                key: 'cost',
                sort: true,
                default: false
            },

            // Key==2
            {
                view: 2,
                title: '材料',
                key: 'materials',
                sort: false,
                default: false
            }, {
                view: 2,
                title: '材料費',
                key: 'cost',
                sort: true,
                default: false
            },
            // {
            //     view: 2,
            //     title: '報名日期',
            //     key: 'open_date',
            //     sort: true,
            //     default: false
            // },
            {
                view: 2,
                title: '人數上限',
                key: 'limit',
                sort: true,
                default: false
            }, {
                view: 2,
                title: '最低人數',
                key: 'min',
                sort: false,
                default: false
            }
        ]

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
    static creditCountText(course) {
        if (!this.isCredit(course)) return 0
        if (!this.hasParent(course)) return course.credit_count

        let text = this.mustText(course.must)
        return course.credit_count + ' (' + text + ')'
    }
    static teachersText(teachers) {
        if (!teachers || !teachers.length) return ''

        let html = ''
        for (var i = 0; i < teachers.length; i++) {
            html += teachers[i].name + '&nbsp;'
        }
        return html
    }
    static categoriesText(categories) {
        if (!categories || !categories.length) return ''

        let html = ''
        for (var i = 0; i < categories.length; i++) {
            html += categories[i].name + '&nbsp;'
        }
        return html
    }
    static getClassTimesText(class_times) {
        if (!class_times || !class_times.length) return ''

        let html = ''
        for (var i = 0; i < class_times.length; i++) {
            html += Classtime.classTimeFullText(class_times[i]) + '&nbsp;'
        }
        return html
    }
    static getFormatedCourseName(course, text=true) {
        let space= text ? ' ' : '&nbsp'
       

        let fullname='' 
        if(course.number){
            fullname= course.number + space + course.name
        }else{
            fullname=  course.name
        } 

        if(course.level){
            fullname += ' - ' + course.level
        }
        
        return fullname
    }
    static weeksOptions() {
        return Helper.numberOptions(1, 30)
    }
    static toOptions(courseList) {
        if (!courseList.length) return []
        let options = []
        for (let i = 0; i < courseList.length; i++) {
            let item = {
                value: courseList[i].id,
                text: this.getFormatedCourseName(courseList[i], true)
            }
            options.push(item)
        }
        return options

    }
    static activeLabel(active) {
        let style = 'label label-default'
        let text = '停止開課'
        if (parseInt(active)) {
            style = 'label label-info'
            text = '正常開課'
        }


        return `<span class="${style}" > ${text} </span>`
    }




}


export default Course;