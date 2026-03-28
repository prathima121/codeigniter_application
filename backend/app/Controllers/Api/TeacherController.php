<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AuthUserModel;
use App\Models\TeacherModel;
use CodeIgniter\HTTP\ResponseInterface;

class TeacherController extends BaseController
{
    private AuthUserModel $authUserModel;
    private TeacherModel $teacherModel;

    public function __construct()
    {
        $this->authUserModel = new AuthUserModel();
        $this->teacherModel = new TeacherModel();
    }

    public function createWithUser(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? [];

        $rules = [
            'email' => 'required|valid_email',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'password' => 'required|min_length[8]',
            'university_name' => 'required|min_length[2]',
            'gender' => 'required|in_list[male,female,other]',
            'year_joined' => 'required|integer|greater_than_equal_to[1950]|less_than_equal_to[2099]',
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $email = strtolower(trim((string) $payload['email']));
        if ($this->authUserModel->where('email', $email)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Email is already registered.',
            ]);
        }

        $db = db_connect();
        $db->transBegin();

        $userId = $this->authUserModel->insert([
            'email' => $email,
            'first_name' => trim((string) $payload['first_name']),
            'last_name' => trim((string) $payload['last_name']),
            'password' => password_hash((string) $payload['password'], PASSWORD_DEFAULT),
        ], true);

        if (! $userId) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create user.',
            ]);
        }

        $teacherId = $this->teacherModel->insert([
            'user_id' => (int) $userId,
            'university_name' => trim((string) $payload['university_name']),
            'gender' => strtolower(trim((string) $payload['gender'])),
            'year_joined' => (int) $payload['year_joined'],
        ], true);

        if (! $teacherId || $db->transStatus() === false) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create teacher profile.',
            ]);
        }

        $db->transCommit();

        $user = $this->authUserModel->find((int) $userId);
        $teacher = $this->teacherModel->find((int) $teacherId);
        unset($user['password']);

        return $this->response->setStatusCode(201)->setJSON([
            'success' => true,
            'message' => 'User and teacher records created successfully.',
            'data' => [
                'auth_user' => $user,
                'teacher' => $teacher,
            ],
        ]);
    }

    public function authUsers(): ResponseInterface
    {
        $users = $this->authUserModel
            ->select('id, email, first_name, last_name, created_at, updated_at')
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function teachers(): ResponseInterface
    {
        $teachers = $this->teacherModel
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $teachers,
        ]);
    }

    public function teachersWithUsers(): ResponseInterface
    {
        $rows = $this->teacherModel
            ->select('teachers.id, teachers.user_id, teachers.university_name, teachers.gender, teachers.year_joined, auth_user.email, auth_user.first_name, auth_user.last_name')
            ->join('auth_user', 'auth_user.id = teachers.user_id', 'inner')
            ->orderBy('teachers.id', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $rows,
        ]);
    }
}
