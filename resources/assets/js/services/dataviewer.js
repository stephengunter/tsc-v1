class DataViewerService {
    constructor(source, order, direction ,search, perPage,search_params) {
        this.source=source
        this.order=order
        this.direction=direction
        this.search=search
        this.perPage=perPage
        this.search_params=search_params

    }
    getOperators(){
        return {
                    like: 'LIKE',
                    equal_to: '=',
                    not_equal: '<>',
                    less_than: '<',
                    greater_than: '>',
                    less_than_or_equal_to: '<=',
                    greater_than_or_equal_to: '>=',
                    in: 'IN',
                    not_in: 'NOT IN',
                    between: 'BETWEEN'
                }
    }
    getParams() {
        
        return {
                    column: this.order,  
                    direction: this.direction,
                    per_page: this.perPage,
                    page: 1,
                    search_column: this.search,
                    search_operator: 'like',
                    search_query_1: '',
                    search_query_2: ''
                }
    }
    buildURL() {
            let url=this.source + '?'
            if(this.search_params){
                let searchParams=this.search_params
                for (let field in searchParams) {

                  let value=searchParams[field]
                  url += field + '=' + value + '&'

                }
            }
            var p = this.params
           
            url += `column=${p.column}&direction=${p.direction}&per_page=${p.per_page}&page=${p.page}&search_column=${p.search_column}&search_operator=${p.search_operator}&search_query_1=${p.search_query_1}&search_query_2=${p.search_query_2}`
         
            return url

    }
    
   
    
    


}


export default DataViewerService;