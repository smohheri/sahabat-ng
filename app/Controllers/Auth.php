<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->session->get('is_logged_in')) {
            return redirect()->to(role_home((string) $this->session->get('role')));
        }

        $roleModel    = new RoleModel();
        $aksesOptions = $roleModel->options();

        if ($this->request->is('post')) {
            $rules = [
                'username' => 'required|max_length[50]',
                'password' => 'required',
                'akses'    => 'required|in_list[' . implode(',', array_keys($aksesOptions)) . ']',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
            }

            $username = (string) $this->request->getPost('username');
            $password = (string) $this->request->getPost('password');
            $akses    = strtolower((string) $this->request->getPost('akses'));

            // Brute-force protection: batasi percobaan gagal.
            $lockedUntil = (int) ($this->session->get('login_locked_until') ?? 0);
            if ($lockedUntil > time()) {
                return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan gagal. Silakan coba lagi beberapa menit.');
            }

            $user = (new UserModel())->login($username, $password);

            if (! $user) {
                $this->registerLoginFailure();

                return redirect()->back()->withInput()->with('error', 'Username atau password salah!');
            }

            $role = strtolower((string) $user['role']);
            $role = $role === 'guru' ? 'pengajar' : $role;

            if (! $this->aksesMatch($role, $akses)) {
                return redirect()->back()->withInput()->with('error', 'Username atau password salah!');
            }

            $this->session->remove('login_attempts');
            $this->session->remove('login_locked_until');

            $this->session->set([
                'id_user'      => (int) $user['id_user'],
                'nama'         => $user['nama'],
                'username'     => $user['username'],
                'role'         => $role,
                'id_role'      => (int) $user['id_role'],
                'id_anak'      => (int) ($user['id_anak'] ?? 0),
                'id_guru'      => (int) ($user['id_guru'] ?? 0),
                'is_logged_in' => true,
            ]);

            (new UserModel())->update((int) $user['id_user'], ['last_login_at' => date('Y-m-d H:i:s')]);
            log_activity('login', 'Login ke sistem');

            return redirect()->to(role_home($role));
        }

        return view('auth/login', [
            'title'        => 'Login - ' . app_name(),
            'settings'     => $this->settings,
            'aksesOptions' => $aksesOptions,
        ]);
    }

    public function logout()
    {
        if ($this->session->get('is_logged_in')) {
            log_activity('logout', 'Logout dari sistem');
        }

        $this->session->destroy();

        return redirect()->to(site_url('login'))->with('success', 'Anda telah keluar dari sistem.');
    }

    private function aksesMatch(string $userRole, string $selected): bool
    {
        $teachers = ['guru', 'pengajar'];

        if (in_array($userRole, $teachers, true) && in_array($selected, $teachers, true)) {
            return true;
        }

        return $userRole === $selected;
    }

    private function registerLoginFailure(): void
    {
        $attempts = (int) ($this->session->get('login_attempts') ?? 0) + 1;
        $this->session->set('login_attempts', $attempts);

        if ($attempts >= 5) {
            $this->session->set('login_locked_until', time() + 300); // kunci 5 menit
        }
    }
}
