class Helper {
    static newWindow(path){
        window.open(window.location.origin +  path)
    }
    static redirect(url){
        document.location = url;
    }
    static tryParseInt(val){
        if(!val) return 0
        return parseInt(val)
    }
    static getOptions(list, valueKey, textKey){
        let options=[]
        if(list.length<1) return []
        for(let i=0; i<list.length; i++){
            let item={
                value:list[i][valueKey],
                text:list[i][textKey]
            }
            options.push(item)
        }
        return options

    }
    static getIcon(title){
        title=title.toLowerCase()
        let html = ''
        switch (title) {
            case 'users':
                html = '<i class="fa fa-user" aria-hidden="true"></i>'
                break;
            case 'contactinfo':
            html = '<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>'
                break;  
            case 'teachers':
                html = '<span class="glyphicon glyphicon-user" aria-hidden="true"></span>'
                break;
            case 'centers':
                html = '<i class="fa fa-university" aria-hidden="true"></i>'
                break;
            case 'categories':
                html = '<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>'
                break;
            case 'volunteers':
                html = '<i class="fa fa-handshake-o" aria-hidden="true"></i>'
                break;
            case 'courses':
                html = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>'
                break;
            case 'lessons':
                html = '<i class="fa fa-calendar-check-o" aria-hidden="true"></i>'
                break;    
            case 'signups':
                html = '<i class="fa fa-file-text-o" aria-hidden="true"></i>'
            break;
            case 'tuitions':
                html = '<i class="fa fa-money" aria-hidden="true"></i>'
            break;
            case 'refunds':
                html = '<i class="fa fa-arrow-circle-left" aria-hidden="true"></i>'
            break;
            case 'settings':
                html = '<i class="fa fa-cog" aria-hidden="true"></i>'
            break;
            case 'discounts':
                html = '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span>'
            break;
            case 'admins':
                html = '<i class="fa fa-key" aria-hidden="true"></i>'
            break;
            case 'statuses':
                html = '<i class="fa fa-check-circle" aria-hidden="true"></i>'
            break;
               
        }

        return html
    }
    static getTitleHtml(title) {
        let html = ''
        title=title.toLowerCase()
        switch (title) {
            case 'users':
                html = '<i class="fa fa-user" aria-hidden="true"></i> 使用者管理'
                break;
            case 'teachers':
                html = '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> 教師管理'
                break;
            case 'centers':
                html = '<i class="fa fa-university" aria-hidden="true"></i> 開課中心'
                break;
            case 'categories':
                html = '<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> 課程分類'
                break;
            case 'volunteers':
                html = '<i class="fa fa-handshake-o" aria-hidden="true"></i> 志工管理'
                break;
            case 'courses':
                html = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> 課程管理'
                break;
            case 'signups':
            html = '<i class="fa fa-file-text-o" aria-hidden="true"></i> 報名紀錄'
            break;
            case 'tuitions':
            html = '<i class="fa fa-money" aria-hidden="true"></i> 繳費紀錄'
            break;
            case 'refunds':
            html = '<i class="fa fa-arrow-circle-left" aria-hidden="true"></i> 退費申請'
            break;
            case 'admins':
                html = '<i class="fa fa-key" aria-hidden="true"></i> 權限管理'
                break;
        }

        return html
    }
    static todayString(){
        let now = Moment()
        return this.tpeDate(now)
    }
    static tpeTime(datetime) {
        return MomentTimeZone.utc(datetime).tz("Asia/Taipei").format('YYYY-MM-DD HH:mm:ss')
    }
    static tpeDate(datetime){
          return MomentTimeZone.utc(datetime).tz("Asia/Taipei").format('YYYY-MM-DD')
    }
    static activeText(active) {
        if (parseInt(active)) return '上架中'
        return '已下架'
    }
    static activeLabel(active) {
        if (parseInt(active)) return 'label label-info'
        return 'label label-default'
    }

