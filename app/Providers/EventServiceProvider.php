<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
         'App\Events\UserRegistered' => [
            'App\Listeners\SendActivationToken',
        ],
        // 'App\Events\TeacherCreated' => [
        //     'App\Listeners\SendActivationToken',
        // ],

       

    ];

     protected $subscribe = [
        'App\Listeners\SignupEventListener',
        'App\Listeners\TeacherEventListener',
        'App\Listeners\VolunteerEventListener',
        'App\Listeners\ContactInfoEventListener',
        'App\Listeners\CourseEventListener',
        'App\Listeners\StudentEventListener',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
