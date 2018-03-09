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

class GetExamAnswerSheetTest extends TestCase {

    private $status = [200, 302];

    public function testNotExist() {

        $rand = rand(1, 100000);
        while (RegularQuiz::whereId($rand) != null)
            $rand = rand(1, 100000);

        $response = $this->post(route('get_exam_answer_sheet_template', ['exam_id' => $rand]));

        $response->assertStatus($this->status);
    }

    public function testExist() {

        $response = $this->post(route('get_exam_answer_sheet_template', ['exam_id' => RegularQuiz::first()]));

        $response->assertStatus($this->status);
    }

}