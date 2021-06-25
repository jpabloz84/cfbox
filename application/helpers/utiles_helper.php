<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
    //Nos aseguramos de que no haya conflictos con otras funciones
    if(!function_exists('loadXML')){
	       function loadXML($data) {
		  $xml = @simplexml_load_string($data);
		  if (!is_object($xml))
		    throw new Exception('Error en la lectura del XML',1001);
		  return $xml;
		}
    }



	
if(!function_exists('resizeImagen')){
	function resizeImagen($ruta, $nombre, $alto, $ancho,$nombreN,$extension){
		$exito=false;
		try {
			$rutaImagenOriginal = $ruta.$nombre;
	    $info=getimagesize($rutaImagenOriginal);
	    	
	    $type    = $info[2];
    	$width   = $info[0]; // you don't need to use the imagesx and imagesy functions
    	$height  = $info[1];

	    switch($type) {
	        case IMAGETYPE_JPEG:	        
	            $img_original = imagecreatefromjpeg($rutaImagenOriginal);
	            break;
	        case IMAGETYPE_GIF:	        
	            $img_original = imagecreatefromgif($rutaImagenOriginal);
	            break;
	        case IMAGETYPE_PNG:	        
	            $img_original = imagecreatefrompng($rutaImagenOriginal);
	            break;
	        default:
	            throw new Exception('This file is not in JPG, GIF, or PNG format!');
	    }

	    /*
	    $extension=strtoupper($extension);
	    if($extension == 'GIF' || $extension == 'gif'){	    
	    $img_original = imagecreatefromgif($rutaImagenOriginal);
	    }
	    if($extension == 'jpg' || $extension == 'JPG'){	    	
	    $img_original = imagecreatefromjpeg($rutaImagenOriginal);
	    }
	    if($extension == 'png' || $extension == 'PNG'){	    	
	    $img_original = imagecreatefrompng($rutaImagenOriginal);
	    }*/
	    /*echo "extension".$extension;
	    print_r(getimagesize($img_original));
	    die("fin info imagen");*/
	    $max_ancho = $ancho;
	    $max_alto = $alto;
	    list($ancho,$alto)=getimagesize($rutaImagenOriginal);
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
		//echo "ancho".$ancho_final."alto:".$alto_final;
		/*if($img_original ==null)
		{
			echo "es nulo";
		}*/
			//echo "tamanio".sizeof($img_original);
	    $tmp=imagecreatetruecolor($ancho_final,$alto_final);
	    imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
	    imagedestroy($img_original);
	    $calidad=70;
	    imagejpeg($tmp,$ruta.$nombreN,$calidad);
			$exito=true;
		} catch (Exception $e) {
			
		}
				
	    return $exito;
	    
	}
}//resizeImagen

if(!function_exists('todate')){
	//dada una cadena , convierte a date segun el codigo de formato sql server
	function todate($strdate,$codigo){
		$str="";
		//conviete de yyyy-mm-dd
		if($codigo==103)
		{
			$p=explode("/",$strdate);
			if(count($p)!=3)
			{
				$p=explode("-",$strdate);
			}
			if(count($p)!=3)
			{
				$p=explode("\\",$strdate);
			}

			if(count($p)==3)
			{			
			$str=((string)$p[2])."-".((string)$p[1])."-".((string)$p[0]);
			}
		}
		//formato dd-mm-yyyy a  YYYYMMDD
		if($codigo==112)
		{
			$p=explode("/",$strdate);
			if(count($p) != 3)
			{
				$p=explode("-",$strdate);
			}
			if(count($p) != 3)
			{
				$p=explode("\\",$strdate);
			}

			if(count($p)==3)
			{
			$str=((string)$p[2]).((string)$p[1]).((string)$p[0]);
			}
		}
	    
	    return $str;
	    
	}
}//todate


