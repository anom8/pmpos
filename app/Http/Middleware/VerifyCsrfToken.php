<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
		'api/tikuy*',
		'api/userLog*',
		'api/register*',
		'api/user*',
		'api/post*',
		'api/updateProfile*',
		'api/updatePassword*',
		'api/updatePost*',
		'api/forgotPassword*',
		'api/checkEmail*',
		'api/confirm*',
		'api/redeem*'
    ];
}
