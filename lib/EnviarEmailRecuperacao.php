<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function enviarEmailRecuperacao($emailDestino, $token, $nomeUsuario){
    include 'config.php';
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 1;
        $mail->CharSet = "UTF-8"; 
        $mail->isSMTP();
        $mail->Host     = 'smtp.gmail.com';
        $mail->Port     = 587;
        $mail->Username = 'filmix.oficial@gmail.com';
        $mail->Password = $SENHA_SMTP; // 16 dígitos do Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth =  true;

        $mail->setFrom ('filmix.oficial@gmail.com', 'Filmix Oficial');
        $mail->addAddress($emailDestino, $nomeUsuario);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperação de Senha - FILMIX';

        $link = "http://localhost/filmix/NovaSenha.php?token=" . $token;

        $mail->Body = "
            <div style='font-family: Arial; padding: 20 px;'>
                <h2>Olá, {$nomeUsuario} </h2>
                <p>Você solicitou a troca de senha no <b>FILMIX</b></p>            
                <p>Clique no botão abaixo para criar uma nova senha</p>
                <a href='{$link}' style='background: #e50914; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; display: inline-block;'>REDEFINIR SENHA</a>
                <br><br>
                <p>Este link expira em 30 minutos.</p>
                <p>Se não foi você quem solicitou, ignore este e-mail</p>
            </dvi>  
            ";
        //$mail->AltBody = "Olá, $nomeUsuario! Para ativar sua conta, copie e cole o link no navegador: http://localhost/filmix/validaEmail.php?token=$token";
        return $mail->send();        
    } catch (Exception $e) {
        echo "Erro detalhado com PHPMailer: ($mail->ErrorInfo)";
        return false;
    }
}
?>