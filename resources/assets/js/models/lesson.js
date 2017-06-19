class Lesson {
    constructor(data) {
       
        for (let property in data) {
            this[property] = data[property];
        }

        this.courseNameText=Lesson.courseNameText(data.course)
        this.dateFormatted=Lesson.dateFormatted(data.date)
        this.classTimeText=Lesson.lessonClassTimeText(data)
        this.statusLabel=Lesson.statusLabel(data.status)
        this.teacherNames=Lesson.teacherNames(data.teachers)
        this.volunteerNames=Lesson.volunteerNames(data.volunteers)

    }
    static title(){
       return 'Lessons'
    }
    static source(){
        return '/lessons'
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
    static create(course){
        let url = this.createUrl() 
        url += '?course=' + course
      
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
    static submitDayoff(){
        let url =this.storeUrl() + '/dayOff'
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
    static createInitialize(course){
        let url ='/lessons-initialize/create'
        url += '?course=' + course
      
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
    static submitInitialize(form){
        let url ='/lessons-initialize'
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

    static getThead(){
    let thead= [{
                    title: '',
                    key: '',                    
                    sort: false,
                    static:true,
                    default:true,
                    width:'5%'

                },{
                    title: '',
                    key: 'order',
                    sort: true,
                    static:true,
                    default:true,
                    width:'3%'

                },{
                    title: '日期',
                    key: 'date',
                    sort: true,
                    static:true,
                    default:true,
                    width:'12%'

                }, {
                    title: '狀態',
                    key: 'status',
                    static:true,
                    sort: true,
                    default:true,
                     width:'8%'
                }, {
                    title: '時間',
                    key: 'time',
                    static:true,
                    sort: false,
                    default:true,
                    width:'11%'
                }, {
                    title: '地點',
                    key: 'position',
                    static:true,
                    sort: false,
                    default:true,
                    width:'11%'

                }, {
                    title: '授課老師',
                    key: 'teachers',
                    sort: false,
                    default:true,
                    width:'19%'
                }, {
                    title: '教育志工',
                    key: 'volunteers',
                    sort: false,
                    default:true,
                    width:'19%'
                }, {
                    title: '學生應到/實到',
                    key: 'ps',
                    sort: false,
                    default:true,
                    width:'11%'

                }, 




                {
                    title: '課目標題',
                    key: 'title',
                    sort: false,
                    default:false,
                    width:'15%'
                }, {
                    title: '內容重點',
                    key: 'content',
                    sort: false,
                    default:false,
                    width:'15%'
                },{
                    title: '教材',
                    key: 'materials',
                    sort: false,
                    default:false,
                    width:'10%'
                },{
                    title: '備註',
                    key: 'ps',
                    sort: false,
                    default:false,
                    width:'10%'
                }
                ]
            return thead
    }
  
    static courseNameText(course) {
       return course.number + ' ' + course.name
    }
    static statusLabel(status) {
        status=parseInt(status)
       if(status<0) return '<span class="label label-danger">停課</span>'
       if(status>0) return '<span class="label label-default">已結束</span>'
       return '<span class="label label-success">正常</span>'
    }
    static dateFormatted(date){
        
        let formated=true
        let weekdayText= Helper.chineseDayofWeek(date ,formated)
        return date + ' ' + weekdayText 
    }
    static lessonClassTimeText(lesson){

        let on=Helper.timeString(lesson.on)
        let off=Helper.timeString(lesson.off)
        if(on!='' && off!='') return on + ' - ' + off
        return ''
         
        
    }
    static positionText(lesson){
       if(!lesson.classroom)   return ''
       return lesson.classroom.name
    }
    static teacherNames(teachers){
        
        if(teachers && teachers.length){
           let html=''
            for (let i = 0; i < teachers.length; i++) { 
                html += teachers[i].name + '&nbsp;';
            }
            return html
        }else{
          return ''
        }
    }
    static volunteerNames(volunteers){
        if(volunteers && volunteers.length){
           let html=''
            for (let i = 0; i < volunteers.length; i++) { 
                html += volunteers[i].name + '&nbsp;';
            }
            return html
        }else{
          return ''
        }
    }
    static statusOptions(){
        return [{
                    text: '正常',
                    value: '0'
                }, {
                    text: '已結束',
                    value: '1'
                },{
                    text: '停課',
                    value: '-1'
                }]
    }
   
   
    
}


export default Lesson;