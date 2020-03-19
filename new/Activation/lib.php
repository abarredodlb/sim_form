<?php

class Validation {

    public function validate($connection, $serialNumber) {
        $getProductId = $this->getProductId($connection, $serialNumber);
        if ($getProductId) {
            $isActive = $this->getActivationId($connection, $getProductId['id']);
            if ($isActive) {
                return 2;
            } else {
                return 1;
            }
        } else {
            return 3;
        }
    }

    public function getProductId($connection, $serialNo) {
        $stmt = $connection->prepare("
            SELECT id
            FROM products
            WHERE serial_number = :serialNumber
        ");
        $stmt->execute(array(
            ":serialNumber" => $serialNo
        ));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getActivationId($connection, $productId) {
        $stmt = $connection->prepare("
            SELECT id
            FROM activations
            WHERE product_id = :productId
        ");
        $stmt->execute(array(
            ":productId" => $productId
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
                ":firstName" => $firstName,
                ":lastName" => $lastName,
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

    public function insertActivation($connection, $userId, $productId, $date) {
        $message = new stdClass();
        $activationDate = date("Y-m-d", strtotime("03/19/2020"));
        try {
            $stmt = $connection->prepare("
                INSERT INTO activations (product_id, user_id, activation_date)
                VALUES (:productId, :userId, :activationDate)
            ");
            $stmt->execute(array(
                ":productId" => $productId,
                ":userId" => $userId,
                ":activationDate" => $activationDate
            ));
            $message->code = 200;
            $message->msg = "Los datos han sido enviados correctamente! :)";
            echo json_encode($message);
        } catch( PDOExecption $e ) {
            print "Error!: " . $e->getMessage() . "</br>";
            $message->code = 404;
            $message->msg = "Hubo un problema al subir la información. Por favor intente mas tarde :(";
        }
    }
}

?>