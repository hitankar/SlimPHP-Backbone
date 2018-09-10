<?php

require '../vendor/autoload.php';
require '../bootstrap/settings.php';

$app = new \Slim\App(["settings" => $config]);

// It will add the Access-Control-Allow-Methods header to every request

$app->add(function($request, $response, $next) {
	$route = $request->getAttribute("route");

	$methods = [];

	if (!empty($route)) {
		$pattern = $route->getPattern();

		foreach ($this->router->getRoutes() as $route) {
			if ($pattern === $route->getPattern()) {
				$methods = array_merge_recursive($methods, $route->getMethods());
			}
		}
		//Methods holds all of the HTTP Verbs that a particular route handles.
	} else {
		$methods[] = $request->getMethod();
	}
	
	$response = $next($request, $response);

	return $response
		->withHeader('Access-Control-Allow-Origin', 'http://apt.sabhomeo.dev')
		// ->withHeader('Access-Control-Allow-Origin', 'http://apt.hitankarray.com')
		->withHeader( 'Content-Type', 'application/json' )
		->withHeader('Access-Control-Allow-Credentials', 'true')
		->withHeader('Access-Control-Allow-Headers', "Origin, X-Requested-With, Content-Type, Accept, Key")
		->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
});

require '../bootstrap/container.php';

spl_autoload_register(function ($classname) {
	require ("../classes/" . str_replace('\\', '/', $classname) . ".php");
});

require '../routes/patient.php';
require '../routes/appointment.php';

$app->post('/login', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $cred = [];
    $cred['email'] = filter_var($data['email'], FILTER_SANITIZE_STRING);
    $cred['word'] = filter_var($data['password'], FILTER_SANITIZE_STRING);

    $patient = new PatientEntity($patient_data);
    $patient_mapper = new PatientMapper($this->db);
    $result = $patient_mapper->save($patient);
    return $this->view->render($response, $result, 200);
});


$app->run();