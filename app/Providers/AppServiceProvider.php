<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Models\Userview;
use App\Events\PostPointUpdated;
use App\Events\UserPointUpdated;
use App\Events\UserViewed;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Post::updated(function ($post) {
            if ($post->isDirty('points')) {
                event(new PostPointUpdated($post));
            }
        });

        User::updated(function ($user) {
            if ($user->isDirty('points')) {
                event(new UserPointUpdated($user));
            }
        });

        Userview::created(function($userview) {
            $userDataProfileViews = Userview::where('user_id', $userview->user_id)->count();
            $data = [
               'userDataProfileViews' => $userDataProfileViews
            ];
            event(new UserViewed($userview->user_id, $data));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
