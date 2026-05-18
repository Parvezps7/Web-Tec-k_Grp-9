<?php

declare(strict_types=1);

class AuthController extends BaseController
{
    public function login(): void
    {
        if (current_user()) {
            redirect(url('dashboard', 'index'));
        }
        $this->view('auth/login', []);
    }

    public function register(): void
    {
        if (current_user()) {
            redirect(url('dashboard', 'index'));
        }
        $this->view('auth/register', []);
    }

    public function loginpost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('auth', 'login'));
        }
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            flash('error', 'Email and password are required.');
            redirect(url('auth', 'login'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email format.');
            redirect(url('auth', 'login'));
        }
        $db = connect_db();
        $row = User::findByEmail($db, $email);
        if (!$row || !password_verify($password, $row['password'])) {
            flash('error', 'Invalid credentials.');
            redirect(url('auth', 'login'));
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_role'] = $row['role'];
        flash('success', 'Welcome back, ' . $row['name'] . '!');
        redirect(url('dashboard', 'index'));
    }

    public function registerpost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('auth', 'register'));
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');
        $role = trim((string) ($_POST['role'] ?? 'attendee'));

        if ($name === '' || strlen($name) > 100) {
            flash('error', 'Please enter a valid name (max 100 characters).');
            redirect(url('auth', 'register'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email format.');
            redirect(url('auth', 'register'));
        }
        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            redirect(url('auth', 'register'));
        }
        if ($password !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirect(url('auth', 'register'));
        }
        if (!in_array($role, ['attendee', 'organiser'], true)) {
            $role = 'attendee';
        }

        $db = connect_db();
        if (User::findByEmail($db, $email)) {
            flash('error', 'That email is already registered.');
            redirect(url('auth', 'register'));
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        if (!User::create($db, $name, $email, $hash, $role)) {
            flash('error', 'Registration failed. Please try again.');
            redirect(url('auth', 'register'));
        }

        flash('success', 'Account created. You can log in now.');
        redirect(url('auth', 'login'));
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        flash('success', 'You have been logged out.');
        redirect(url('home', 'index'));
    }
}
