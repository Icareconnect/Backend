<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = ['admin/delete_filter_option','client/*','godpanel/variables/*','webhook/*','custom/*','register/*','service_provider/*','admin/delete_master_option','admin/delete_symptoms_option','admin/master_slot/edit','al_rajhi_bank/webhook','send_link','query_post'];
}
