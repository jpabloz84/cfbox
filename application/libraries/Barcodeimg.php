<?php

class Barcodeimg{
  private $numError;
  private $descError;
 private $html='';
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    require_once dirname(__FILE__).'/barcode-img/barcode.php';
    $barcodeImg=new barcode_generator();
    $CI=& get_instance();
    $CI->barcodeImg=$barcodeImg;
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






  function base64img($codigo,$format,$symbology,$options)
  {
    
    $this->numError=0;
    $this->descError='';
    $codbarras="";
    $basefile=BASE_PATH_FISICO_FILE;
    try{
    //$format='png';
    //$symbology='code-39-ascii';
    //$options=array('w' =>550 ,'h'=>100 ); 
    $generator = new barcode_generator();
    $imagedata = $generator->render_image($symbology, $codigo, $options);
    //  $imagedata = $this->render_image($symbology, $codigo, $options);
    $name=randString(10);
    $pathimgbarcode='/files/codebar/tmp-'.$name.'.'.$format;
    imagepng($imagedata,$basefile.$pathimgbarcode);
    imagedestroy($imagedata);
    $image = file_get_contents($basefile.$pathimgbarcode);
    $type = pathinfo($basefile.$pathimgbarcode, PATHINFO_EXTENSION);        
    $codbarras = 'data:image/' . $type . ';base64,' . base64_encode($image);
    unlink($basefile.$pathimgbarcode);
    }catch (Exception $e) {
        $this->numError=1;
        $this->descError=$e->getMessage();
    }
    return $codbarras;
   
  }

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