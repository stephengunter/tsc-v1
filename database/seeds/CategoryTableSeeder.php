<?php

use App\Category;
use App\Repositories\Categories;
use Illuminate\Database\Seeder;

use Illuminate\Http\File;

class CategoryTableSeeder extends Seeder
{
	public function __construct(Categories $categories) 
    {
        $this->categories=$categories;
		
	}
    public function run() {
		$dev=config('app.dev');
		$email=$dev['email'];

		$current_user=\App\User::where('email',$email)->first();


		$path='import/categories.xlsx';

        $file=new File(storage_path($path));

        $this->categories->importCategories($file,$current_user);

		
	}
}
