<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'              => CSRF::class,
        'toolbar'           => DebugToolbar::class,
        'honeypot'          => Honeypot::class,
        'invalidchars'      => InvalidChars::class,
        'secureheaders'     => SecureHeaders::class,
        'auth'              => \App\Filters\AuthFilter::class,
        'role'              => \App\Filters\RoleFilter::class,
        'ratelimit'         => \App\Filters\RateLimitFilter::class,
        'security'          => \App\Filters\SecurityHeadersFilter::class,
        'logactivity'       => \App\Filters\LogActivityFilter::class,
        'blockip'           => \App\Filters\BlockIpFilter::class,
        'csrf-token-header' => \App\Filters\CsrfTokenHeaderFilter::class,
    ];

    public array $globals = [
        'before' => [
            'blockip', 
            'security',
            'csrf' => [
            'except' => [
                'api/*', 
                'file/*',
                'advokat/pengajuan/draft',   // ← TAMBAHKAN INI
            ]
        ],
            'invalidchars',
        ],
        'after' => [
            'security',
            'csrf-token-header',
            'toolbar' => ['except' => ['api/*']],
        ],
    ];


    public array $methods = [];
    public array $filters = [];
}