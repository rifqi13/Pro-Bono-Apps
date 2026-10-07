<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ============================================================
        // SECURITY HEADERS (BASIC)
        // ============================================================
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        if (ENVIRONMENT === 'production') {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // ============================================================
        // CONTENT SECURITY POLICY (CSP)
        // ============================================================
        $csp = implode('; ', [
            // Default — semua resource default hanya dari domain sendiri
            "default-src 'self'",

            // SCRIPT — CDN yang diizinkan
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "https://code.jquery.com " .
                "https://cdn.datatables.net " .
                "https://cdn.datatables.net/responsive/",

            // STYLE — CDN yang diizinkan
            "style-src 'self' 'unsafe-inline' " .
                "https://fonts.googleapis.com " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "https://cdn.datatables.net " .
                "https://cdn.datatables.net/responsive/",

            // IMAGE — boleh dari manapun via https, data URI, blob
            "img-src 'self' data: blob: https:",

            // FONT — termasuk data: untuk Bootstrap Icons
            "font-src 'self' data: " .
                "https://fonts.gstatic.com " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com",

            // CONNECT (AJAX/fetch) — hanya domain sendiri
            "connect-src 'self'",

            // MEDIA
            "media-src 'self'",

            // OBJECT — block total (untuk keamanan)
            "object-src 'none'",

            // FRAME
            "frame-ancestors 'self'",

            // FORM ACTION
            "form-action 'self'",

            // BASE URI
            "base-uri 'self'",
        ]);

        $response->setHeader('Content-Security-Policy', $csp);
    }
}