<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['products.create', 'products.edit'], function ($view) {
            $categories = \App\Models\Category::orderBy('name')->get();

            $view->with([
                'categories'      => $view->categories ?? $categories,
                'specSections'    => config('product_specs'),
                'categorySpecMap' => $categories->mapWithKeys(fn($c) => [
                    $c->id => app(\App\Http\Controllers\ProductController::class)
                        ->resolveSpecKey($c->name),
                ])->toArray(),
            ]);
        });
    }
}