class Helper {
    
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
    static genderOptions() {
        return [{
            text: '先生',
            value: 1
        }, {
            text: '小姐',
            value: 0
        }]
    }
    static datetimePickerOption() {
        let option = {
            type: 'day',
            week: ['一', '二', '三', '四', '五', '六', '日'],
            month: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            format: 'YYYY-MM-DD',
            buttons: {
                ok: '確定',
                cancel: '取消'
            },
            placeholder: '',
            inputStyle: {
                'display': 'block',
                'width': '100%',
                'height': '34 px',
                'padding': '6px 12px',
                'line-height': '1.42857143',
                'font-size': '14px',
                'border': '1px solid #ccc',
                'background-color': '#fff',
                'box-shadow': '0 1px 3px 0 rgba(0, 0, 0, 0.2)',
                'border-radius': '4px',
                'color': '#555'
            },
        }
        return option
    }
    static numberOptions(min, max, desc) {
        let options = []
        if (desc) {
            for (var i = max; i >= min; i--) {
                let option = {
                    text: i,
                    value: i
                }
                options.push(option)
            }
        } else {
            for (var i = min; i <= max; i++) {
                let option = {
                    text: i,
                    value: i
                }
                options.push(option)
            }
        }


        return options
    }
    static getTimeobj(val) {
        if (!val) return null
        val = String(val)
        let hour = ''
        let minute = ''
        if (val.length > 3) {
            hour = val.substr(0, 2)
            minute = val.substr(2, 2)
        } else {
            hour = val.substr(0, 1)
            minute = val.substr(1, 2)
        }
        return { HH: hour, mm: minute }
    }
    static defaultTimeObj(hour, minute) {
        return { HH: hour, mm: minute }
    }
    static getTimeSelected(val) {
        let hour = parseInt(val.HH)
        let minute = parseInt(val.mm)
        return (hour * 100) + minute
    }
    static timeString(val) {
        let timeObj = this.getTimeobj(val)
        if (!timeObj) return ''
        return timeObj.HH + ':' + timeObj.mm
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
    static classTimeFullText(classTime) {
        let day = classTime.weekday.text
        let on = this.timeString(classTime.on)
        let off = this.timeString(classTime.off)
        return day + ' ' + on + ' - ' + off
    }

    static statusHtml(active) {
        if (active && parseInt(active) > 0) {
            return '<span class="label label-success">上架中</span>'
        } else {
            return '<span class="label label-default">已下架</span>'
        }

    }
    
    static yearOptions() {
        let thisYear = Moment().year()
        let max = thisYear + 1
        let min = thisYear - 15

        return this.numberOptions(min, max)
    }
    static termYearOptions() {
        let thisYear = Moment().year() - 1911
        let max = thisYear + 1
        let min = thisYear - 15
        let desc = true
        return this.numberOptions(min, max, desc)
    }
    static termOrderOptions() {
        let max = 5
        let min = 1
        return this.numberOptions(min, max)
    }

    static chineseDayofWeek(val, formated) {
        let date = Moment(val)
        let day = ""
        switch (date.day()) {
            case 0:
                day = "日";
                break;
            case 1:
                day = "一";
                break;
            case 2:
                day = "二";
                break;
            case 3:
                day = "三";
                break;
            case 4:
                day = "四";
                break;
            case 5:
                day = "五";
                break;
            case 6:
                day = "六";
        }
        if (formated) return '(' + day + ')'
        return day
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
   
    


}


export default Helper;