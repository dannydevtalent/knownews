<?php
    require("sendgrid-php-master/sendgrid-php.php");
    // If you are not using Composer (recommended)
    // require("path/to/sendgrid-php/sendgrid-php.php");

$email = new \SendGrid\Mail\Mail();
$email->setFrom("test@example.com", "Example User");
$email->setSubject("Sending with Twilio SendGrid is Fun");
$email->addTo("test@example.com", "Example User");
$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);
$sendgrid = new \SendGrid(getenv('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'));
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}
?>