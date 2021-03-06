<?php

return [

	'backend' =>[
		'name' => '慈大社推中心',
		'url' => 'http://tsc-master',
		'spa' => false
	],
	'frontend' =>[
		'name' => '慈大社推中心',
		'url' => 'http://tsc',
		'spa' => true
	],

	'default_password'=> '000000',

	'name' => '慈大社推中心',

	'dev'=>array(
		'name' => 'stephen',
		'email' =>'traders.com.tw@gmail.com',
		'phone' => '0936060049',
	 ),

	'areas' => [
		[
			'value' => 1,
			'text' => '宜花東'
		],
		[
			'value' => 2,
			'text' => '北北基'
		],
		[
			'value' => 3,
			'text' => '桃竹苗'
		],
		[
			'value' => 4,
			'text' => '中彰投'
		],
		[
			'value' => 5,
			'text' => '雲嘉南'
		],
		[
			'value' => 6,
			'text' => '高高屏'
		]
	 ],



	'file_upload' => [
		
		 'default_folder' => '/files/uploads/',
 
	],

	'downloads' => [
		
		'folder' => '/downloads/',
 
	],

	'course' => [
		
		'close_before_days' => 1,  // 開始前??天截止報名
		'bird_discount_name' => '早鳥優惠'
		
	],
	
	'credit_course' => [
		'centers' => ['A'],  // 有學分班的中心
		'types' => [
			[
				'value' => 1,
				'text' => '進修學分班',
				'attach' => false
			],
			[
				'value' => 2,
				'text' => '學士學分班隨班附讀',
				'attach' => true
			],
			[
				'value' => 3,
				'text' => '碩士學分班隨班附讀',
				'attach' => true
			]
		 ],
		 'colleges' =>[
			[
				'value' => 1,
				'text' => '文學院',
				
			],
			[
				'value' => 2,
				'text' => '醫學院',
				
			],
			[
				'value' => 3,
				'text' => '工學院',
				
			]
		 ]
   ],

	

	'payways' => [
		[
			'value' => 0,
			'text' => '信用卡'
		],
		[
			'value' => 1,
			'text' => '便利商店繳費'
		],
		[
			'value' => 2,
			'text' => '現金收付'
		]
	 ],
 


	/*
		    |--------------------------------------------------------------------------
		    | Application Environment
		    |--------------------------------------------------------------------------
		    |
		    | This value determines the "environment" your application is currently
		    | running in. This may determine how you prefer to configure various
		    | services your application utilizes. Set this in your ".env" file.
		    |
	*/

	// 'env' => env('APP_ENV', 'production'),
	'env' => env('APP_ENV', 'local'),

	/*
		    |--------------------------------------------------------------------------
		    | Application Debug Mode
		    |--------------------------------------------------------------------------
		    |
		    | When your application is in debug mode, detailed error messages with
		    | stack traces will be shown on every error that occurs within your
		    | application. If disabled, a simple generic error page is shown.
		    |
	*/

	'debug' => env('APP_DEBUG', true),

	/*
		    |--------------------------------------------------------------------------
		    | Application URL
		    |--------------------------------------------------------------------------
		    |
		    | This URL is used by the console to properly generate URLs when using
		    | the Artisan command line tool. You should set this to the root of
		    | your application so that it is used when running Artisan tasks.
		    |
	*/

	'url' => env('APP_URL', 'http://localhost'),

	/*
		    |--------------------------------------------------------------------------
		    | Application Timezone
		    |--------------------------------------------------------------------------
		    |
		    | Here you may specify the default timezone for your application, which
		    | will be used by the PHP date and date-time functions. We have gone
		    | ahead and set this to a sensible default for you out of the box.
		    |
	*/

	'timezone' => 'UTC',

	/*
		    |--------------------------------------------------------------------------
		    | Application Locale Configuration
		    |--------------------------------------------------------------------------
		    |
		    | The application locale determines the default locale that will be used
		    | by the translation service provider. You are free to set this value
		    | to any of the locales which will be supported by the application.
		    |
	*/

	'locale' => 'en',

	/*
		    |--------------------------------------------------------------------------
		    | Application Fallback Locale
		    |--------------------------------------------------------------------------
		    |
		    | The fallback locale determines the locale to use when the current one
		    | is not available. You may change the value to correspond to any of
		    | the language folders that are provided through your application.
		    |
	*/

	'fallback_locale' => 'en',

	/*
		    |--------------------------------------------------------------------------
		    | Encryption Key
		    |--------------------------------------------------------------------------
		    |
		    | This key is used by the Illuminate encrypter service and should be set
		    | to a random, 32 character string, otherwise these encrypted strings
		    | will not be safe. Please do this before deploying an application!
		    |
	*/

	'key' => env('APP_KEY','base64:GfVTGv85JMAIrdfkGoM4SIi8uSrta3dZB0pD1x/7uhk='),

	'cipher' => 'AES-256-CBC',

	/*
		    |--------------------------------------------------------------------------
		    | Logging Configuration
		    |--------------------------------------------------------------------------
		    |
		    | Here you may configure the log settings for your application. Out of
		    | the box, Laravel uses the Monolog PHP logging library. This gives
		    | you a variety of powerful log handlers / formatters to utilize.
		    |
		    | Available Settings: "single", "daily", "syslog", "errorlog"
		    |
	*/
	
    'log' => 'errorlog',
	// 'log' => env('APP_LOG', 'single'),

	'log_level' => env('APP_LOG_LEVEL', 'debug'),

	/*
		    |--------------------------------------------------------------------------
		    | Autoloaded Service Providers
		    |--------------------------------------------------------------------------
		    |
		    | The service providers listed here will be automatically loaded on the
		    | request to your application. Feel free to add your own services to
		    | this array to grant expanded functionality to your applications.
		    |
	*/

	'providers' => [

		/*
			         * Laravel Framework Service Providers...
		*/
		Illuminate\Auth\AuthServiceProvider::class,
		Illuminate\Broadcasting\BroadcastServiceProvider::class,
		Illuminate\Bus\BusServiceProvider::class,
		Illuminate\Cache\CacheServiceProvider::class,
		Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
		Illuminate\Cookie\CookieServiceProvider::class,
		Illuminate\Database\DatabaseServiceProvider::class,
		Illuminate\Encryption\EncryptionServiceProvider::class,
		Illuminate\Filesystem\FilesystemServiceProvider::class,
		Illuminate\Foundation\Providers\FoundationServiceProvider::class,
		Illuminate\Hashing\HashServiceProvider::class,
		Illuminate\Mail\MailServiceProvider::class,
		Illuminate\Notifications\NotificationServiceProvider::class,
		Illuminate\Pagination\PaginationServiceProvider::class,
		Illuminate\Pipeline\PipelineServiceProvider::class,
		Illuminate\Queue\QueueServiceProvider::class,
		Illuminate\Redis\RedisServiceProvider::class,
		Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
		Illuminate\Session\SessionServiceProvider::class,
		Illuminate\Translation\TranslationServiceProvider::class,
		Illuminate\Validation\ValidationServiceProvider::class,
		Illuminate\View\ViewServiceProvider::class,

		/*
			         * Package Service Providers...
		*/
		Laravel\Tinker\TinkerServiceProvider::class,
		Laravel\Passport\PassportServiceProvider::class,
		Barryvdh\Cors\ServiceProvider::class,
		Barryvdh\DomPDF\ServiceProvider::class,
		Maatwebsite\Excel\ExcelServiceProvider::class,

		/*
			         * Application Service Providers...
		*/
		App\Providers\AppServiceProvider::class,
		App\Providers\AuthServiceProvider::class,
		// App\Providers\BroadcastServiceProvider::class,
		App\Providers\EventServiceProvider::class,
		App\Providers\RouteServiceProvider::class,

		DaveJamesMiller\Breadcrumbs\ServiceProvider::class,
		Collective\Html\HtmlServiceProvider::class,
		Zizaco\Entrust\EntrustServiceProvider::class,
		Intervention\Image\ImageServiceProvider::class

	],

	/*
		    |--------------------------------------------------------------------------
		    | Class Aliases
		    |--------------------------------------------------------------------------
		    |
		    | This array of class aliases will be registered when this application
		    | is started. However, feel free to register as many as you wish as
		    | the aliases are "lazy" loaded so they don't hinder performance.
		    |
	*/

	'aliases' => [

		'App' => Illuminate\Support\Facades\App::class,
		'Artisan' => Illuminate\Support\Facades\Artisan::class,
		'Auth' => Illuminate\Support\Facades\Auth::class,
		'Blade' => Illuminate\Support\Facades\Blade::class,
		'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
		'Bus' => Illuminate\Support\Facades\Bus::class,
		'Cache' => Illuminate\Support\Facades\Cache::class,
		'Config' => Illuminate\Support\Facades\Config::class,
		'Cookie' => Illuminate\Support\Facades\Cookie::class,
		'Crypt' => Illuminate\Support\Facades\Crypt::class,
		'DB' => Illuminate\Support\Facades\DB::class,
		'Eloquent' => Illuminate\Database\Eloquent\Model::class,
		'Event' => Illuminate\Support\Facades\Event::class,
		'File' => Illuminate\Support\Facades\File::class,
		'Gate' => Illuminate\Support\Facades\Gate::class,
		'Hash' => Illuminate\Support\Facades\Hash::class,
		'Lang' => Illuminate\Support\Facades\Lang::class,
		'Log' => Illuminate\Support\Facades\Log::class,
		'Mail' => Illuminate\Support\Facades\Mail::class,
		'Notification' => Illuminate\Support\Facades\Notification::class,
		'Password' => Illuminate\Support\Facades\Password::class,
		'Queue' => Illuminate\Support\Facades\Queue::class,
		'Redirect' => Illuminate\Support\Facades\Redirect::class,
		'Redis' => Illuminate\Support\Facades\Redis::class,
		'Request' => Illuminate\Support\Facades\Request::class,
		'Response' => Illuminate\Support\Facades\Response::class,
		'Route' => Illuminate\Support\Facades\Route::class,
		'Schema' => Illuminate\Support\Facades\Schema::class,
		'Session' => Illuminate\Support\Facades\Session::class,
		'Storage' => Illuminate\Support\Facades\Storage::class,
		'URL' => Illuminate\Support\Facades\URL::class,
		'Validator' => Illuminate\Support\Facades\Validator::class,
		'View' => Illuminate\Support\Facades\View::class,

		'Breadcrumbs' => DaveJamesMiller\Breadcrumbs\Facade::class,
		'Form' => Collective\Html\FormFacade::class,
     	'Html' => Collective\Html\HtmlFacade::class,
	 	'Entrust'   => Zizaco\Entrust\EntrustFacade::class,
		'Image' => Intervention\Image\Facades\Image::class,
		'PDF' => Barryvdh\DomPDF\Facade::class,
		'Excel' => Maatwebsite\Excel\Facades\Excel::class,

	],

];
