<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
if (isset($_POST['txtEmailSignIn']) && isset($_POST['txtPasswordSignIn'])) {
    $userEmail = $_POST['txtEmailSignIn'];
    $userPassword = $_POST['txtPasswordSignIn'];
    $sha1Password = sha1($userPassword);

    try {
        $db = new mysqli("localhost", "root", '', "alandalus");
        $qrystr = "SELECT * FROM accounts WHERE email = '$userEmail' AND password = '$sha1Password'";
        $res = $db->query($qrystr);

        if ($res && $res->num_rows > 0) {
            header("Location: ../Home Page/index.php");

        } else {
            echo "Invalid email or password.";
        }

        $db->close();
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>

</body>
</html>