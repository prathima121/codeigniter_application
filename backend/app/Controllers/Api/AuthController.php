<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\JwtService;
use App\Models\AuthUserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    private AuthUserModel $authUserModel;
    private JwtService $jwtService;

    public function __construct()
    {
        $this->authUserModel = new AuthUserModel();
        $this->jwtService = new JwtService();
    }

    public function register(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? [];

        $rules = [
            'email' => 'required|valid_email',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'password' => 'required|min_length[8]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $email = strtolower(trim((string) $payload['email']));
        $existing = $this->authUserModel->where('email', $email)->first();
        if ($existing) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Email is already registered.',
            ]);
        }

        $userData = [
            'email' => $email,
            'first_name' => trim((string) $payload['first_name']),
            'last_name' => trim((string) $payload['last_name']),
            'password' => password_hash((string) $payload['password'], PASSWORD_DEFAULT),
        ];

        $userId = $this->authUserModel->insert($userData, true);
        $user = $this->authUserModel->find($userId);

        $token = $this->jwtService->createToken([
            'sub' => (int) $user['id'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => time() + (int) env('auth.jwtExpiry', 86400),
        ]);

        unset($user['password']);

        return $this->response->setStatusCode(201)->setJSON([
            'success' => true,
            'message' => 'User registered successfully.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? [];

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $email = strtolower(trim((string) $payload['email']));
        $user = $this->authUserModel->where('email', $email)->first();

        if (! $user || ! password_verify((string) $payload['password'], (string) $user['password'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Invalid credentials.',
            ]);
        }

        $token = $this->jwtService->createToken([
            'sub' => (int) $user['id'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => time() + (int) env('auth.jwtExpiry', 86400),
        ]);

        unset($user['password']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function profile(): ResponseInterface
    {
        $token = $this->extractToken();
        if ($token === null) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Authorization token is required.',
            ]);
        }

        $payload = $this->jwtService->validateToken($token);
        if ($payload === null || ! isset($payload['sub'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ]);
        }

        $user = $this->authUserModel->find((int) $payload['sub']);
        if (! $user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User not found.',
            ]);
        }

        unset($user['password']);

        return $this->response->setJSON([
            'success' => true,
            'user' => $user,
        ]);
    }

    private function extractToken(): ?string
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (! preg_match('/^Bearer\s+(.*)$/i', $header, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }
}
