<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\IpBlocker;

class BlockIpFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();

        // Skip untuk health check
        $path = $request->getUri()->getPath();
        if (in_array($path, ['up', 'health'], true)) return;

        $blocked = IpBlocker::check($ip);
        if ($blocked) {
            $expire = $blocked['expired_at']
                ? 'sampai ' . date('d M Y H:i', strtotime($blocked['expired_at']))
                : 'permanen';

            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/403_blocked', [
                    'ip'     => $ip,
                    'reason' => $blocked['reason'],
                    'expire' => $expire,
                ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}