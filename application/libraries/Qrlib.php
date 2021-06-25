<?php

class Qrlib{
  private $numError;
  private $descError;
 private $html='';
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    require_once dirname(__FILE__).'/phpqrcode/qrlib.php';
    $qrlib=new QRcode();
    $CI=& get_instance();
    $CI->qrlib=$qrlib;
  }
  function __destruct()
  { 

  }


  
   function numError()
   {
    return $this->numError;
   }

  function descError()
  {
    return $this->descError;
  }


  /*$nivel="L"; //nivel de seguridad
    $tamanio=10; //5,10,15,20,25 tamaño de la imagen
    $marco=0;*/
  function png($codigo,$filename,$nivel,$tamanio,$marco)
  {
     
    $this->numError=0;
    $this->descError='';
    
    try{
        QRcode::png($codigo,BASE_PATH_FISICO_FILE."/".$filename,$nivel,$tamanio,$marco);
    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
   
  }
    //agrega logo al codigo QR
  function addlogo($fileqrpng,$filelogo){
  
  //$basefile=(new Ruta)->basepath();
  //$imagenlogopath=$basefile."vistas/img/qr/bb.png";
   $this->numError=0;
   $this->descError='';
    try{
      $QR = imagecreatefrompng(BASE_PATH_FISICO_FILE."/".$fileqrpng);
      $logo = imagecreatefromstring(file_get_contents(BASE_PATH_FISICO_FILE."/".$filelogo));
      $QR_width = imagesx($QR);
      $QR_height = imagesy($QR);  
      $logo_width = imagesx($logo);
      $logo_height = imagesy($logo);  
      // Scale logo to fit in the QR Code
      $logo_qr_width = $QR_width/6;
      $scale = $logo_width/$logo_qr_width;
      $logo_qr_height = $logo_height/$scale;
      
      if(imagecopyresampled($QR, $logo, $QR_width/2.3, $QR_height/2.3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height))
      {
      imagepng($QR,BASE_PATH_FISICO_FILE."/".$fileqrpng);      
      }
      imagedestroy($QR);

    }catch (Exception $e) {
        $this->numError=99;
        $this->descError=$e->getMessage();
    }
  }//addlogo
}
?>