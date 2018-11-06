<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'showRSSGach',
        'showRSSIrysc',
        'get_exam_answer_sheet_template',
        'getROQ',
        'checkAuth',
        'multiPaymentPostQuiz/*',
        'paymentPostQuiz/*',
        'chargeAccountPost/*',
        'getExams',
        'getMyStudents'
    ];
}

