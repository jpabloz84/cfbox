<?php
//use PHPMailer\lib\class.phpmailer.php;
class Mailer{
  private $numError;
  private $descError;
  private $html='';
  private $debug=0;
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    require_once dirname(__FILE__).'/PHPMailer/PHPMailerAutoload.php';
    $objeto=new PHPMailer();
    $CI=& get_instance();
    $CI->mailer=$objeto;
  }
  function __destruct()
  { 

  }
  function setdebug($debug){
    $this->debug=$debug;
  }
   function numError()
   {
    return $this->numError;
   }

  function descError()
  {
    return $this->descError;
  }

  function sendsimplemail($destinatarios,$asunto,$htmlcuerpo,$remitente,$descripcionremitente='',$con,$archivos=null)
  {
    $this->numError=0;
    $this->descError='';
    $exito=false;
    try{
      $errorInfo="";
      $correo = new PHPMailer(); //Creamos una instancia en lugar usar mail()
      $correo->CharSet = 'UTF-8';
      if($con['ssl']){
      $correo->IsSMTP();  
      }
      
    //$correo->SMTPDebug  = 0;
    $correo->SMTPDebug  =$this->debug;
    //Ahora definimos gmail como servidor que aloja nuestro SMTP
    $correo->Host       = $con['host'];
    //El puerto será el 587 ya que usamos encriptación TLS
    $correo->Port       = $con['ptosalida'];
    //Definmos la seguridad como TLS
    $correo->SMTPSecure = 'tls';
    //Tenemos que usar gmail autenticados, así que esto a TRUE
    $correo->SMTPAuth   = $con['ssl'];
    //Definimos la cuenta que vamos a usar. Dirección completa de la misma
    $correo->Username   = $con['mail'];
    //Introducimos nuestra contraseña de gmail
    $correo->Password   = $con['pass'];
    $correo->setFrom($remitente, $descripcionremitente);
    $correo->From = $remitente;  
    $correo->FromName = $descripcionremitente;    
    //Usamos el AddReplyTo para decirle al script a quien tiene que responder el correo
    $correo->AddReplyTo($remitente,$descripcionremitente); 
     $arrDestinatarios=explode(";",$destinatarios);
      foreach($arrDestinatarios as $key => $value) {
        //Usamos el AddAddress para agregar un destinatario
        //echo "Email:".$value;
        $correo->AddAddress($value, $value);
      //$correo->AddReplyTo($value, $value); //anda?
      }
      //Ponemos el asunto del mensaje
      $correo->Subject = $asunto;       
      /*
       * Si deseamos enviar un correo con formato HTML utilizaremos MsgHTML:
       * $correo->MsgHTML("<strong>Mi Mensaje en HTML</strong>");
       * Si deseamos enviarlo en texto plano, haremos lo siguiente:
       * $correo->IsHTML(false);
       * $correo->Body = "Mi mensaje en Texto Plano";
       */

      $correo->MsgHTML($htmlcuerpo);
      //Si deseamos agregar un archivo adjunto utilizamos AddAttachment
      // ejemplo $correo->AddAttachment("images/phpmailer.gif");
      if(isset($archivos)){
        if(is_array($archivos)){
          foreach($archivos as $patharchivo) {
            $correo->AddAttachment($patharchivo);
          }
        }
      }
        //Enviamos el correo
        if($correo->Send()) {
        $exito=true;   
        } else {
          $this->numError=1;
          $this->descError="No se envio mail:".$correo->ErrorInfo;
        }  

    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
    return $exito;
  }

}
?>