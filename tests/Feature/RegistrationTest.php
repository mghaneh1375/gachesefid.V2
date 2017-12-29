<?php

namespace Tests\Feature;

use App\models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

include_once __DIR__ . '/../../app/Http/Controllers/Common.php';

class RegistrationTest extends TestCase {

    private $status = [200, 302];

    public function testSameUserNameRegistration() {

        $data = array('doRegistration' => 'تایید', 'username' => User::first()->username, 'password' => '123456', 'firstName' => 'حامد',
            'lastName' => 'reza', 'phoneNum' => '09214915905', 'level' => getValueInfo('studentLevel'), 'sex' => '1');

        $response = $this->post(route('doRegistration'), $data);

        echo "response 1: " . $response->getContent();

        return $response->assertStatus($this->status);

    }

    public function testNewUserNameRegistration() {

        $username = "hamed";

        $rand = rand(10000, 100000);
        while (User::whereUsername($username.$rand)->count() > 0)
            $rand = rand(10000, 100000);

        $username .= $rand;

        $data = ['doRegistration' => 'ok', 'username' => $username, 'password' => '123456', 'firstName' => 'حامد',
            'lastName' => 'reza', 'phoneNum' => '09214915905', 'level' => getValueInfo('studentLevel'), 'sex' => '1'];

        $response = $this->post(route('doRegistration'), $data);

        echo "response 2: " . $response->getContent();

        return $response->assertStatus($this->status);

    }

}