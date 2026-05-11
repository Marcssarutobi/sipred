<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\VerifierStockAlerteJob;
use App\Jobs\AnalyseProduitsPlusVendusJob;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new VerifierStockAlerteJob())->everyMinute()->withoutOverlapping();
Schedule::job(new AnalyseProduitsPlusVendusJob())->weeklyOn(1, '02:00')->withoutOverlapping();
