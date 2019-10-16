<?php
use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

function isExistingUser($data, $connection, $column) {
    $error = "";

    $stmt = $connection->prepare("SELECT * FROM members WHERE " . $column. " = ?");
    $stmt->bind_param('s', $data);

    $stmt->execute();

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      if ($row[$column] == $data) {
        $error = "The " .$column. " you entered already exists.";
        break;
      }
    }

    $stmt->close();

    return $error;
}

function registerUser($data, $connection) {
    $error = "";  
    $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);

    if ($stmt = $connection->prepare('INSERT INTO members (firstName, lastName, email, username, hashed_password) VALUES (?, ?, ?, ?, ?)')) {

        $stmt->bind_param('sssss', $data['first-name'], $data['last-name'], $data['email'], $data['username'], $hashed_password);
        $result = $stmt->get_result();
        $stmt->execute();
    } else {
        $error = $connection->errno . ' ' . $connection->error;
    }
    $stmt->close();

    return $error;
}

function sendConfirmationEmail($data) {
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->Mailer = 'smtp';
  $mail->SMTPAuth = true;
  // $mail->SMTPDebug = 2;
  $mail->Host = 'smtp.gmail.com'; 
  $mail->Port = 587;
  $mail->SMTPSecure = 'tls';
  $mail->SMTPOptions = array(
      'ssl' => array(
      'verify_peer' => false,
      'verify_peer_name' => false,
      'allow_self_signed' => true
      )
      );

      // need to put this information safer once online
  $mail->Username = "jayanttailor95@gmail.com";
  $mail->Password = "clonewars95";

  $mail->IsHTML(true); // if you are going to send HTML formatted emails
  $mail->SingleTo = true; // if you want to send a same email to multiple users. multiple emails will be sent one-by-one.

  $mail->From = "jht1995@hotmail.com";
  $mail->FromName = "Jayant";

  $mail->addAddress($data['email']);
  $mail->Subject = "Piano Circle";
  $mail->Body = 
  "Hi " . $data['first-name'] . ",<br/><br/>
  Thanks for signing up with Piano Circle! You can now discuss in our forum with other pianists free of charge!<br>
  Our additonal plans allow you to receive new piano sheet music every month from aspiring composers! Our plans include:<br><br>
  Plan 1 - $0/month: Discuss tips and tricks with other pianists. You just signed up for this!<br>
  Plan 2 - $12/month: 5 pieces of sheet music + discuss with others learning the same music!<br>
  Plan 3 - $30 for 3 months: Same as Plan 2 at a better price!<br><br>

  Thank you for choosing to sign up with us!<br>
  Piano Circle";

  if(!$mail->Send()) {
      $error = 'Mail error: '.$mail->ErrorInfo;
      return false;
  } else {
      $error = 'Message sent!';
      return true;
  }
}


?>