if(!function_exists('datetostr')){
	//dada una cadena , convierte a string
	function datetostr($strdate,$formato='yyyymmdd'){
		$str="";
		
		if($strdate=="" || $strdate==null) return;
		$strdate=substr($strdate,0,10);
		if($formato=='yyyymmdd')
		{
			$p=explode("/",$strdate);
			if(count($p)!=3)
			{
				$p=explode("-",$strdate);
			}
			if(count($p)!=3)
			{
				$p=explode("\\",$strdate);
			}

			if(count($p)==3 && len($p[2])==4)
			{			
			$str=((string)$p[2]).((string)$p[1]).((string)$p[0]);
			}	

			if(count($p)==3 && strlen($p[0])==4)
			{			
			$str=((string)$p[0]).((string)$p[1]).((string)$p[2]);
			}
		}
		//sea yyyy-mm-dd convierte a dd-mm-yyyy
		$strdate=substr($strdate,0,10);
		if($formato=='dd-mm-yyyy')
		{
			$p=explode("/",$strdate);
			if(count($p)!=3)
			{
				$p=explode("-",$strdate);
			}
			if(count($p)!=3)
			{
				$p=explode("\\",$strdate);
			}

			if(count($p)==3 && strlen($p[0])==4)
			{			
			$str=((string)$p[2])."-".((string)$p[1])."-".((string)$p[0]);
			}	
			
		}
		//sea un date con formato yyyy-mm-dd lo convierte a formato string DIA 1 ,mes año
		if($formato=="string"){
			$f=explode("-",$strdate);
			$m=(int)$f[1];
			$d=(int)$f[2];
			$a=(int)$f[0];
			$dat=mktime(0,0,0,$m,$d,$a);
			$dia_desc=date("N",$dat);	
			$dia=date("j", $dat);
			$mes=date("n", $dat);	
			$anio=date("Y", $dat);
			$diadesc='';
			switch ($dia_desc) {
				case 1:
					$diadesc='Lunes';
					break;
					case 2:
					$diadesc='Martes';
					break;
					case 3:
					$diadesc='Miércoles';
					break;
					case 4:
					$diadesc='Jueves';
					break;
					case 5:
					$diadesc='Viernes';
					break;
					case 6:
					$diadesc='Sábado';
					break;
					case 7:
					$diadesc='Domingo';
					break;
				default:					
					break;
			}
			$mesdesc="";
			switch ($mes) {
				case 1:
					$mesdesc='Enero';
					break;
					case 2:
					$mesdesc='Febrero';
					break;
					case 3:
					$mesdesc='Marzo';
					break;
					case 4:
					$mesdesc='Abril';
					break;
					case 5:
					$mesdesc='Mayo';
					break;
					case 6:
					$mesdesc='Junio';
					break;
					case 7:
					$mesdesc='Julio';
					break;
					case 8:
					$mesdesc='Agosto';
					break;
					case 9:
					$mesdesc='Septiembre';
					break;
					case 10:
					$mesdesc='Octubre';
					break;
					case 11:
					$mesdesc='Noviembre';
					break;
					case 12:
					$mesdesc='Diciembre';
					break;
				default:					
					break;
			}
	
			$str=$diadesc." ".$dia." de ".$mesdesc." de ".$anio;
		}
	    return $str;
		
	}
}//date to str

if(!function_exists('strtodate')){
	//dada una cadena , convierte a date , el formato que se pasa, es de como viene
	function strtodate($strdate,$formatoin='yyyymmdd',$formatoout='yyyy-mm-dd'){
		$str="";
		
		if($formatoin=='yyyymmdd' && $formatoout=='yyyy-mm-dd')
		{
			$yyyy=substr($strdate,0,4);	
			$mm=substr($strdate,4,2);	
			$dd=substr($strdate,6,2);	
			$str=$yyyy."-".$mm."-".$dd;
		}

		if($formatoin=='yyyymmddhhmmss' && $formatoout=='yyyy-mm-dd hh:mm:ss')
		{
			$yyyy=substr($strdate,0,4);	
			$mm=substr($strdate,4,2);	
			$dd=substr($strdate,6,2);	
			$hh=substr($strdate,8,2);	
			$min=substr($strdate,10,2);	
			$ss=substr($strdate,12,2);	
			$str=$yyyy."-".$mm."-".$dd." ".$hh.":".$min.":".$ss;
		}
	    return $str;
	}
}//date to str




if(!function_exists('randString')){

	function randString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
}

if(!function_exists('es_entero')){
function es_entero($valor)
{ $v=trim((string)$valor);
	if($v==""){
		return false;
	}
	return preg_match("/^[0-9]+$/", $v);
}
}//es entero
if(!function_exists('es_flotante')){
function es_flotante($valor){
	
	$v=trim((string)$valor);
	if($v==""){
		return false;
	}
	return preg_match("/^\d+\.?\d*$/", $v);
}
}//es_flotante

if(!function_exists('bar25')){

function	bar25($t)
{
$a=chr(33);
for ($x=0;$x<strlen($t);$x+=2)
{
$v=intval(substr($t,$x,2));
$c="";
if ($v>=0 and $v<=91)
{
$c=chr($v+35);
}

if ($v==92)
{
$c=chr(196);
}
if ($v==93)
{
$c=chr(197);
}
if ($v==94)
{
$c=chr(199);
}
if ($v==95)
{
$c=chr(201);
}
if ($v==96)
{
$c=chr(209);
}
if ($v==97)
{
$c=chr(214);
}

if ($v==98)
{
$c=chr(220);
}
if ($v==99)
{
$c=chr(225);
}

$a.=$c;
}//fin del for
$a.=chr(34);

return $a;
}//fin de la funcion

}//bar25





?>
