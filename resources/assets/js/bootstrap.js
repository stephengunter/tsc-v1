require('../css/app.css');
require('../css/font-awesome.css');
require('../css/site.css');

require('bootstrap-sass');

import Vue from 'vue';
Vue.component('modal', require('vue-strap/src/modal') )
Vue.component('alert', require('vue-strap/src/alert') )
Vue.component('tooltip', require('vue-strap/src/tooltip') )

import vSelect from 'vue-select'
Vue.component('drop-down', vSelect)
Vue.component('date-picker', require('vue-datepicker') )
Vue.component('time-picker', require('vue2-timepicker') )
Vue.component('pager', require('vue-simple-pager') )

Vue.component('delete-confirm', require('./components/DeleteConfirm') )
Vue.component('toggle', require('./components/Toggle') )
Vue.component('checkbox', require('./components/CheckBox') )
Vue.component('data-viewer', require('./components/DataViewer') )
Vue.component('photo', require('./components/Photo') )
Vue.component('image-upload', require('./components/ImageUpload') )
Vue.component('role-label', require('./components/RoleLabel') )
Vue.component('combination-select', require('./components/CombinationSelect') )


Vue.component('menus', require('./views/menus') )

Vue.component('login', require('./components/auth/login') )
Vue.component('forgot-password', require('./components/auth/password/forgot') )
Vue.component('reset-password', require('./components/auth/password/reset') )
Vue.component('change-password', require('./components/auth/password/change') )
Vue.component('user-card', require('./components/user/card') )

Vue.component('signup-view', require('./views/signups/view') )
Vue.component('signup-index', require('./views/signups/index') )
Vue.component('signup-details', require('./views/signups/details') )
Vue.component('signup-create', require('./views/signups/create') )
Vue.component('new-user-signup', require('./views/signups/new-user-signup') )

Vue.component('tuition-view', require('./views/tuitions/view') )


Vue.component('refund-index', require('./views/refunds/index') )
Vue.component('refund-create', require('./views/refunds/create') )
Vue.component('refund-details', require('./views/refunds/details') )
Vue.component('refund-view', require('./views/refunds/view') )


Vue.component('user-index', require('./views/users/index') )
Vue.component('user-create', require('./views/users/create') )
Vue.component('user-details', require('./views/users/details') )

Vue.component('volunteer-index', require('./views/volunteers/index') )
Vue.component('volunteer-create', require('./views/volunteers/create') )
Vue.component('volunteer-details', require('./views/volunteers/details') )

Vue.component('teacher-index', require('./views/teachers/index') )
Vue.component('teacher-create', require('./views/teachers/create') )
Vue.component('teacher-details', require('./views/teachers/details') )

Vue.component('center-index', require('./views/centers/index') )
Vue.component('center-create', require('./views/centers/create') )
Vue.component('center-details', require('./views/centers/details') )

Vue.component('term-index', require('./views/terms/index') )
Vue.component('holiday-index', require('./views/holidays/index') )
Vue.component('classroom-index', require('./views/classrooms/index') )
Vue.component('title-index', require('./views/titles/index') )
Vue.component('discount-index', require('./views/discounts/index') )



import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common = {   
    // 'Authorization' : 'Bearer ' + Vue.auth.getToken() ,
    'X-Requested-With': 'XMLHttpRequest' 
};
import Moment from 'moment'
import MomentTimeZone from 'moment-timezone'
window.Moment = Moment;
window.MomentTimeZone = MomentTimeZone;

import Form from './utilities/Form';
import Helper from './helper.js'
import Config from './config.js'

import TimeService from './services/time.js'
import CommonService from './services/common.js'

import User from './models/user.js'
import Photo from './models/photo.js'
import ContactInfo from './models/contactinfo.js'
import UserCenters from './models/usercenters.js'
import Address from './models/address.js'
import Course from './models/course.js'
import Signup from './models/signup.js'
import Tuition from './models/tuition.js'
import Refund from './models/refund.js'
import Center from './models/center.js'
import Teacher from './models/teacher.js'
import Volunteer from './models/volunteer.js'

import Term from './models/term.js'
import Holiday from './models/holiday.js'
import Classroom from './models/classroom.js'
import Title from './models/title.js'
import Discount from './models/discount.js'

import Auth from './packages/auth/Auth.js'
Vue.use(Auth)

window.Form = Form
window.Helper = Helper
window.Config = Config

window.TimeService = TimeService
window.CommonService = CommonService;

window.User=User
window.Photo=Photo
window.ContactInfo=ContactInfo
window.Address=Address
window.UserCenters=UserCenters

window.Course=Course
window.Signup=Signup
window.Tuition=Tuition
window.Refund=Refund
window.Teacher=Teacher
window.Volunteer=Volunteer
window.Center=Center

window.Term=Term
window.Holiday=Holiday
window.Classroom=Classroom
window.Title=Title
window.Discount=Discount




window.Vue = Vue;



Vue.filter('tpeTime', function (datetime) {
    return Helper.tpeTime(datetime)
})
Vue.filter('genderText', function (gender) {
        if (parseInt(gender)) return '男'
        return '女'
})
Vue.filter('activeLabel', function (active) {
    let text='已下架'
    let style='label label-default'
    if(parseInt(active)){
        text = '上架中'
        style='label label-info'
    } 
    return `<span class="${style}" > ${text} </span>`
})
Vue.filter('reviewedLabel', function (reviewed) {
    let text='未審核'
    let style='label label-danger'
    if(parseInt(reviewed)){
        text = '已審核'
        style='label label-success'
    } 
    return `<span class="${style}" > ${text} </span>`
})
Vue.filter('showIcon', function (icon) {
    if(!icon)  return ''
    return '<i class="' + icon + '"  aria-hidden="true"></i>'
})
Vue.filter('formatMoney', function (val) {
    return Helper.formatMoney(val)
})
Vue.filter('namesText', function (names) {
    return Helper.namesText(names)
})
Vue.filter('titleHtml', function (title) {
    return Helper.getTitleHtml(title)
})
Vue.filter('tryParseInt', function (val) {
    return Helper.tryParseInt(val)
})

window.Bus = new Vue({});









//window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

//window.$ = window.jQuery = require('jquery');

// Vue.http.interceptors.push((request, next) => {
//     request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;

//     next();
// });