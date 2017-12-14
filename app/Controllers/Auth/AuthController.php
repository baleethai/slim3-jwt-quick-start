<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function postSignOut($request, $response)
    {
        $this->auth->logout();

        return $response->withJson([
            'status' => true,
            'message' => 'user loggedout successfully'
        ], 200)->withHeader('Content-type', 'application/json');
    }

    public function postSignIn($request, $response)
    {
        $container = $this->container->get("settings");
        $key = $container['secretkey'];

        $user = new \stdClass();

        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password'),
            $user
        );

        if (! $auth) {
            return $response->withJson([
                'status' => false,
                'message' => 'could not authenticate'
            ], 401)->withHeader('Content-type', 'application/json');
        }

        $jwtToken = JWT::encode($user->id, $key);

        return $response->withJson([
            'status' => true,
            'message' => 'user logged successfully',
            'token' => $jwtToken,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->fullName(),
                'email' => $user->email
            ]
        ], 200)->withHeader('Content-type', 'application/json');
    }

    public function postSignUp($request, $response)
    {
        $this->validator->setAliases([
            'first_name' => 'Firstname',
            'last_name' => 'Lastname',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password'
        ]);

        $validation = $this->validator->validate($request, [
            'first_name' => v::noWhitespace()->notEmpty()->alpha(),
            'last_name' => v::noWhitespace()->notEmpty(),
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'username' => v::noWhitespace()->notEmpty(),
            'password' => v::noWhitespace()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $response->withJson([
                'status' => false,
                'message' => 'Register failed',
                'errors' => $this->validator->getErrors()
            ], 200)->withHeader('Content-type', 'application/json');
        }

        $user = User::create([
            'first_name' => $request->getParam('first_name'),
            'last_name' => $request->getParam('last_name'),
            'email' => $request->getParam('email'),
            'username' => $request->getParam('username'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT)
        ]);

        $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password'),
            $user
        );

        return $response->withJson([
            'status' => false,
            'message' => 'You have been signed up!',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->getFullName(),
                'email' => $user->email
            ]
        ], 200)->withHeader('Content-type', 'application/json');
    }
}
