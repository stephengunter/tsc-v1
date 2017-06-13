
export default function(Vue){
    Vue.auth={
        login(form){
            let url='/login'
            let method='post'
            return new Promise((resolve, reject) => {
                form.submit(method,url)
                    .then(data => {
                        resolve(data)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
            
        },
        sendConfirmationMail(email){
             return new Promise((resolve, reject) => {
                     let form = new Form({
                        email:email
                     })
                     let url='/send-confirmation-mail' 
                     form.post(url)
                     .then(data => {
                          resolve(data)
                     })
                     .catch(error => {
                        reject(error)
                     })
                });
        },
        forgotPassword(form){
            return new Promise((resolve, reject) => {
                 let url='/forgot-password' 
                 form.post(url)
                 .then(data => {
                      resolve(data)
                 })
                 .catch(error => {
                    reject(error)
                 })
            });
        },
        changePassword(form){
            return new Promise((resolve, reject) => {
                 
                 let url='/change-password' 
                 form.post(url)
                 .then(data => {
                      resolve(data)
                 })
                 .catch(error => {
                    reject(error)
                 })
            });
        },
        resetPassword(form){
            return new Promise((resolve, reject) => {
                 
                 let url='/reset-password' 
                 form.post(url)
                 .then(data => {
                      resolve(data)
                 })
                 .catch(error => {
                    reject(error)
                 })
            });
        }
        
    }
   

    Object.defineProperties(Vue.prototype, {
        $auth:{
            get:()=>{
                return Vue.auth
            }
        }
    })
}