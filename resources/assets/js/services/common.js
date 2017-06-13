class CommonService {
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
    static reviewedOptions(){
        return [{
                    text: '已審核',
                    value: '1'
                }, {
                    text: '未審核',
                    value: '0'
                }]
    }
    
    static activeOptions(){
      return  [{
                    text: '上架中',
                    value: '1'
                }, {
                    text: '已下架',
                    value: '0'
                }]
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
    

    static statusHtml(active) {
        if (active && parseInt(active) > 0) {
            return '<span class="label label-success">上架中</span>'
        } else {
            return '<span class="label label-default">已下架</span>'
        }

    }
    
    


}


export default CommonService;