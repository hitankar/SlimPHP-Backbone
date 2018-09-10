<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Entity\PatientEntity;
use \Mapper\PatientMapper;

$app->get('/patients', function (Request $request, Response $response) {
    $parameter = $request->getQueryParams();
    $search = null;
    if (count($parameter) > 0 && urldecode($parameter['search']) !== '') {
        $search = urldecode($parameter['search']);
    }
    $this->logger->addInfo("Patient List");
    $mapper = new PatientMapper($this->db);
    $patients = $mapper->getPatients($search);
    $response->withHeader( 'Content-Type', 'application/json' );
    return $this->view->render($response, $patients, 200);
});

$app->get('/patients/{id}', function (Request $request, Response $response, $args) {
    $patient_id = (int)$args['id'];
    $mapper = new PatientMapper($this->db);
    $patient = $mapper->getPatientById($patient_id);
    $response->withHeader( 'Content-Type', 'application/json' );
    if (!$patient) {
            return $this->view->render($response, [
            'error'     => true,
            'msg'       => 'Patient not found'
        ], 404);
    }
    return $this->view->render($response, [
        'patient' => $patient,
        'error'     => false,
        'msg'       => null
    ], 200);
})->setName('patient-detail');

$app->post('/patients', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $patient_data = [];
    $patient_data['name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $patient_data['address'] = filter_var($data['address'], FILTER_SANITIZE_STRING);
    $patient_data['age'] = filter_var($data['age'], FILTER_SANITIZE_STRING);

    $patient = new PatientEntity($patient_data);
    $patient_mapper = new PatientMapper($this->db);
    $result = $patient_mapper->save($patient);
    return $this->view->render($response, $result, 200);
});


$app->put('/patients/{id}', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $patient_data = [];
    $patient_data['id'] = (int)$args['id'];
    $patient_data['name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $patient_data['address'] = filter_var($data['address'], FILTER_SANITIZE_STRING);
    $patient_data['age'] = filter_var($data['age'], FILTER_SANITIZE_STRING);

    $patient = new PatientEntity($patient_data);
    $patient_mapper = new PatientMapper($this->db);
    $patient_mapper->update($patient);
    return $this->view->render($response, [
        'error'     => false,
        'msg'       => 'Updated Sucessfully'
    ], 204);
});

$app->delete('/patients/{id}', function (Request $request, Response $response, $args) {
    $patient_mapper = new PatientMapper($this->db);
    $patient_mapper->delete((int)$args['id']);
    return $this->view->render($response, [
        'error'     => false,
        'msg'       => 'Deleted Sucessfully'
    ], 204);
});