<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy Configuration
    |--------------------------------------------------------------------------
    | When enabled, requests are mapped to a client (tenant) by domain or user
    | credentials via the central admin_erp panel. The client's database
    | credentials come from admin_erp, and the local DB_* values in .env
    | are used as fallback when tenancy is disabled.
    */
    'enabled' => (bool) env('TENANCY_ENABLED', false),

    /*
    | Base URL of the admin_erp tenant API, e.g.
    | http://127.0.0.1:8001/api/tenant
    */
    'api_url' => rtrim((string) env('TENANT_API_URL', 'http://127.0.0.1:8001/api/tenant'), '/'),

    /*
    | Must equal TENANT_API_SECRET in admin_erp.
    */
    'secret' => (string) env('TENANT_API_SECRET', 'ERP_SEC_9d8f7a6b5c4e3f2a1d0e9b8c7a6f5e4d'),

    /*
    | Product and Project identification codes in admin_erp.
    | A domain/user is accepted if its client purchased this product & project.
    */
    'product_code' => strtoupper((string) env('TENANT_PRODUCT_CODE', 'HMS')),
    'project_code' => strtoupper((string) env('TENANT_PROJECT_CODE', 'HMS')),

    /*
    | How long a successful lookup is cached before querying admin_erp again.
    */
    'cache_seconds' => (int) env('TENANT_CACHE_SECONDS', 300),

    /*
    | Grace period to serve stale cache if admin_erp is temporarily unreachable.
    */
    'grace_seconds' => (int) env('TENANT_GRACE_SECONDS', 86400),

    /*
    | HTTP timeout in seconds for requests to admin_erp API.
    */
    'http_timeout' => (int) env('TENANT_HTTP_TIMEOUT', 8),

];

