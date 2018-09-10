<?php
namespace Mapper;

abstract class AMapper {
    protected $db;
    public function __construct($db) {
        $this->db = $db;
    }
}
