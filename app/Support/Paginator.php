<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class Paginator 
{
   public static function create(Array $items,Request $request=null, int $page=1,int $perPage=999)
   {
      // $url='';
      // if($request) $url=$request->url();

      // $query='';
      // if($request) $url=$request->query();
     

     
      $items=static::getPagedItems($items,$page, $perPage);
      
      return new LengthAwarePaginator(
          $items, // items
          count($items), // total
          $perPage, // perPage
          $page // currentPage
         
      );    
   }

   public static function init(Array $items , int $page=1,int $perPage=999)
   {
        $items=static::getPagedItems($items,$page, $perPage);

        return new LengthAwarePaginator(
            $items, // items
            count($items), // total
            $perPage, // perPage
            $page // currentPage
        
        );  
   }

   public static function getPagedItems(Array $items,int $page=1,int $perPage=999)
   {
       $offset = ($page * $perPage) - $perPage;
       return array_slice($items, $offset, $perPage, true);
   }
}