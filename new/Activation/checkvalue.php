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

    if ($isValid) {
        $message->code = 200;
        $message->msg = "Existe";
        echo json_encode($message);
    } else {
        $message->code = 404;
        $message->msg = "Número de SIM no válido";
        echo json_encode($message);
    }

} elseif (isset($_POST['json'])) {
    $form_data = json_decode($_POST['json']);
    $userId = $validation->insertUser($connection, $form_data);
    $productIdArray = $validation->getProductId($connection, $form_data->datas->serialNo);
    $productId = $productIdArray['id'];
    $validation->insertActivation($connection, $userId, $productId, $form_data->datas->date);    
}

?>