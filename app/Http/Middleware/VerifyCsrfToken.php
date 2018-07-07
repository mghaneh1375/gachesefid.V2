<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except_urls = [
        'showRSSGach',
        'showRSSIrysc',
        'get_exam_answer_sheet_template',
        'getROQ',
        'checkAuth',
        'paymentPostQuiz/*/*',
        'multiPaymentPostQuiz/*/*',
        'paymentPostSelfQuiz/*'
    ];

    public function handle($request, Closure $next) {

        $regex = '#' . implode('|', $this->except_urls) . '#';

        if ($this->isReading($request) || $this->tokensMatch($request) || preg_match($regex, $request->path()))
        {
            return $this->addCookieToResponse($request, $next($request));
        }

        throw new TokenMismatchException;
    }

}
