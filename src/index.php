<?php
require __DIR__ . '/../vendor/autoload.php';
require_once '../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$bericht = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $naam = htmlspecialchars($_POST['naam'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $klacht = htmlspecialchars($_POST['klacht'] ?? '');

    if (!empty($naam) && !empty($email) && !empty($klacht)) {
        $mail = new PHPMailer(true);

        try {
        
            $mail->isSMTP();
            $mail->Host       = $smtphost ;   // <-- aanpassen
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpuser; // <-- aanpassen
            $mail->Password   = $smtppass;      // <-- aanpassen
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Afzender & ontvanger
            $mail->setFrom('zakaria@zakariao.nl', 'Klachtverwerking');
            $mail->addAddress($email, $naam);  // stuur naar gebruiker
            $mail->addCC('z.aourzag@gmail.com'); // cc naar jezelf

            // Inhoud
            $mail->isHTML(true);
            $mail->Subject = 'Uw klacht is in behandeling';
            $mail->Body    = "
                <h2>Uw klacht is ontvangen</h2>
                <p><strong>Naam:</strong> {$naam}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Klacht:</strong><br>" . nl2br($klacht) . "</p>
            ";

            $mail->send();
            $bericht = "Uw klacht is succesvol verzonden!";
        } catch (Exception $e) {
            $bericht = "Er is iets misgegaan bij het verzenden: {$mail->ErrorInfo}";
        }
    } else {
        $bericht = "Vul alle velden in.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Klachtenformulier</title>
</head>
<body>
    <h1>Klachtenformulier</h1>
    <?php if ($bericht): ?>
        <p><strong><?= $bericht ?></strong></p>
    <?php endif; ?>

    <form method="post">
        <label>Naam:<br>
            <input type="text" name="naam" required>
        </label><br><br>

        <label>Email:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Omschrijving klacht:<br>
            <textarea name="klacht" rows="5" cols="40" required></textarea>
        </label><br><br>

        <button type="submit">Verstuur klacht</button>
    </form>
</body>
</html>
