<?php

require 'vendor/autoload.php';
require 'lib.php';
require 'Connection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$message = new stdClass();

session_start();

$connect = new Connection();

$connection = $connect->connect(getenv('DBSERVERNAME'), getenv('DBNAME'), getenv('DBUSERNAME'), getenv('DBPASSWORD'));

if (isset($_POST['serial_no'])) {
    $validation = new Validation();

    $isValid = $validation->validate($connection, $_POST['serial_no']);

    if ($isValid) {
        $message->code = 200;
        $message->msg = "Existe";
        echo json_encode($message);
        $_SESSION['serial_no'] = $_POST['serial_no'];
    } else {
        $message->code = 404;
        $message->msg = "Número de SIM no válido";
        echo json_encode($message);
        unset($_SESSION['serial_no']);
    }
} elseif (isset($_POST['json'])) {
    $form_data = json_decode($_POST['json']);
    $message->code = 200;
    $message->msg = "Good job";
    echo json_encode($message);
}

?>