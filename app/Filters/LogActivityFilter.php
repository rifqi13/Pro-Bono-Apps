<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\ActivityLogger;

class LogActivityFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Log hanya untuk method yang mengubah data
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) return;

        $path = $request->getUri()->getPath();
        // Skip path yang tidak perlu dilog
        $skip = ['auth/login', 'auth/logout'];
        foreach ($skip as $s) {
            if (strpos($path, $s) !== false) return;
        }

        ActivityLogger::log(
            strtolower($request->getMethod()) . '_' . str_replace('/', '_', trim($path, '/')),
            explode('/', trim($path, '/'))[0] ?? 'root',
            'Akses: ' . $request->getUri()
        );
    }
}