<?php
namespace Mapper;
use \Mapper\AMapper;
use \Entity\PatientEntity;
/**
* Patient Mapper
*/
class PatientMapper extends AMapper
{
	
	function getPatients($search = null)
	{
        if ($search != null) {
            $sql = "SELECT p.id, p.name, p.address, p.age
            from patients p WHERE p.name LIKE :name ORDER BY name ASC LIMIT 30";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(["name" => "%{$search}%"]);
        } else {
            $sql = "SELECT p.id, p.name, p.address, p.age
            from patients p ORDER BY name ASC";
            $stmt = $this->db->query($sql);
        }
		$results = array();
		while($row = $stmt->fetch()) {
		    $results[] = new PatientEntity($row);
		}
		return $results;
	}

	 /**
     * Get one patient by its ID
     *
     * @param int $patient_id The ID of the patient
     * @return patientEntity  The patient
     */
    public function getPatientById($patient_id) 
    {
        $sql = "SELECT p.id, p.name, p.address, p.age
            from patients p
            where p.id = :patient_id";;
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["patient_id" => $patient_id]);
        $result = $stmt->fetch();
        if($result) {
            return new PatientEntity($result);
        } else {
            return null;
        }
    }

    public function update(PatientEntity $patient) {
        $sql = "UPDATE patients p SET name = :name, address = :address, age = :age WHERE id = :patient_id";
        $stmt = $this->db->prepare($sql); 
        $result = $stmt->execute([
            "patient_id" => $patient->getId(),
            "name" => $patient->getName(),
            "address" => $patient->getAddress(),
            "age" => $patient->getAge(),
        ]);
        if(!$result) {
            throw new Exception("Could not update Patient profile");
        }

    }

	public function save(PatientEntity $patient) 
	{
        $sql = "INSERT into patients
            (name, address, age) values
            (:name, :address, :age)";
        $stmt = $this->db->prepare($sql);
        $data = [
            "name" => $patient->getName(),
            "address" => $patient->getAddress(),
            "age" => $patient->getAge(),
        ];
        $result = $stmt->execute($data);
        if(!$result) {
            throw new Exception("Could not save Patient profile");
        } else {
            $data['id'] = (int)$this->db->lastInsertId();
            return new PatientEntity($data);
        }
    }

    public function delete($patient_id) {
        $sql = "DELETE FROM patients WHERE id = :patient_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':patient_id', $patient_id, \PDO::PARAM_INT);
        $result = $stmt->execute();
        if(!$result) {
            throw new Exception("Could not delete Patient profile");
        }
    }
}