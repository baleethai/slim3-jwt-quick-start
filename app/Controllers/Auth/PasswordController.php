<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{
    public function postChangePassword($request, $response)
    {
        $password = $this->auth->user()->password;

        $this->validator->setAliases([
            'password' => 'Password',
            'password_old' => 'Current Password'
        ]);

        $validation = $this->validator->validate($request, [
            'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($password),
            'password' => v::noWhitespace()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $response->withJson([
                'status' => false,
                'message' => 'Could not change password',
                'errors' => $this->validator->getErrors()
            ], 200)->withHeader('Content-type', 'application/json');
        }

        $this->auth->user()->setPassword($request->getParam('password'));

        return $response->withJson([
            'status' => true,
            'message' => 'Your password was changed'
        ], 200)->withHeader('Content-type', 'application/json');
    }
}
