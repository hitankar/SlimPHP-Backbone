<?php
namespace Mapper;
use \Mapper\AMapper;
use \Entity\DiscoveredSymptomEntity;
/**
* Appointent Mapper
*/
class DiscoveredSymptomsMapper extends AMapper
{
	public function getSymptoms($appointment_id) {
		$sql = "SELECT * FROM discovered_symptoms WHERE appointment_id = :appointment_id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(["appointment_id" => $appointment_id]);
        $results = array();
		while($row = $stmt->fetch()) {
            $results[] = new DiscoveredSymptomEntity($row);
        }
        if (count($results) > 0) {
            return $results;
        } else {
            return null;
        }
	}
}