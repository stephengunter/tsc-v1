class Course {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        this.teachersText=Course.teachersText(data.teachers)
        this.categoriesText=Course.categoriesText(data.categories)
        this.classTimesText=Course.getClassTimesText(data.class_times)

        this.canNetSignup='可'
        if(Helper.tryParseInt(data.net_signup) < 1){
            this.canNetSignup='否'
        } 
       

    }
    static title(){
       return 'Courses'
    }
    static source(){
        return '/courses'
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
    static create(){
        let url = this.createUrl() 
      
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                   resolve(response.data)
                })
                .catch(error=> {
                     reject(error)
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
    static import(form){
        let url =this.storeUrl() + '/import'
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
    static updatePhoto(courseId, photoId) {
        let form = new Form({
            photo_id: photoId
        })
        let url ='/courses/' + courseId + '/update-photo'
        let method = 'put'
        return new Promise((resolve, reject) => {
            form.submit(method,url)
                .then(saved => {
                    resolve(saved);
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
                })  
    }
    static options(searchParams){
        let url =this.source() + '/options' 
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
    static groupOptions(searchParams){
        let url =this.source() + '/group-options' 
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
    
    static search(name,options){
        let url =this.source() + '/search' 
        url += '?name=' + name
        return new Promise((resolve, reject) => {
                     axios.get(url)
                    .then(response => {
                        if(options){
                            resolve(this.toOptions(response.data.courseList))
                        }else{
                            resolve(response.data)
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
                }) 
    }

    static isParentGroup(course){
          let credit_count= parseInt(course.credit_count)
          let parent= parseInt(course.parent)
          return credit_count > 0  &&  parent==0
    }

    static isGroupSubCourse(course){
        let credit_count= parseInt(course.credit_count)
        let parent= parseInt(course.parent)
        return credit_count > 0  &&  parent > 0
    }

    static getThead(canSelect){
        let thead= [{
                    title: '開課中心',
                    key: 'center',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '編號',
                    key: 'number',
                    sort: true,
                    static:true,
                    default:true

                },{
                    title: '名稱',
                    key: 'name',
                    sort: true,
                    static:true,
                    default:true

                }, {
                    title: '群組課程',
                    key: 'group',
                    sort: false,
                    default:true
                }, {
                    title: '上課時間',
                    key: 'time',
                    sort: false,
                    default:true
                }, {
                    title: '課程日期',
                    key: 'begin_date',
                    sort: true,
                    default:true

                }, {
                    title: '報名日期',
                    key: 'open_date',
                    sort: true,
                    default:true
                }, {
                    title: '狀態',
                    key: 'active',
                    sort: true,
                    default:true
                },{
                    title: '教師',
                    key: 'teacherNames',
                    sort: false,
                    default:false
                }, {
                    title: '學分數',
                    key: 'credit_count',
                    sort: true,
                    default:false
                },{
                    title: '週數',
                    key: 'weeks',
                    sort: true,
                    default:false
                },{
                    title: '時數',
                    key: 'hours',
                    sort: true,
                    default:false
                },{
                    title: '學費',
                    key: 'cost',
                    sort: true,
                    default:false
                },{
                    title: '材料',
                    key: 'materials',
                    sort: false,
                    default:false
                },{
                    title: '材料費',
                    key: 'cost',
                    sort: true,
                    default:false
                },{
                    title: '人數上限',
                    key: 'limit',
                    sort: true,
                    default:false
                },{
                    title: '最低人數',
                    key: 'min',
                    sort: false,
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
    static teachersText(teachers){
        if (!teachers.length) return ''
        let html=''
        for (var i = 0; i < teachers.length; i++) {
            html += teachers[i].name + '&nbsp;'
        }
        return html
    }
    static categoriesText(categories){       
        if (!categories.length) return ''
        let html=''
        for (var i = 0; i < categories.length; i++) {
            html += categories[i].name + '&nbsp;'
        }
        return html
    }
    static getClassTimesText(class_times){
        let html=''
        if(class_times.length){
            for (var i = 0; i < class_times.length; i++) {
                html += Classtime.classTimeFullText(class_times[i])   + '&nbsp;'
            }
        }
        return html               
    }
    static getFormatedCourseName(course,text){
        if(text) {
            return course.name + '  (編號 ' + course.number + ' )'
        }
       return course.name + ' &nbsp (編號 ' + course.number + ' )'
    }
    static weeksOptions(){
        return Helper.numberOptions(1,30)
    }
    static toOptions(courseList){
        if(!courseList.length) return []
         let options=[]
        for(let i=0; i<courseList.length; i++){
            let item={
                value:courseList[i].id,
                text:this.getFormatedCourseName(courseList[i],true)
            }
            options.push(item)
        }
        return options

    }
    
   
    

}


export default Course;