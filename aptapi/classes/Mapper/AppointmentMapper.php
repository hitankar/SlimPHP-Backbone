<?php
namespace Mapper;
use \Mapper\AMapper;
use \Entity\AppointmentEntity;
use \Entity\DiscoveredSymptomEntity;
use \Mapper\DiscoveredSymptomsMapper;
use \Entity\AdministeredDrugEntity;
use \Mapper\AdministeredDrugsMapper;
/**
* Appointent Mapper
*/
class AppointmentMapper extends AMapper
{

	public function save(AppointmentEntity $appointment) 
	{
        $this->db->beginTransaction();
        $sql = "INSERT into appointments
            (next_appointment, note, patient_id) values
            (:next_appointment, :note, :patient_id)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "next_appointment" => $appointment->getNextAppointment(),
            "note" => $appointment->getNote(),
            "patient_id" => $appointment->getPatient(),
        ]);
        $appointment_id = (int)$this->db->lastInsertId();
        $appointment->setId($appointment_id);
        $symptoms = $appointment->getDiscoveredSymptoms();
        if (count($symptoms) > 0 ){
            foreach ($symptoms as $symptom) {
                $symptom->setAppointmentID($appointment_id);
                $sql = "INSERT into discovered_symptoms
                    (name, appointment_id) values
                    (:name, :appointment_id)";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    "name" => $symptom->getName(),
                    "appointment_id" => $symptom->getAppointment(),
                ]);
            }
        }
        $drugs = $appointment->getAdministeredDrugs();
        if (count($drugs) > 0 ){
            foreach ($drugs as $drug) {
                $drug->setAppointmentID($appointment_id);
                $sql = "INSERT into administered_drugs
                    (name, appointment_id) values
                    (:name, :appointment_id)";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    "name" => $drug->getName(),
                    "appointment_id" => $drug->getAppointment(),
                ]);
            }
        }
        $this->db->commit();
        if(!$result) {
            throw new Exception("Could not save Appointment");
        } else {
            return $appointment;
        }
    }

    public function getAppointmentsByPatientId($patient_id) 
    {
        $sql = "SELECT *
            from appointments a
            where a.patient_id = :patient_id LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["patient_id" => $patient_id]);

        $results = [];
        while($row = $stmt->fetch()) {
            $symptomsMapper = new DiscoveredSymptomsMapper($this->db);
            $drugsMapper = new AdministeredDrugsMapper($this->db);
            $appointment = new AppointmentEntity($row);
            $appointment->setDiscoveredSymptoms($symptomsMapper->getSymptoms($appointment->getId()));
            $appointment->setAdministeredDrugs($drugsMapper->getDrugs($appointment->getId()));
            $results[] = $appointment;
        }
        if (count($results) > 0) {
            return $results;
        } else {
            return null;
        }
    }

    public function getAppointment($appointment_id) {
        $sql = "SELECT *
            from appointments a
            where a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["id" => $appointment_id]);
        $appointment = new AppointmentEntity($stmt->fetch());

        if (!$appointment) {
            return null;
        }

        $sql = "SELECT *
            from discovered_symptoms s
            where s.appointment_id = :appointment_id
            order by s.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["appointment_id" => $appointment_id]);
        $symptoms = [];
        while($row = $stmt->fetch()) {
            $symptoms[] = new DiscoveredSymptomEntity($row);
        }
        $appointment->setDiscoveredSymptoms($symptoms);

        $sql = "SELECT *
            from administered_drugs d
            where d.appointment_id = :appointment_id
            order by d.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["appointment_id" => $appointment_id]);
        $drugs = [];
        while($row = $stmt->fetch()) {
            $drugs[] = new AdministeredDrugEntity($row);
        }
        $appointment->setAdministeredDrugs($drugs);
        
        return $appointment;
    }

    public function delete($appointment_id) {
        $sql = "DELETE FROM discovered_symptoms WHERE appointment_id = :appointment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':appointment_id', $appointment_id, \PDO::PARAM_INT);
        $result = $stmt->execute();

        $sql = "DELETE FROM administered_drugs WHERE appointment_id = :appointment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':appointment_id', $appointment_id, \PDO::PARAM_INT);
        $result = $stmt->execute();

        $sql = "DELETE FROM appointments WHERE id = :appointment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':appointment_id', $appointment_id, \PDO::PARAM_INT);
        $result = $stmt->execute();

        if(!$result) {
            throw new Exception("Could not delete Appointment");
        }
    }
}