<?php
namespace Entity;

class AdministeredDrugEntity implements \JsonSerializable
{
    protected $id;
    protected $name;
    protected $created_at;
    protected $updated_at;
    protected $appointment;

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
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        if (isset($data['appointment'])) {
            $this->appointment = $data['appointment'];
        }
        if (isset($data['updated_at'])) {
            $this->updated_at = $data['updated_at'];
        }
        if (isset($data['created_at'])) {
            $this->created_at = $data['created_at'];
        }
    }

    public function setId($id) {
        return $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function setAppointmentId($appointment_id) {
        return $this->appointment = $appointment_id;
    }

    public function getAppointment() {
        return $this->appointment;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getID(),
            'name' => $this->getName(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}