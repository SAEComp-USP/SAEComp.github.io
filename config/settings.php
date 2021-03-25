<?php
//session_cache_limiter('private');
//session_cache_expire(10); //tempo de sessão igual 10 minutos
session_name(md5('ILoveSEnC2018'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])); //session name
session_start();

//SITE
define('URL_SITE','https://www.saecomp.com.br/');

define('PATH_SITE',getcwd());
define('PATH_INSCRICAO',getcwd());


define('UPLOAD_DIR_USER','/var/www/uploads/fotos/');

// define('GUSER', 'no-reply@saecomp.com.br'); 
// define('GPWD', 'no_reply_s@ecomp_159'); 

define('GUSER', 'saecomp@saecomp.com.br'); 
define('GPWD', 'mail_s@ec0mp_s@ecomp_159'); 

define('SOCIAL_FACE', 'https://www.facebook.com/saecomp');	
define('SOCIAL_INSTA', 'https://www.instagram.com/saecomp.ec');	
define('SOCIAL_LINKE', 'https://www.linkedin.com/company/saecomp'); 
define('SOCIAL_TUBE', '');	
define('SOCIAL_TW', '');
	
//SISTEMA
define('SYS_VERSAO','1.0.0.0');

$token_user = md5('ILoveSEnC2018'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);

// echo "GERADOR ".$token_user."<br>";
// echo "SESSAO ".$_SESSION["token_user_senc2018"]."<br>";

function get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(isset($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
    $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

function randString($size){
  $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  $return= "";
  for($count= 0; $size > $count; $count++){
    //Gera um caracter aleatorio
    $return.= $basic[rand(0, strlen($basic) - 1)];
  }
  return ($return.time());
}
function convert_data_br($data_sql){
  return date('d/m/Y', strtotime($data_sql));
}

function convert_data_hora_br($data_sql){
  return date('d/m/Y H:i:s', strtotime($data_sql));
}

function convert_hora_exibe($hora){
  $hora_exibe = explode(":",$hora);
  if($hora_exibe[1] == "00"){
    $minutos = ""; 
  }
  else{
    $minitos = $hora_exibe[1];
  }
  return $hora_exibe[0]."h".$minitos;
}
function protect( &$str ) {
  /*** Função para retornar uma string/Array protegidos contra SQL/Blind/XSS Injection*/
  if( !is_array( $str ) ) {                      
    $str = preg_replace( '/(from|select|insert|delete|where|drop|union|order|update|database)/i', '', $str );
    $str = preg_replace( '/(&lt;|<)?script(\/?(&gt;|>(.*))?)/i', '', $str );
    $tbl = get_html_translation_table( HTML_ENTITIES );
    $tbl = array_flip( $tbl );
    $str = addslashes( $str );
    $str = strip_tags( $str );
    return strtr( $str, $tbl );
  } else {
    return array_filter( $str, "protect" );
  }
}

function validaCPF($cpf) {
    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf{$c} * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf{$c} != $d) {
            return false;
        }
    }
    return true;
}
function gerar_barcode($barcode,$factor,$size){
  $path = URL_SITE."class/barcode.php?codetype=Code128&text=".$barcode."&size=".$size."&sizefactor=".$factor;
  $type = pathinfo($path, PATHINFO_EXTENSION);
  $data = file_get_contents($path);
  $base64 = base64_encode($data);
  return "<img class='barcode_boleto' alt='".$barcode."' src='data:image/png;base64,".$base64."' />";
}
?>