    static reviewedText(reviewed) {
        if (parseInt(reviewed)) return '已審核'
        return '未審核'
    }
    static reviewedLabel(reviewed) {
        if (parseInt(reviewed)) return 'label label-success'
        return 'label label-danger'
    }
    static activeOptions(){
        return CommonService.activeOptions()
    }
    static genderOptions() {
         return CommonService.genderOptions()
    }
    static boolOptions() {
         return CommonService.boolOptions()
    }
    static datetimePickerOption() {
        return TimeService.datetimePickerOption()
    }
    static numberOptions(min, max, desc) {

        return CommonService.numberOptions(min, max, desc)
    }
    static getTimeobj(val) {
        return TimeService.getTimeobj(val)
    }
    static defaultTimeObj(hour, minute) {
        return TimeService.defaultTimeObj(hour, minute) 
    }
    static getTimeSelected(val) {
        return TimeService.getTimeSelected(val)
    }
    static timeString(val) {
        return TimeService.timeString(val)
    }

    static categoriesText(categories) {
        if (!categories.length) return ''
        let html = ''
        for (var i = 0; i < categories.length; i++) {
            html += categories[i].name + '&nbsp;'
        }
        return html
    }
    static namesText(names) {
        if (!names.length) return ''
        let html = ''
        for (var i = 0; i < names.length; i++) {
            html += names[i] + '&nbsp;'
        }
        return html
    }
    static classTimeFullText(data) {
        
        return Classtime.classTimeFullText(data) 
    }

    static statusHtml(active) {
        if (active && parseInt(active) > 0) {
            return '<span class="label label-success">上架中</span>'
        } else {
            return '<span class="label label-default">已下架</span>'
        }

    }
    static formatMoney(money,wantInt) {
        if (!money){
            if(wantInt) return 0
              return ''
        }
        money=String(money)
        let pos = money.indexOf(".")
        if (pos < 0) return money

        let cents = parseInt(money.substring(pos + 1))
        if (cents > 0) return money

        return money.substring(0, pos)
    }
    static yearOptions() {

        return TimeService.yearOptions()
    }
    

    static chineseDayofWeek(val, formated) {
        return TimeService.chineseDayofWeek(val, formated)
    }
    static BusEmitError(error, title) {
        console.log(error)
        let msgtitle = title
        if (error.data && error.data.msg) {
            msgtitle = error.data.msg;
        }
        if (!msgtitle) {
            msgtitle = "系統無回應，請稍後再試"
        }

        Bus.$emit('errors', {
            title: msgtitle,
            status: error.status
        })
    }
    static BusEmitOK(title) {
        let msgtitle = '資料已存檔'
        if (title) msgtitle = title
        let msg = {
            title: msgtitle,
            status: 200
        }

        Bus.$emit('okmsg', msg);
    }
    static dataNotFound(){
        Bus.$emit('errors', {
            title: '查無資料',
            status: 404
        })
    }
    static getUserToRoleUrl(user_id, role) {
        let url = ''
        switch (role) {
            case 'Teacher':
                url = '/teachers/' + user_id + '/create'
                break;
            case 'Volunteer':
                url = '/volunteers/' + user_id + '/create'
                break;
            case 'Admin':
                url = '/admins/' + user_id + '/create'
                break;
            case 'Owner':
            url = '/admins/' + user_id + '/create'
            break;
        }

        return url
    }
    static period(begin, end) {
        return TimeService.period(begin, end)
    }
    static hasOptionValue(options, value) {
        let hasMatch = false
        for (let i = 0; i < options.length; ++i) {
            let item = options[i];

            if (item.value == value) {
                hasMatch = true;
                break;
            }
        }
        return hasMatch
    }
    static buildQuery(url, searchParams) {
        url += '?'
        for (let field in searchParams) {

            let value = searchParams[field]
            url += field + '=' + value + '&'

        }
        return url.substr(0, url.length - 1);

    }
    static removeItem(list, item) {
        list.splice(list.indexOf(item), 1);
    }


}


export default Helper;