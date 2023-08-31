<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BaseTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * refresh seeders since every time tests are running database is clearing up
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
    }
}
