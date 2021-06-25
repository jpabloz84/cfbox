<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Fwxml
{
	public $numError=0;
	public $descError="";
	public $xmlResponse="";
	public $arrResponse=array();
	 function __construct()
	 {

	 }
	 function Response()
	 {
	 	$respuesta='<xml version="1.0"><responseData>';
	 	$respuesta.='<numError>'.$this->numError.'</numError>';
	 	$respuesta.='<descError><![CDATA['.$this->descError.']]></descError>';
	 	$respuesta.='<Data><![CDATA['.$this->xmlResponse.']]></Data>';
	 	$respuesta.='</responseData></xml>';
	 	echo $respuesta;
	 	die();
	 }

	 function ResponseJson()
	 {
	 	$arrJson=array('numerror' =>$this->numError , 'descerror'=>$this->descError,'data'=>$this->arrResponse);
		echo json_encode($arrJson, JSON_HEX_QUOT);
		die();         
     }
	 

}//fin de la clase response
?>
