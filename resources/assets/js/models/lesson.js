class LessonScripts {
    constructor(lesson) {
       this.lesson = lesson;
    }
    setLesson(lesson){
       this.lesson = lesson;
    }
    courseNameText() {
       return this.lesson.course.number + ' ' + this.lesson.course.name
    }
    statusLabel() {
       let status=parseInt(this.lesson.status)
       if(status<0) return '<span class="label label-danger">停課</span>'
       if(status>0) return '<span class="label label-default">已結束</span>'
       return ''
    }
    dateFormatted(){
        let date=this.lesson.date
        
        let formated=true
        let weekdayText= Helper.chineseDayofWeek(date ,formated)
        return date + ' ' + weekdayText 
    }
    classTimeText(){

        let on=Helper.timeString(this.lesson.on)
        let off=Helper.timeString(this.lesson.off)
        if(on!='' && off!='') return on + ' - ' + off
        return ''
         
        
    }
    positionText(){
       if(!this.lesson.classroom)   return ''
       return this.lesson.classroom.name
    }
    teacherNames(){
        let teachers=this.lesson.teachers
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
    volunteerNames(){
        let volunteers=this.lesson.volunteers
        if(volunteers && volunteers.length){
           let html=''
            for (let i = 0; i < volunteers.length; i++) { 
                html += volunteers[i].profile.fullname + '&nbsp;';
            }
            return html
        }else{
          return ''
        }
    }
   
   
    
}


export default LessonScripts;