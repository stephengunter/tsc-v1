class UrlService
{
   static buildQuery(url, searchParams) {
      url += '?'
      for (let field in searchParams) {

          let value = searchParams[field]
          url += field + '=' + value + '&'

      }
      return url.substr(0, url.length - 1);

   }
   static getShowUrl(source, id) {
      return source + '/' + id
   }
   static getEditUrl(source,id) {
      return this.getShowUrl(source) + '/' + id + '/edit'
   }
   static getCreateUrl(source) {
      return source + '/create'
   }
   static getUpdateUrl(source,id) {
      return this.getShowUrl(source,id)
   }
   static getDeleteUrl(source,id) {
      return source + '/' + id
   }
   
}

export default UrlService;