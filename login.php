<?php
    require_once("connection/db_connection.php");
    require "php-jwt/vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    $user = [];
    $configs = include('../includes/config.php');

    if ($_POST) {
        $user['email'] = !empty($_POST['email']) ? $_POST['email'] : "";
        $user['password'] = !empty($_POST['password']) ? $_POST['password'] : "";

        $error = "";
        $message = "";
        $loggedIn = false;
        $name = "";
        $username = "";

        if ($stmt = $connection->prepare('SELECT * FROM members WHERE email = ? LIMIT 1')) {
            $stmt->bind_param('s', $user['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['email'] == $user['email']) {
                    $id = $row['memberID'];
                    $firstName = $row['firstName'];
                    $lastName = $row['lastName'];
                    $email = $row['email'];
                    $username = $row['username'];

                    if (password_verify($user['password'], $row['hashed_password'])) {
                        $secret_key = $configs['secret-key'];
                        $issuer_claim = "SERVER_NAME";
                        $audience_claim = "THE_AUDIENCE";
                        $issuedAt_claim = time();
                        $notBefore_claim = $issuedAt_claim + 10;
                        $expire_claim = $issuedAt_claim + 60 * 60;
                        $token = array(
                            "iss" => $issuer_claim,
                            "aud" => $audience_claim,
                            "iat" => $issuedAt_claim,
                            "nbf" => $notBefore_claim,
                            "exp" => $expire_claim,
                            "data" => array(
                                "id" => $id,
                                "firstname" => $firstName,
                                "lastname" => $lastName,
                                "email" => $email
                            ));
                        
                        http_response_code(200);

                        $jwt = JWT::encode($token, $secret_key);
                        echo json_encode(
                            array(
                                "message" => "Successful Login",
                                "jwt" => $jwt,
                                "email" => $email,
                                "expireAt" => $expire_claim,
                                "username" => $username
                            ));
                        
                    } else {
                        http_response_code(401);
                        echo json_encode(
                            array(
                                "message" => "Login Failed"
                            ));
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(
                        array(
                            "message" => "Login Failed"
                        ));
                }
            }
        } else {
            //$error = $connection->errno . ' ' . $connection->error;
        }

        $stmt->close();
        $connection->close();
    }

?>
