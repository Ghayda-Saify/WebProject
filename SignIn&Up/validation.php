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
//phpinfo();



if (isset($_POST['txtEmailSignIn']) && isset($_POST['txtPasswordSignIn'])) {
    $userEmail = $_POST['txtEmailSignIn'];
    $userPassword = $_POST['txtPasswordSignIn'];
    $sha1Password = sha1($userPassword);

    try {
        $db = new mysqli("127.0.0.1", "root", '', "alandalus");
//        $qrystr = "SELECT * FROM 'Users' WHERE 'Email' = '$userEmail' AND 'Password' = '$sha1Password'";
        $stmt = $db->prepare("SELECT * FROM Users WHERE Email = ? AND Password = ?");
        $stmt->bind_param("ss", $userEmail, $sha1Password);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            header("Location: ../index.html");
        } else {
            echo "Invalid email or password.";
        }


        $db->close();
    } catch (Exception $e) {
        echo "An error occurred: ",$e->getMessage(), "<br>";
    }
}
?>

</body>
</html>