<?php

namespace Ycore\Html\Providers;

use Illuminate\Support\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHtmlBuilder();
        $this->registerFormBuilder();

        $this->app->bind('Ycore\Html\Contracts\HtmlInterface', 'Ycore\Html\HtmlBuilder');
        $this->app->bind('Ycore\Html\Contracts\FormInterface', 'Ycore\Form\FormBuilder');
        $this->app->bind('Ycore\Html\Contracts\StyleInterface', 'Ycore\Html\Styles\Bootstrap4');

        $this->app->alias('html', 'Ycore\Html\HtmlInterface');
        $this->app->alias('form', 'Ycore\Html\FormInterface');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
            return new \Ycore\Html\HtmlBuilder($app->make('Ycore\Html\Contracts\StyleInterface'));
        });
    }

    /**
     * Register the FORM builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            return new \Ycore\Html\FormBuilder($app['url'], $app->make('Ycore\Html\Contracts\HtmlInterface'), csrf_token());
        });
    }

}
