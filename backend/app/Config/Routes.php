<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['filter' => 'cors'], static function ($routes) {
	$routes->options('(:any)', static function () {
		return service('response')->setStatusCode(204);
	});

	$routes->post('register', 'Api\\AuthController::register');
	$routes->post('login', 'Api\\AuthController::login');

	$routes->group('', ['filter' => 'jwtAuth'], static function ($routes) {
		$routes->get('profile', 'Api\\AuthController::profile');
		$routes->post('teachers/create-with-user', 'Api\\TeacherController::createWithUser');
		$routes->get('auth-users', 'Api\\TeacherController::authUsers');
		$routes->get('teachers', 'Api\\TeacherController::teachers');
		$routes->get('teachers-with-users', 'Api\\TeacherController::teachersWithUsers');
	});
});
