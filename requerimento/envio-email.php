<?php
include_once('../config/settings.php'); 

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../class/mail/src/Exception.php';
require '../class/mail/src/PHPMailer.php';
require '../class/mail/src/SMTP.php';

define('RECAPTCHA_SECRET_KEY', '6LeVuogUAAAAANzGbZewG0j3v2sDuCQeXPqOBO9k');
// //json response helper
$json_response = function($data = []) {
    header('Content-Type: application/json; charset=utf-8');
    exit(json_encode($data));
};

$dataEnvio = date('d/m/Y');
$horaEnvio = date('H:i:s');

// handle post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $nome = trim(stripslashes($_POST['nome_form']));
    $email = trim(stripslashes($_POST['email_form']));
    $assunto = trim(stripslashes($_POST['assunto_form']));
    $mensagem = trim(stripslashes($_POST['mensagem_form']));


    if (!isset($_POST['g-recaptcha-response'])) {
        $error['recaptcha'] = 'Selecione o reCAPTCHA';
    }
    // Check Name
    if (strlen($nome) < 2) {
        $error['name'] = "Informe seu nome completo!";
    }
    // Check Email
    if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $email)) {
        $error['email'] = "Informe um e-mail válido!";
    }
    // Check Message
    if (strlen($mensagem) < 10) {
        $error['message'] = "Por favor insira a sua mensagem. Deve ter pelo menos 10 caracteres.";
    }


    // if all good call API else error out
    if (!empty($error)) {
      $response_e = (isset($error['name'])) ? $error['name'] . "<br /> \r\n" : null;
      $response_e .= (isset($error['email'])) ? $error['email'] . "<br /> \r\n" : null;
      $response_e .= (isset($error['message'])) ? $error['message'] . "<br /> \r\n" : null;
      $response_e .= (isset($error['recaptcha'])) ? $error['recaptcha'] . "<br />" : null;

      $json_response(['errors' => $response_e]);
    }

    // call recaptcha site verify
    $response = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?'.http_build_query([
            'secret'   => RECAPTCHA_SECRET_KEY,
            'response' => $_POST['g-recaptcha-response'],
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ])
    );
    $response = json_decode($response, true);
    
    $arquivo = '<html style="margin:10px auto;font-family:arial;font-size:14px;color: #313131;display: table;position: relative;"><table style="border: 1px solid #e2e2e2;border-right: 0;border-top: 0;" width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#fff">
        <tr>
        <td colspan="2" style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;">
                <img style="width: 150px;margin:10px auto;display: table;position: relative;" src="'.URL_SITE.'images/logo-footer.png">
              </td>
          </tr>
          <tr>
              <td style="padding: 10px;text-align: center;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;" colspan="2"><b>CONTATO VIA SITE:</b></td>
          </tr>
          <tr>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;"><b>Nome:</b></td>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;">'.$_POST['nome_form'].'</td>
          </tr>
          <tr>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;"><b>E-mail:</b></td>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;"><a href="mailto:'.$_POST['email_form'].'">'.$_POST['email_form'].'</a></td>
          </tr>
          <tr>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;"><b>Assunto:</b></td>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;">'.$_POST['assunto_form'].'</td>
          </tr>
          <tr>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;"><b>Mensagem:</b></td>
              <td style="padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;">'.$_POST['mensagem_form'].'</td>
          </tr>
          <tr>
              <td class="horaEnvio" style="text-align: center;padding: 10px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;font-size: 13px;" colspan="2"><b>Mensagem enviada dia '.$dataEnvio.' às '.$horaEnvio.'</b></td>
          </tr>
          <tr>
            <td colspan="2" style="padding: 20px;border: 1px solid #e2e2e2;border-left: 0;border-bottom: 0;">
                <p class="endereco" style="text-align: center;font-size: 14px;" >
                 SAEComp - Secretaria Acadêmica da Engenharia de Computação<br>
                 USP São Carlos<br>
                 <b>contato@saecomp.com.br</b>
               </p>
             </td>
          </tr>
      </table></html>';

    // handle status and respond with json
    if (intval($response["success"]) !== 1) {
      $json_response(['errors' => 'Selecione o reCAPTCHA!']);
    } 
    else{


      $mail = new PHPMailer;
      $mail->CharSet = "UTF-8";
      $mail->isSMTP();
      $mail->SMTPDebug = 0;

      $mail->Host = 'smtp.kinghost.net';
      $mail->Port = 587;
      $mail->SMTPSecure = 'tls';

      $mail->SMTPAuth = true;
      $mail->Username = GUSER;
      $mail->Password = GPWD;

      $mail->SetFrom("no-reply@saecomp.com.br","SAEComp");

      $mail->addAddress("saecomp.ec@gmail.com","SAEComp");
      //$mail->AddBCC("contato@saecomp.com.br","SAEComp");              

      $mail->Subject = 'Contato via site - SAEComp';
      $mail->msgHTML($arquivo, __DIR__);


      if($mail->send()){
        $json_response(['success' => true]);
      }
      else{
        $json_response(['success' => false]);
      }   
    }
}
?>