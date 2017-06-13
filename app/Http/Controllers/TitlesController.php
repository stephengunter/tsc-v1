<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TitleRequest;

use App\Title;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class TitlesController extends Controller
{
    public function __construct(CheckAdmin $checkAdmin) {
      
		$this->middleware('admin');

        $this->checkAdmin=$checkAdmin;
	}
   
   public function index()
    {
        $titleList=Title::all();
       
        return response()
            ->json([
                'titleList' => $titleList
            ]);
    }
    public function store(TitleRequest $request)
    {
        $values=$request['title'];
       
        $title=Title::create($values);
        return response()->json($title);
    }

    public function update(TitleRequest $request, $id)
    {
         $title=Title::findOrFail($id); 
         $values=$request['title'];
         $title->update($values);

          return response()->json($title);
    }

    public function destroy($id)
    {
         $title=Title::findOrFail($id); 

     
       $title->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }
    
}
