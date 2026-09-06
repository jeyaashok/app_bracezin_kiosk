<?php

namespace App\Providers;

use App\Http\Exceptions\ErrorResponse;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance([
            'ErrorResponse' => ErrorResponse::class,
            'JWTAuth' => JWTAuth::class,
        ]);

        $loader->register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Schema::defaultStringLength(191);
        setlocale(LC_MONETARY, 'en_IN.UTF-8');
        Relation::morphMap([
            'users' => 'App\Models\User',
            'user' => 'App\Models\User',
            'media' => 'Directory\Models\Media',
            'comment' => 'Remark\Models\Comment',
            'feedback' => 'Remark\Models\Feedback',
            'setting' => 'Setting\Models\Setting',
            'menu_group' => 'Layout\Models\MenuGroup',
            'menu' => 'Layout\Models\Menu',
            'submenu' => 'Layout\Models\SubMenu',
        ]);
    }
}
