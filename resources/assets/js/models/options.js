var terms = this.loadTermOptions()
var centers = this.loadCenterOptions()
var categories = this.loadCategoryOptions()
var loaded = Promise.all([terms, centers, categories]);

loaded.then(() => {
   this.loaded=true
});
function loadTermOptions(){
      return new Promise((resolve, reject) => {
         let url = '/api/terms/options' 
         axios.get(url)
        .then(response => {
            this.termOptions = response.data.options 
           
            let term=this.termOptions[0].value
            this.searchParams.term=term

            resolve(true);
        })
        .catch(error => {
            reject(error.response);
        });
      });
}
function loadCenterOptions(){
    return new Promise((resolve, reject) => {
         let url = '/api/centers/options' 
         axios.get(url)
        .then(response => {
            this.centerOptions = response.data 
            let allCenters={ text:'全部開課中心' , value:'0' }
            this.centerOptions.splice(0, 0, allCenters);
            let center=this.centerOptions[0].value
            this.searchParams.center=center

            resolve(true);
        })
        .catch(error => {
            reject(error.response);
        });
    });
}
function loadCategoryOptions(){
      return new Promise((resolve, reject) => {
        let url = '/api/categories/allOptions'   
         axios.get(url)
        .then(response => {
            this.categoryOptions=response.data.options
            let defaultItem={ text:'全部課程分類' , value:'0' }
            this.categoryOptions.splice(0, 0, defaultItem);
            let category=this.categoryOptions[0].value
            this.searchParams.category=category

            resolve(true);
        })
        .catch(error => {
            reject(error.response);
        });
      });

}