<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Entity\AppointmentEntity;
use \Entity\DiscoveredSymptomEntity;
use \Entity\AdministeredDrugEntity;
use \Mapper\AppointmentMapper;

$app->post('/appointments', function (Request $request, Response $response, $args) {
	$data = $request->getParsedBody();
	$appointment_data = [];
	$appointment_data['next_appointment'] = filter_var($data['next_appointment'], FILTER_SANITIZE_STRING);
	$appointment_data['patient_id'] = (int)filter_var($data['patient']);
	$appointment_data['note'] = filter_var($data['note'], FILTER_SANITIZE_STRING);
	$symptoms = (function() use ($data) {
		$result = [];
		foreach ($data['discovered_symptoms'] as $symptom) {
			$result[] = new DiscoveredSymptomEntity(['name' => $symptom['tag']]);
		}
		return $result;
	}); 
	$appointment_data['symptoms'] = $symptoms();

	$drugs = (function() use ($data) {
		$result = [];
		foreach ($data['administered_drugs'] as $drug) {
			$result[] = new AdministeredDrugEntity(['name' => $drug['tag']]);
		}
		return $result;
	});

	$appointment_data['drugs'] = $drugs();
 
	$appointment = new AppointmentEntity($appointment_data);
	$appointment_mapper = new AppointmentMapper($this->db);
	$result = $appointment_mapper->save($appointment);
	return $this->view->render($response, $result, 200);
});

$app->get('/appointments', function (Request $request, Response $response, $args) {
	$parameter = $request->getQueryParams();
	$patient_id = $parameter['patient'];
	$appointment_mapper = new AppointmentMapper($this->db);
	$appointments = $appointment_mapper->getAppointmentsByPatientID($patient_id);
	
	if ($appointments) {
		return $this->view->render($response, $appointments, 200);
	}
	
}); 

$app->get('/appointments/{appointment_id}', function (Request $request, Response $response, $args) {
	$appointment_mapper = new AppointmentMapper($this->db);
	$appointment = $appointment_mapper->getAppointment((int)$args['appointment_id']);
	$response->withHeader( 'Content-Type', 'application/json' );
	if (!$appointment) {
			return $this->view->render($response, [
			'error'     => true,
			'msg'       => 'No appointments found'
		], 404);
	}
	return $this->view->render($response, [
		'appointment' => $appointment,
		'error'     => false,
		'msg'       => null
	], 200);
});

$app->delete('/appointments/{appointment_id}', function (Request $request, Response $response, $args) {
	$patient_mapper = new AppointmentMapper($this->db);
	$patient_mapper->delete((int)$args['appointment_id']);
	return $this->view->render($response, [
		'error'     => false,
		'msg'       => 'Deleted Sucessfully'
	], 204);
});
