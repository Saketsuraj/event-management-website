<?php
    include "connection.php";
    require_once 'recaptcha-master/src/autoload.php';
    

    $name = $_POST['name'];
    $email = $_POST['email'];
    $country = $_POST['country'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $query = "insert into contact(name, email, country, phone, message) values ('$name','$email', '$country', '$phone', '$message')";
    $runquery = mysqli_query($conn, $query);

    $mail_html = '
        <html>
            <head>
                <title></title>
            </head>
            <body>
                <p>Name:- '.$name.'</p>
                <p>Email:- '.$email.'</p>
                <p>Country:- '.$country.'</p>
                <p>Phone:- '.$phone.'</p>
                <p>Message:- '.$message.'</p>
            </body>
        </html>

    ';

    $secretKey = "6LcrL6kUAAAAABpdI_vx3tmmc07VuKOdgYqhTQFY";
    $responseKey = $_POST['g-recaptcha-response'];

    $recaptcha = new \ReCaptcha\ReCaptcha($secretKey);
    $userIP = $_POST['REMOTE_ADDR'];
    $captchaResp = $recaptcha->setExpectedHostname('localhost')
                  ->verify($responseKey, $userIP);

    if ($captchaResp->isSuccess()) {
        if ($runquery) {
            $arr = array('status' => true, 'message' => 'Thanks for reaching out to Lightning Speed Events Services. One of our team will get back to you shortly. Please check your email regularly for a response. Looking forward to servicing you very soon');
            echo json_encode($arr);
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "From: noreply@ranchiblog.in\r\n";
            $mail = mail("saket.suraj57@gmail.com","Contact - ".$name,$mail_html, $headers);
            
        } else {
            $arr = array('status' => false, 'message' => 'Error occured while subscribing');
            echo json_encode($arr);
        }
    } else {
        $errors = $captchaResp->getErrorCodes();
        $arr = array('status' => false, 'message' => 'Invalid captcha, try again!!');
        echo json_encode($arr);
    }
?>