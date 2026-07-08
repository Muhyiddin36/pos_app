<?php

declare(strict_types=1);

final class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            redirect('dashboard/index');
        }
        $this->view('auth/login', ['title' => 'Masuk'], 'guest');
    }

    public function doLogin(): void
    {
        $this->requireCsrf();

        $username = $this->post('username');
        $password = (string) $this->postRaw('password', '');

        $validator = Validator::make(
            ['username' => $username, 'password' => $password],
            ['username' => 'required|max:50', 'password' => 'required']
        );

        if ($validator->fails()) {
            $_SESSION['_errors'] = $validator->errors();
            $_SESSION['_old_input'] = ['username' => $username];
            $this->withError('Mohon lengkapi username dan password.', 'auth/login');
        }

        $result = Auth::attempt($username, $password);
        if (!$result['ok']) {
            $_SESSION['_old_input'] = ['username' => $username];
            $this->withError($result['message'], 'auth/login');
        }

        unset($_SESSION['_errors'], $_SESSION['_old_input']);
        redirect('dashboard/index');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        redirect('auth/login');
    }
}
