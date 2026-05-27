<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Eloquent\StudentRepository;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Contracts\FaceDataRepositoryInterface;
use App\Repositories\Eloquent\FaceDataRepository;
use App\Repositories\Contracts\LogRepositoryInterface;
use App\Repositories\Eloquent\LogRepository;
use App\Services\Contracts\FaceRecognitionServiceInterface;
use App\Services\Eloquent\FaceRecognitionService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(FaceDataRepositoryInterface::class, FaceDataRepository::class);
        $this->app->bind(LogRepositoryInterface::class, LogRepository::class);
        $this->app->bind(FaceRecognitionServiceInterface::class, FaceRecognitionService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
