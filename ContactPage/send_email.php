
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // إرسال الإيميل لمالك الموقع
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'talaalhendiuni4@gmail.com';
        $mail->Password = 'xszs cskg uywo aixj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('talaalhendiuni4@gmail.com', 'Send Message Alandalus Design');
        $mail->addAddress('talaalhendiuni4@gmail.com'); // مالك الموقع

        $mail->isHTML(false);
        $mail->Subject = !empty($subject) ? $subject : 'New Contact Message';
        $mail->Body = "Name: $name\nEmail: $email\nMessage:\n$message";

        $mail->send();

        // ⬇️ إنشاء كائن جديد للرد على المستخدم
        $reply = new PHPMailer(true);
        $reply->isSMTP();
        $reply->Host = 'smtp.gmail.com';
        $reply->SMTPAuth = true;
        $reply->Username = 'talaalhendiuni4@gmail.com';
        $reply->Password = 'xszs cskg uywo aixj';
        $reply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $reply->Port = 587;
        $reply->CharSet = 'UTF-8';

        $reply->setFrom('talaalhendiuni4@gmail.com', 'Send Message Alandalus Design');
        $reply->addAddress($email, $name); // المستخدم
        $reply->isHTML(true);
        $reply->Subject = "تم استلام رسالتك من Alandalus Design";
        $reply->Body = "مرحبًا $name,\n\nشكرًا لتواصلك معنا. لقد استلمنا رسالتك وسنقوم بالرد عليك في أقرب وقت ممكن.\n\n" .
            "معلومات رسالتك:\n\n" .
            "الاسم: $name\n" .
            "البريد الإلكتروني: $email\n" .
            "الموضوع: $subject\n" .
            "الرسالة:\n$message\n\n" .
            "تحياتنا،\nفريق Alandalus Design";

        $reply->send();

        echo "<script>alert('تم إرسال الرسالة بنجاح وسيتم الرد عليك قريباً!'); window.location.href='contact.php';</script>";

    } catch (Exception $e) {
    echo "خطأ في الإرسال: " . $mail->ErrorInfo;
    exit;
}


} else {
    header("Location: contact.php");
    exit();
}
?>
