<?php
namespace Entity;

class AppointmentEntity implements \JsonSerializable
{
    protected $id;
    protected $created_at;
    protected $updated_at;
    protected $next_appointment;
    protected $patient;
    protected $discovered_symptoms;
    protected $administered_drugs;
    protected $note;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }
        if(isset($data['next_appointment']))
            $this->next_appointment = $data['next_appointment'];
        if(isset($data['patient_id']))
            $this->patient = $data['patient_id'];
        if(isset($data['symptoms']))
            $this->discovered_symptoms = $data['symptoms'];
        if(isset($data['drugs']))
            $this->administered_drugs = $data['drugs'];
        if(isset($data['created_at']))
            $this->created_at = $data['created_at'];
        if(isset($data['updated_at']))
            $this->updated_at = $data['updated_at'];
        if(isset($data['note']))
            $this->note = $data['note'];
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        return $this->id = $id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function getNextAppointment() {
        return $this->next_appointment;
    }

    public function getPatient() {
        return $this->patient;
    }

    public function getDiscoveredSymptoms() {
        return $this->discovered_symptoms;
    }

    public function getAdministeredDrugs() {
        return $this->administered_drugs;
    }

    public function setDiscoveredSymptoms($discovered_symptoms) {
        $this->discovered_symptoms = $discovered_symptoms;
    }

    public function setAdministeredDrugs($administered_drugs) {
        $this->administered_drugs  = $administered_drugs;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note  = $note;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'created_at' => date_format(date_create($this->getCreatedAt()), "F j, Y, g:i a"),
            'updated_at' => date_format(date_create($this->getUpdatedAt()), "F j, Y, g:i a"),
            'next_appointment' => date_format(date_create($this->getNextAppointment()), "F j, Y"),
            'note' => $this->getNote(),
            'patient' => $this->getPatient(),
            'discovered_symptoms' => $this->getDiscoveredSymptoms(),
            'administered_drugs' => $this->getAdministeredDrugs(),
        ];
    }
}