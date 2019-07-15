<?php
namespace Entity;

class PatientEntity implements \JsonSerializable
{
    protected $id;
    protected $name;
    protected $address;
    protected $age;

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

        $this->name = $data['name'];
        $this->address = $data['address'];
        $this->age = $data['age'];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getAge() {
        return $this->age;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getID(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'age' => $this->getAge(),
        ];
    }
}