<?php

require 'vendor/autoload.php';
require 'lib.php';
require 'Connection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$message = new stdClass();
$connect = new Connection();
$validation = new Validation();

$connection = $connect->connect(getenv('DBSERVERNAME'), getenv('DBNAME'), getenv('DBUSERNAME'), getenv('DBPASSWORD'));

if (isset($_POST['serial_no'])) {
    $isValid = $validation->validate($connection, $_POST['serial_no']);

    switch ($isValid) {
        case 1:
            $needsPassport = $validation->needsPassport($connection, $_POST['serial_no']);
            if ($needsPassport) {
                $message->code = 200;
                $message->msg = "";
                $message->passport = 2;
            } else {
                $message->code = 200;
                $message->msg = "";
                $message->passport = 1;
            }
            echo json_encode($message);
            break;

        case 2:
            $message->code = 403;
            $message->msg = "SIM en proceso de activación";
            echo json_encode($message);
            break;
        
        default:
            $message->code = 404;
            $message->msg = "Número de SIM no válido";
            echo json_encode($message);
            break;
    }

} elseif (isset($_POST['json'])) {
    $form_data = json_decode($_POST['json']);
    $userId = $validation->insertUser($connection, $form_data);
    $productIdArray = $validation->getProductId($connection, $form_data->datas->serialNo);
    $productId = $productIdArray['id'];
    $validation->insertActivation($connection, $userId, $productId, $form_data->datas->date);    
}

?>