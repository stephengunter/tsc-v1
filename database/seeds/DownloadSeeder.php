
<?php

use App\Download;
use Illuminate\Database\Seeder;

class DownloadSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		
		$title='加持第二本普通護照申請書';
		$name='a.doc';
		$this->save($title,$name);


		$title='護照加簽或修正申請書';
		$name='b.pdf';
		$this->save($title,$name);

		
	}

	private function save($title,$name)
	{
		$path='app/public/files/';
	
		$file_path= storage_path($path . $name);

		Download::create([
			'title' => $title,
			'name' => $name,
			'type' => File::extension($file_path),
			'filedata' => base64_encode(file_get_contents($file_path)),
					
		]);
	}
}
	
