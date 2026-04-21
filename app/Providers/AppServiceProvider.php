<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        /**
         * แก้ไขปัญหา Undefined constant "Yajra\Pdo\OCI_DEFAULT"
         * โดยการเช็คว่าถ้ายังไม่มีการนิยามค่านี้ ให้กำหนดเป็น 0 (ค่ามาตรฐาน)
         */
        if (!defined('OCI_DEFAULT')) {
            define('OCI_DEFAULT', 0);
        }
    }
}