<?php

namespace Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Models\TestUserModel;
use TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    /*
    TODO: nemusia sa posielat smsky, len overit dane akcie, nezabudnut aj overovanie ked uz je overeny
    - napisat si vzsetky nazvy testov
    - chranene nechranene routy (redirecting, atd)
    - aj znovuvyziadanie kodu nezabudnut
    -
    */


    /* @var TestUserModel */
    private $testUser = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->testUser = TestUserModel::create([
            'email' => 'john@example.com',
            'password' => bcrypt('testpass123'),
            'activated' => true,
            'forbidden' => false,
        ]);

        dd($this->testUser);



        //TODO
    }

    private function createTestConfig(): void
    {
//        Config::set('');
    }

    /* @test */
    public function can_send_verification_code()
    {
        $this->actingAs($this->testUser);
    }
}
