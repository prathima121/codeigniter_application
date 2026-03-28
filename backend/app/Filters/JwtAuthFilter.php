<?php

namespace App\Filters;

use App\Libraries\JwtService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(.*)$/i', $header, $matches)) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Authorization token is required.',
            ])->setStatusCode(401);
        }

        $jwt = trim($matches[1]);
        $payload = (new JwtService())->validateToken($jwt);
        if ($payload === null) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ])->setStatusCode(401);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
