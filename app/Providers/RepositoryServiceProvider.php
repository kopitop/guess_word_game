<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\RoomRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Repositories\Eloquent\WordRepository;
use App\Repositories\Contracts\WordRepositoryInterface;
use App\Repositories\Eloquent\MessageRepository;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\Eloquent\ResultRepository;
use App\Repositories\Contracts\ResultRepositoryInterface;
use App;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind(UserRepositoryInterface::class, UserRepository::class);
        App::bind(RoomRepositoryInterface::class, RoomRepository::class);
        App::bind(WordRepositoryInterface::class, WordRepository::class);
        App::bind(MessageRepositoryInterface::class, MessageRepository::class);
        App::bind(ResultRepositoryInterface::class, ResultRepository::class);
    }
}
