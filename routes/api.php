<?php

/**
 * File containing all routes to api applications
 */

/**
 * Use (require) all the necessary
 * application controllers on top of the fle
 */
use \App\Controllers\Pages\HomeController;
use \App\Controllers\Auth\AuthController;
use \App\Controllers\Auth\PasswordController;

$app->group('/api', function () {

    /**
     * Routes responsable for signin/signout and change a user password
     */
    $this->group('/auth', function () {
        $this->post('/signup', AuthController::class . ':postSignUp')->setName('auth.signup');
        $this->post('/signin', AuthController::class . ':postSignIn')->setName('auth.signin');
    });

    $this->group('/auth', function () {
        $this->post('/signout', AuthController::class . ':postSignOut')->setName('auth.signout');
        $this->post('/password/change', PasswordController::class . ':postChangePassword')->setName('auth.password.change');
    });
});
