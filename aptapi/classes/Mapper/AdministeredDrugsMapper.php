<?php
namespace Mapper;
use \Mapper\AMapper;
use \Entity\AdministeredDrugEntity;
/**
* Appointent Mapper
*/
class AdministeredDrugsMapper extends AMapper
{
	public function getDrugs($appointment_id) {
		$sql = "SELECT * FROM administered_drugs WHERE appointment_id = :appointment_id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(["appointment_id" => $appointment_id]);
        $results = array();
		while($row = $stmt->fetch()) {
            $results[] = new AdministeredDrugEntity($row);
        }
        if (count($results) > 0) {
            return $results;
        } else {
            return null;
        }
	}
}