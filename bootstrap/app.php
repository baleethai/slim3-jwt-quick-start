<?php

/**
 * Use (require) the main validation library
 */
 use Respect\Validation\Validator as v;

$missingFolders = [];

if (! file_exists(__DIR__ . "/../config/development.php")) {
    $missingFolders[] = 'Please, remove the .default from config/development.default.php';
}

if (! file_exists(__DIR__ . "/../config/production.php")) {
    $missingFolders[] = 'Please, remove the .default from config/production.default.php';
}

/**
 * Test to see if the configuration files are corrected
 * placed under the configuration (config) folder
 */
if (! empty($missingFolders)) {
    exit(json_encode([
        'status' => false,
        'message' => 'faild to load application',
        'errors' => $missingFolders
    ]));
}

/**
 * Require the composer autoload file
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * Application mode
 */
$mode = trim(file_get_contents(__DIR__ . '/../mode.txt'));

/**
 * Instanciate a slim application
 */
$app = new \Slim\App([
    'settings' => require __DIR__ . "/../config/{$mode}.php"
]);

/**
 * Get an instance of the container object
 */
$container = $app->getContainer();

/**
 * Configure php.ini according to application mode
 */
ini_set('display_errors', $container['settings']['displayErrorDetails']);

/**
 * Configure Illuminate Database (Laravel package)
 */
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

/**
 * Make Illuminate Database available in the slim container by the name db
 */
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

/**
 * Add to slim container an authentication class object
 */
$container['auth'] = function ($container) {
   return new \App\Auth\Auth($container);
};

/**
 * Add to slim container a wrapper validation class to Respect\Validator library
 */
 $container['validator'] = function ($container) {
     return new App\Validation\Validator;
 };

/**
 * Convert all Generic Exceptions inside the application into JSON responses
 */
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $statusCode = $exception->getCode() ? $exception->getCode() : 500;
        return $container['response']->withStatus($statusCode)
            ->withHeader('Content-Type', 'Application/json')
            ->withJson(["message" => $exception->getMessage()], $statusCode);
    };
};

/**
 * Convert all 405 Exceptions Errors into 405 - Not Allowed
 */
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-Type', 'Application/json')
            ->withHeader("Access-Control-Allow-Methods", implode(",", $methods))
            ->withJson(["message" => "Method not Allowed; Method must be one of: " . implode(', ', $methods)], 405);
    };
};

/**
 * Convert all 404 Exceptions Errors into - Not Found
 */
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'Application/json')
            ->withJson(['message' => 'Page not found']);
    };
};

/**
 * Apply all the middlewares
 */
$app->add(new \Slim\Middleware\JwtAuthentication([
    "regexp" => "/(.*)/", // Regex to find tokens in the headers
    "header" => "token", // Our header token content goes here
    "path" => "/", // Let's cover all the API from the /
    "passthrough" => ["/api/auth/signin", "/api/auth/signup"], // Let's add exceptions in the need for authentication
    "realm" => "Protected",
    "secret" => $container['settings']['secretkey'] // Our created secretkey
]));

/**
 * Add a custom rules (validation) folder to the Respect Validator library
 */
v::with('App\\Validation\\Rules');

/**
 * Include all the necessary routes files
 */
require __DIR__ . '/../routes/api.php';
