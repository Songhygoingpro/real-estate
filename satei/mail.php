<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require '../config/db_config.php';

session_start();

// Collect and sanitize form data 
$物件の種別 = htmlspecialchars($_SESSION['物件の種別'], ENT_QUOTES, 'UTF-8');
$物件の所在地 = htmlspecialchars($_SESSION['prefecture'], ENT_QUOTES, 'UTF-8') . htmlspecialchars($_SESSION['city'], ENT_QUOTES, 'UTF-8') . htmlspecialchars($_SESSION['town'], ENT_QUOTES, 'UTF-8');
$丁目 = htmlspecialchars(trim($_POST['丁目']), ENT_QUOTES, 'UTF-8');
$マンション名 = htmlspecialchars(trim($_POST['マンション名']), ENT_QUOTES, 'UTF-8');
$号室 = htmlspecialchars(trim($_POST['号室']), ENT_QUOTES, 'UTF-8');
$間取り = htmlspecialchars($_POST['間取り'], ENT_QUOTES, 'UTF-8');
$専有面積 = htmlspecialchars($_POST['専有面積'], ENT_QUOTES, 'UTF-8');
$築年 = htmlspecialchars($_POST['築年'], ENT_QUOTES, 'UTF-8');
$現状 = htmlspecialchars($_POST['現状'], ENT_QUOTES, 'UTF-8');
$あなたと売却物件との関係 = htmlspecialchars($_POST['あなたと売却物件との関係'], ENT_QUOTES, 'UTF-8');
$住宅ローン残高 = htmlspecialchars(trim($_POST['住宅ローン残高']), ENT_QUOTES, 'UTF-8');
$希望買取金額 = htmlspecialchars(trim($_POST['希望買取金額']), ENT_QUOTES, 'UTF-8');
$お名前 = htmlspecialchars(trim($_POST['お名前']), ENT_QUOTES, 'UTF-8');
$フリガナ = htmlspecialchars(trim($_POST['フリガナ']), ENT_QUOTES, 'UTF-8');
$性別 = htmlspecialchars($_POST['性別'], ENT_QUOTES, 'UTF-8');
$電話番号 = htmlspecialchars(trim($_POST['電話番号']), ENT_QUOTES, 'UTF-8');
$ご希望の連絡時間帯 = htmlspecialchars($_POST['ご希望の連絡時間帯'], ENT_QUOTES, 'UTF-8');
$メールアドレス = htmlspecialchars(trim($_POST['メールアドレス']), ENT_QUOTES, 'UTF-8');
$希望する連絡方法1 = isset($_POST['希望する連絡方法1']) ? htmlspecialchars($_POST['希望する連絡方法1'], ENT_QUOTES, 'UTF-8') : '';
$希望する連絡方法2 = isset($_POST['希望する連絡方法2']) ? htmlspecialchars($_POST['希望する連絡方法2'], ENT_QUOTES, 'UTF-8') : '';
$希望査定方法1 = isset($_POST['希望査定方法1']) ? htmlspecialchars($_POST['希望査定方法1'], ENT_QUOTES, 'UTF-8') : '';
$希望査定方法2 = isset($_POST['希望査定方法2']) ? htmlspecialchars($_POST['希望査定方法2'], ENT_QUOTES, 'UTF-8') : '';

//Add a '/' between options if both are chosen.
$希望する連絡方法 = $希望する連絡方法1;
if (empty($希望する連絡方法2) || empty($希望する連絡方法)) {
    $希望する連絡方法 .=   $希望する連絡方法2;
} else {
    $希望する連絡方法 .= ' / ' . $希望する連絡方法2;
}

$希望査定方法 = $希望査定方法1;
if (empty($希望査定方法2) || empty($希望査定方法)) {
    $希望査定方法 .= $希望査定方法2;
} else{
    $希望査定方法 .= ' / ' . $希望査定方法2;
}

// Prepare the email content
if (isset($_POST["send"])) {
    $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'exampleSender@gmail.com'; // SMTP username
    $mail->Password = 'examplePassword'; // SMTP password
    $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465; // TCP port to connect to

    //Recipients
    $mail->setFrom($_POST["メールアドレス"], $_POST["お名前"]); // Sender Email and name
    $mail->addAddress('example@gmail.com');     //Add a recipient email  
    $mail->addReplyTo($_POST["メールアドレス"], $_POST["お名前"]); // reply to sender email

    //Content
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'お問い合わせ｜売却査定';
    $mail->Body    = "
            <html>
            <head>
                <style>
                
                    .property-info {
                        margin-bottom: 10px;
                        color: black;
                    }
                    .property-info strong {
                        display: inline-block;
                        width: 200px;
                    }

                </style>
            </head>
            <body>
                <div class='property-info'>
                    <strong>物件の種別:</strong> $物件の種別<br>
                </div>
                <div class='property-info'>
                    <strong>物件の所在地:</strong> $物件の所在地  $丁目$マンション名$号室<br>
                </div>
                <div class='property-info'>
                    <strong>間取り:</strong> $間取り<br>
                </div>
                <div class='property-info'>
                    <strong>専有面積 :</strong> $専有面積 <br>
                </div>
                <div class='property-info'>
                    <strong>築年:</strong> $築年<br>
                </div>
                <div class='property-info'>
                    <strong>現状:</strong> $現状<br>
                </div>
                <div class='property-info'>
                    <strong>あなたと売却物件との関係:</strong> $あなたと売却物件との関係<br>
                </div>
                <div class='property-info'>
                    <strong>住宅ローン残高:</strong>約<span>$住宅ローン残高</span>万円<br>
                </div>
                <div class='property-info'>
                    <strong>希望買取金額:</strong>約<span>$希望買取金額</span>万円<br>
                </div>
                <div class='property-info'>
                    <strong>お名前:</strong> $お名前<br>
                </div>
                <div class='property-info'>
                    <strong>フリガナ:</strong> $フリガナ<br>
                </div>
                <div class='property-info'>
                    <strong>性別:</strong> $性別<br>
                </div>
                <div class='property-info'>
                    <strong>電話番号:</strong> $電話番号<br>
                </div>
                 <div class='property-info'>
                    <strong>ご希望の連絡時間帯:</strong> $ご希望の連絡時間帯<br>
                </div>
                <div class='property-info'>
                    <strong>メールアドレス:</strong> $メールアドレス<br>
                </div>
                <div class='property-info'>
                    <strong>希望する連絡方法:</strong> $希望する連絡方法<br>
                </div>
                <div class='property-info'>
                    <strong>希望査定方法:</strong> $希望査定方法<br>
                </div>
            </body>
            </html>
        ";


    // Send email
    //  if ($mail->send()) {
    //     // Insert data into the database
    //     $stmt = $conn->prepare("INSERT INTO inquiries (property_type, address, name, gender, phone_number, email_address) VALUES (?, ?, ?, ?, ?, ?)");
    //     $stmt->bind_param("ssssss", $物件の種別, $物件の所在地, $お名前, $性別, $電話番号, $メールアドレス);

    //     if ($stmt->execute()) {
    //         header("Location: success.html");
    //     } else {
    //         echo "Error: " . $stmt->error;
    //     }
    //     $stmt->close();
    // } else {
    //     echo "Failed to send email.";
    // }

    // $conn->close(); 
    
}
