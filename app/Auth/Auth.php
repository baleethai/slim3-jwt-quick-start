<?php

namespace App\Auth;

use App\Models\User;
use Firebase\JWT\JWT;

class Auth
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function user()
    {
        $settings = $this->container->get('settings');
        $key = $settings['secretkey'];
        $token = array_shift($this->container->request->getHeader('token'));
        $id = JWT::decode($token, $key, array('HS256'));
        return User::find($id);
    }

    public function check()
    {
        $settings = $this->container->get('settings');
        $key = $settings['secretkey'];
        $token = array_shift($this->container->request->getHeader('token'));
        $id = JWT::decode($token, $key, array('HS256'));
        $id = (int) $id;
        return isset($id) && $id > 0;
    }

    public function attempt($email, $password, &$user)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            return true;
        }

        return false;
    }
}
