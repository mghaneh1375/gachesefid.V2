<?php

namespace Tests\Feature;

use App\models\RegularQuiz;
use App\models\School;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{

    private $status = [200, 302];


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $response = $this->get('/');

        $response->assertStatus($this->status);
    }
    
    public function testPack1()
    {
        $routes = ['login', 'aboutUs', 'schoolsList', 'ranking1'];

        foreach ($routes as $itr) {
            $response = $this->get(route($itr));
            $response->assertStatus($this->status);
        }
    }

    public function testRanking() {

        $response = $this->get(route('ranking', ['quizId' => RegularQuiz::first()->id]));
        $response->assertStatus($this->status);
    }

    public function testSchoolStudent() {

        $response = $this->get(route('schoolStudent', ['sId' => School::first()->uId]));
        $response->assertStatus($this->status);
    }

    public function testGetQuizReport() {

        $response = $this->get(route('getQuizReport', ['quizId' => RegularQuiz::first()->id]));
        $response->assertStatus($this->status);
    }

    public function testQuizReports() {

        $routes = ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7',
            'printA5', 'A1Excel', 'A2Excel', 'A4Excel', 'A5Excel', 'A6Excel', 'A7Excel'];

        foreach ($routes as $itr) {

            $response = $this->get(route($itr, ['quizId' => RegularQuiz::first()->id]));
            $response->assertStatus($this->status);
        }

    }

    public function testPack2() {

        $routes = ['resetPas', 'registration', 'getActivation', 'groupRegistration', 'groupQuizRegistration'];

        foreach ($routes as $itr) {
            $response = $this->get(route($itr));
            $response->assertStatus($this->status);
        }
    }

    public function testUserInfo() {

        $response = $this->get(route('userInfo', ['selectedPart' => 'necessary']));
        $response->assertStatus($this->status);
    }

    public function testPack3() {

        $user = User::first();

        Auth::attempt(['username' => $user->username, 'password' => $user->password]);

        $routes = ['userInfo', 'changePas', 'profile', 'seeResult', 'logout'];

        foreach ($routes as $itr) {
            $response = $this->get(route($itr));
            $response->assertStatus($this->status);
        }
    }




}
