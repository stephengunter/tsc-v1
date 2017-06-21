class CommonService {
    static activeText(active) {
        if (parseInt(active)) return '上架中'
        return '已下架'
    }
    static activeLabel(active) {
        let style='label label-default'
        if (parseInt(active)) style= 'label label-info'
         let text=this.activeText(active)

        return `<span class="${style}" > ${text} </span>`
    }

    static reviewedText(reviewed) {
        if (parseInt(reviewed)) return '已審核'
        return '未審核'
    }
    static reviewedLabel(reviewed) {
        let style='label label-danger'
        if (parseInt(reviewed)) style = 'label label-success'
        let text=this.reviewedText(reviewed)
        return `<span class="${style}" > ${text} </span>`
      
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
    static boolOptions(){
        return [{
                    text: '是',
                    value: '1'
                }, {
                    text: '否',
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
    

    // static statusHtml(active) {
    //     if (active && parseInt(active) > 0) {
    //         return '<span class="label label-success">上架中</span>'
    //     } else {
    //         return '<span class="label label-default">已下架</span>'
    //     }

    // }
    
    


}


export default CommonService;