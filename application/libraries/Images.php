<?php
class Images{
private $numError;
private $descError;
private $html='';
private $pathfilebase=BASE_PATH_FISICO_FILE."/";
public $calidad=70;
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    
  }
  function __destruct()
  { 

  }

  function resampleimg($base64_string)
  {
    $this->numError=0;
    $this->descError='';
    $base64return="";
    try{
    
    $final_file= $this->resampleimgintofile($base64_string);
    $type = pathinfo($final_file, PATHINFO_EXTENSION);
    $data = file_get_contents($final_file);
    $base64return = 'data:image/' . $type . ';base64,' . base64_encode($data);
    
    unlink($final_file);
    
    }catch (Exception $e) {
        $this->numError=99;
        $this->descError=$e->getMessage();
    }
    return $base64return;
  }



//resamplea imagenes png porque en algunos servidores, el png se ve negro
  function resampleimgintofile($base64_string,$extension="jpg")
  {
    $this->numError=0;
    $this->descError='';
    $base64return="";
    $final_file="";
    try{
   $directorio =  $this->pathfilebase."files/tmp/"; // directorio
      if (!is_dir($directorio)) {
        mkdir($directorio);
      }

    $path_img="";
    $data = explode(',', $base64_string);
    //$extension ="png";
    $imgdata=null;
    if(count($data)>1){
    $imgdata=$data[1];  
    }else{
    $imgdata=$base64_string;
    }
    //$f = finfo_open();
    //$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
    $imgtmpname =strtolower(substr(md5(uniqid(rand())),0,10)).".".$extension;
    $output_file=$directorio.$imgtmpname;
    $final_file=$directorio.'final_'.$imgtmpname;
    $file = fopen($output_file, "wb");
    fwrite($file, base64_decode($imgdata));
    fclose($file);
    if($extension=="png"){
    $png=imagecreatefrompng($output_file);  
    /*imagealphablending($png, false);
    imagesavealpha($png, true);
    imagepalettetotruecolor($png);*/
    $x_dimension = imagesx($png);
    $y_dimension = imagesy($png);
    $new_image = imagecreatetruecolor($x_dimension, $y_dimension);

    // create a transparent canvas
   $trans_color = imagecolorallocatealpha($new_image, 0x00, 0x00, 0x00, 127);
   imagefill($new_image, 0, 0, $trans_color);

     for ($x = 0; $x < $x_dimension; $x++) {
          for ($y = 0; $y < $y_dimension; $y++) {
          $rgb = imagecolorat($png, $x, $y);
          $r = ($rgb >> 16) & 0xFF;
          $g = ($rgb >> 8) & 0xFF;
          $b = $rgb & 0xFF;
          $alpha = ($rgb & 0x7F000000) >> 24;
          //$pixel = custom_function($r, $g, $b);
          imagesetpixel($new_image, $x, $y, imagecolorallocatealpha($png, $r, $g, $b, $alpha));
      }
      }
     imagesavealpha($new_image, true);
     imagepalettetotruecolor($new_image);
    imagepng($new_image,$final_file);   
    }else{
      $input=imagecreatefrompng($output_file);  
   // $input = imagecreatefrompng($input_file);
    $width = imagesx($input);
    $height = imagesy($input);
    $output = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($output,  255, 255, 255);
    imagefilledrectangle($output, 0, 0, $width, $height, $white);
    imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
    imagejpeg($output, $final_file);
    //imagejpeg($new_image,$final_file);

    //imagejpeg($jpg,$final_file);   
    }
    
    unlink($output_file);
    }catch (Exception $e) {
        $this->numError=99;
        $this->descError=$e->getMessage();
    }
    return  str_replace($this->pathfilebase, "", $final_file);
  }

  

  
   function numError()
   {
    return $this->numError;
   }

  function descError()
  {
    return $this->descError;
  }

  function save($pathfile='')
  {
    $this->numError=0;
    $this->descError='';
    $pathfileret='';
    try{
        if($pathfile=='')
        {
          $folder=getcwd()."/files/";
          $pathfileret=tempnam($folder, "tmp");
        }else
        {
          $pathfileret=$pathfile;
        }
        //si existe, lo borro
        if(file_exists($pathfileret)){
        unlink($pathfileret);    
        }
      
      file_put_contents($pathfileret,$this->html);
    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
    return $pathfileret;
  }


  function resizeImagen($pathorigen, $pathdestino, $alto, $ancho){
    $exito=false;
    $this->numError=0;
    $this->descError="";
    try {
      
      $info=getimagesize($pathorigen);        
      $type    = $info[2];
      $width   = $info[0]; // you don't need to use the imagesx and imagesy functions
      $height  = $info[1];

      switch($type) {
          case IMAGETYPE_JPEG:          
              $img_original = imagecreatefromjpeg($pathorigen);
              break;
          case IMAGETYPE_GIF:         
              $img_original = imagecreatefromgif($pathorigen);
              break;
          case IMAGETYPE_PNG:         
              $img_original = imagecreatefrompng($pathorigen);
              break;
          default:
              throw new Exception('This file is not in JPG, GIF, or PNG format!');
      }
      $max_ancho = $ancho;
      $max_alto = $alto;
      list($ancho,$alto)=getimagesize($pathorigen);
      $x_ratio = $max_ancho / $ancho;
      $y_ratio = $max_alto / $alto;
      if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho 
      $ancho_final = $ancho;
      $alto_final = $alto;
    } elseif (($x_ratio * $alto) < $max_alto){
      $alto_final = ceil($x_ratio * $alto);
      $ancho_final = $max_ancho;
    } else{
      $ancho_final = ceil($y_ratio * $ancho);
      $alto_final = $max_alto;
    }
    
      $tmp=imagecreatetruecolor($ancho_final,$alto_final);
      imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
      imagedestroy($img_original);
      //$calidad=70;

      switch($type) {
          case IMAGETYPE_JPEG:          
              imagejpeg($tmp,$pathdestino,$this->calidad);
              break;
          case IMAGETYPE_GIF:         
              imagegif($tmp,$pathdestino,$this->calidad);
              break;
          case IMAGETYPE_PNG:         
              imagepng($tmp,$pathdestino);
              break;
          default:
              throw new Exception('This file is not in JPG, GIF, or PNG format!');
      }
      $tmp=null;
      $exito=true;
    } catch (Exception $e) {
      $this->numError=98;
      $this->descError=$e->getMessage();
      $exito=false;
    }
     return $exito; 
  
}//resizeImagen

}
?>