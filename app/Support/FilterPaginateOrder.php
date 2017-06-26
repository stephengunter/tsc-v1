<?php

namespace App\Support;

use Validator;

trait FilterPaginateOrder {

    protected $operators = [
        'equal_to' => '=',
        'not_equal' => '<>',
        'less_than' => '<',
        'greater_than' => '>',
        'less_than_or_equal_to' => '<=',
        'greater_than_or_equal_to' => '>=',
        'in' => 'IN',
        'not_in' => 'NOT_IN',
        'like' => 'LIKE',
        'between' => 'BETWEEN'
    ];

    public function scopeFilterPaginateOrder($query)
    {
        $request = request();

        $v = Validator::make($request->all(), [
            // 'column' => 'required|in:'.implode(',', $this->filter),
            'direction' => 'required|in:asc,desc',
            'per_page' => 'required|integer|min:1',
            'search_operator' => 'required|in:'.implode(',', array_keys($this->operators)),
            'search_column' => 'required|in:'.implode(',', $this->filter),
            'search_query_1' => 'max:255',
            'search_query_2' => 'max:255'
        ]);

        if($v->fails()) {

            //for debug
            dd($v->messages());
        }

        if($request->has('search_query_1')){
            $column=$request->search_column;
            $operator=$request->search_operator;

            if($this->isRelatedColumn($request)) {
                $keys=explode('.', $column);
                $relate='';
                $length=count($keys);                      
                for ($i = 0; $i <= $length-2; $i++) {
                   $relate = $relate . $keys[$i];
                   if($i < $length-2){
                        $relate = $relate . '.';
                   }
                }
                $fieldName=$keys[$length-1];
             
                $query=$query->whereHas($relate,function($subQuery) use ($fieldName,$operator,$request){
                    $this->buildQuery($fieldName, $operator, $request, $subQuery);
                   
                });
               
            }else{
               $query=$this->buildQuery($column, $operator, $request, $query);
            }
        }

        if(empty($request->column)){
            return $query->paginate($request->per_page);
       
        }else{
            return $query->orderBy($request->column, $request->direction)->paginate($request->per_page);
        }

        

        
    }

    protected function isRelatedColumn($request)
    {
        return strpos($request->search_column, '.') !== false;
    }

    protected function buildQuery($column, $operator, $request, $query)
    {
        switch ($operator) {
            case 'equal_to':
            case 'not_equal':
            case 'less_than':
            case 'greater_than':
            case 'less_than_or_equal_to':
            case 'greater_than_or_equal_to':
                $query->where($column, $this->operators[$operator], $request->search_query_1);
                break;
            case 'in':
                $query->whereIn($column, explode(',', $request->search_query_1));
                break;
            case 'not_in':
                $query->whereNotIn($column, explode(',', $request->search_query_1));
                break;
            case 'like':
                $query->where($column, 'like', '%'.$request->search_query_1.'%');
                break;
            case 'between':
                $query->whereBetween($column, [
                    $request->search_query_1,
                    $request->search_query_2
                ]);
                break;
            default:
                throw new Exception('Invalid Search Operator', 1);
                break;
        }

        return $query;
    }
}