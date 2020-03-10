<?php

class Validation {

    public function validate($connection, $serialNumber) {
        $getSerialNumber = $this->getSerialNumber($connection, $serialNumber);
        if ($getSerialNumber) {
            return true;
        } else {
            return false;
        }
    }

    public function getSerialNumber($connection, $serialNumber) {
        $stmt = $connection->prepare("
            SELECT serial_number
            FROM products
            WHERE serial_number = :serialNumber
        ");
        $stmt->execute(array(
            ":serialNumber" => $serialNumber
        ));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>