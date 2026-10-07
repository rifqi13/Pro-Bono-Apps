<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $maxAttempts  = (int) ($arguments[0] ?? 5);
        $decayMinutes = (int) ($arguments[1] ?? 1);

        $key   = 'rl_' . md5($request->getIPAddress() . '|' . $request->getUri()->getPath());
        $cache = cache();
        $data  = $cache->get($key) ?? ['count' => 0, 'reset' => time() + ($decayMinutes * 60)];

        if (time() > $data['reset']) {
            $data = ['count' => 0, 'reset' => time() + ($decayMinutes * 60)];
        }
        $data['count']++;
        $cache->save($key, $data, $decayMinutes * 60);

        if ($data['count'] > $maxAttempts) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(view('errors/429', ['retryAfter' => $data['reset'] - time()]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}