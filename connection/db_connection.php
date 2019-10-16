<?php
  // Create the variables for accessing the database
  DEFINE("DB_USER", "root");
  DEFINE("DB_PASS", "");
  DEFINE("DB_NAME", "piano");
  DEFINE("DB_SERVER", "localhost");

  // Create the connection
  $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

  // If there's an error display the error
  if (mysqli_connect_errno()) {
    die("Database connection failed: " .
    mysqli_connect_error() .
    " (" . mysqli_connect_errno(). ")");
  }
?>
