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

    public function insertUser($connection, $formData) {
        $firstName = $formData->datas->fname;
        $lastName = $formData->datas->lname;
        $email = $formData->datas->email;
        $phoneNo = $formData->datas->mobileno;
        try {
            $stmt = $connection->prepare("
                INSERT INTO users (first_name, last_name, email, phone)
                VALUES (:firstName, :lastName, :email, :phone)
            ");
            $stmt->execute(array(
                "firstName" => $firstName,
                "lastName" => $lastName,
                ":email" => $email,
                ":phone" => $phoneNo
            ));
        } catch( PDOExecption $e ) {
            print "Error!: " . $e->getMessage() . "</br>";
            $message = new stdClass();
            $message->code = 404;
            $message->msg = "Hubo un problema al subir la información. Por favor intente mas tarde";
        }
        return $userId = $connection->lastInsertId();
    }
}

?>