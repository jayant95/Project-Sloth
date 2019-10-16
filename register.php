<?php
session_start();
require_once("connection/db_connection.php");
require_once("helper/helper_functions.php");

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$user = [];

if ($_POST){
    $user['first-name'] = !empty($_POST['firstName']) ? $_POST['firstName'] : "";
    $user['last-name'] = !empty($_POST['lastName']) ? $_POST['lastName'] : "";
    $user['email'] = !empty($_POST['email']) ? $_POST['email'] : "";
    $user['username'] = !empty($_POST['username']) ? $_POST['username'] : "";
    $user['password'] = !empty($_POST['password']) ? $_POST['password'] : "";

    $isRegisteredEmail = isExistingUser($user['email'], $connection, "email");
    $isRegisteredUsername = isExistingUser($user['username'], $connection, "username");

    if (!$isRegisteredEmail && !$isRegisteredUsername) {
        $error = registerUser($user, $connection);

        if (empty($error)) {
            $data = [];
            $data['email'] = $user['email'];
            $data['first-name'] = $user['first-name'];
           // sendConfirmationEmail($data);
            echo json_encode(
                [
                    "registered" => true,
                    "message" => "Registration was successful! Confirmation email will be sent shortly."
                ]
                );

        } else {
            echo json_encode(
                [
                    "registered" => false,
                    "message" => $error
                ]
                ); 
        }

    } else {
        echo json_encode(
            [
                "registered" => false,
                "emailError" => $isRegisteredEmail,
                "usernameError" => $isRegisteredUsername
            ]
            );
    }

} else {
 // tell the user about error
//  echo json_encode(
//      [
//         "sent" => false,
//         "message" => $SendMailFailederrorMessage
//      ]
//  );
}