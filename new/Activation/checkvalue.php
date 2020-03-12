<?php

require 'vendor/autoload.php';
require 'lib.php';
require 'Connection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$message = new stdClass();

$connect = new Connection();

$connection = $connect->connect(getenv('DBSERVERNAME'), getenv('DBNAME'), getenv('DBUSERNAME'), getenv('DBPASSWORD'));

$validation = new Validation();

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

?>