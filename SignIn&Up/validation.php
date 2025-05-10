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

    try {
        $db = new mysqli("localhost", "root", '', "alandalus");
        $qrystr = "select * from accounts";
        $res=$db->query($qrystr);
        for($i=0;$i<$res->num_rows;$i++){
            $row = $res->fetch_assoc();
            echo "<br>".$row['Name'];
        }
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>
</body>
</html>