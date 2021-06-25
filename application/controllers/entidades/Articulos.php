<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos extends CI_Controller {
private $visitante;
	function  __construct()
	{ date_default_timezone_set('America/Argentina/Buenos_Aires'); 
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");
    $this->load->library("images");		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);

	}
	 function index()
	{	

		if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['categorias']=$this->mservice->getCategorias();
		//$this->load->view('entidades/varticulos',$arrVariales);
		$this->load->view('entidades/varticulos',$arrVariales);
		
	}	

	function loginOn(){
		$exito=true;
		$visitante=unserialize($this->session->visitante);
		if(!isset($this->visitante) || $this->visitante==null)
		{		
		$exito=false;
		}

		if($exito && !$this->visitante->logueado())
		{		
		$exito=false;
		}
		return $exito;	
	}

	function listener($id_param=0)
	{
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->arrResponse=array();
		$arrRow=array();
    $campos=array();
    $id_articulo=0;
    $base64img="";
    $pathimg="";
	 	if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}
    if(!$this->visitante->permitir('prt_articulos',2)){
      $this->fwxml->numError=1;
      $this->fwxml->descError="no tiene permiso para esto";
      $this->fwxml->ResponseJson();   
      return;
    }

		$metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
    if($metodo !="DELETE"){
      $json=json_decode(file_get_contents('php://input'), true); 
      $id_articulo=$json["id_articulo"];
      $campos['articulo']=$json["articulo"];
      $campos['id_categoria']=$json["id_categoria"];
      $campos['id_fraccion']=$json["id_fraccion"];
      $campos['precio_base']=$json["precio_base"];
      $campos['id_alicuota']=$json["id_alicuota"];
      $campos['precio_iva']=$json["precio_iva"];
      $campos['precio_venta']=$json["precio_venta"];
      $campos['habilitado']=$json["habilitado"];
      $campos['codbarras']=$json["codbarras"];
      $campos['detalle']=trim($json["detalle"]);
      $campos['stock']=$json["stock"];
      $base64img=$json["img"];
    }
		//eliminacion
		if($metodo=="DELETE" && $id_param > 0)
		{		
		$campos=array('id_articulo' =>$id_param);
		$id=$this->mservice->eliminar_registro('articulos',$campos);		
		}
    //actualizaciones
    if($id_articulo>0 && $metodo!="DELETE")
    { 
    $campos['articulo']=strtoupper($campos['articulo']);
    $condicional=array('id_articulo' => $id_articulo);
    $this->mservice->actualizar_registro('articulos',$campos,$condicional);
    $this->fwxml->arrResponse=array('id_articulo' =>  $id_articulo);;
    }
		//alta
		if($id_articulo=="0" && $metodo!="DELETE")
		{
    //unset($campos['id_articulo']);
    $campos['id_sucursal']=$this->visitante->get_id_sucursal();
		$campos['articulo']=strtoupper($campos['articulo']);
		$id=$this->mservice->insertar('articulos',$campos);
		$this->fwxml->arrResponse=array('id_articulo' => $id);
    $id_articulo=$id;
		}
     //echo "aca0 ".$base64img."metodo ".$metodo;
    //si hay imagen, la transformo, la redimensiono y la guardo con el nombre que va
    if($metodo!="DELETE" && $base64img!=""){
      
      if(!$this->saveimagearticulo($base64img,$id_articulo)){
        $this->fwxml->numError=98;
        $this->fwxml->descError="error al guardar imagen";
      }
    }
		//actualizacoines
		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
    $this->fwxml->ResponseJson();		

	}


function saveimagearticulo($base64img,$id_articulo){

  $pathfinal="";
  $filename="";
  $exito=false;
  $date=new Datetime();
  $pathdestino="files/articulos/";
  $dirbase=BASE_PATH_FISICO_FILE."/";
  //si no existe el directorio base, lo creo
  if (!is_dir($dirbase.$pathdestino)){
        mkdir($dirbase.$pathdestino);
  }
  $strfecha=$date->format("YmdHis");  
  $rs=$this->mservice->get('verarticulos',array("*"),"id_articulo=$id_articulo","");
  if(count($rs)>0){
    $id_empresa=$rs[0]['id_empresa'];
    $pathold=$rs[0]['img'];

    //si existe la imagen anterior, lo borro
    if(file_exists($pathold)){
    unlink($pathold);    
    }
    $pathdestino.=$id_empresa."/";
    //si no existe el directorio base de la empresa en la carpeta articulos, lo creo
    //echo $dirbase.$pathdestino;
    if (!is_dir($dirbase.$pathdestino)){
          mkdir($dirbase.$pathdestino);
    }
    $articulo=strtolower(str_replace(" ","_",$rs[0]['articulo']));
    $filename=$id_articulo."_".$articulo."_".$strfecha.".png";
    $pathdestino.=$filename;
    $pathtmp=$this->images->resampleimgintofile($base64img,"png"); //convierto imagen binaria a fisica
    $pathdestinoabsoluto=$dirbase.$pathdestino;
    if($this->images->resizeImagen($pathtmp,$pathdestinoabsoluto,150,150)){ //redimensiono la imagen fisica
      $campos=array('img' =>$pathdestino);  
      $condicional=array('id_articulo' => $id_articulo);  
      $this->mservice->actualizar_registro('articulos',$campos,$condicional);
      $exito=true;
    }
    //si existe archivo temporal, lo borro
    if(file_exists($pathtmp)){
    unlink($pathtmp);    
    }
  }

  return $exito;
}

 




	function exportar(){
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$filereport="";
  	$id_articulo=trim($this->input->post("id_articulo"));
  	$codbarras=trim($this->input->post("codbarras"));
  	$id_categoria=trim($this->input->post("id_categoria"));
  	$articulo=$this->input->post("articulo");
  	
  	$cond="1=1 ";
  	if($id_articulo!=""){
  		$cond.=" and id_articulo=$id_articulo ";
  	}else{
  		if($codbarras!=""){
  			$cond.=" and codbarras>='$codbarras' ";
  		}
  		if($id_categoria!=""){
  			$cond.=" and id_categoria=$id_categoria ";
  		}
  		if($articulo!=""){
  			$cond.=" and articulo like '$articulo%' ";
  		}
  		
  	}
  	  	
  	try {
  		$this->load->library('documento');
  		
  		$cmp=array("id_articulo","articulo","categoria","cat_abreviada","precio_base","alicuota","iva","precio_iva","precio_venta","cast(CASE WHEN tipo_dato='int' THEN CAST(stock AS UNSIGNED) ELSE CAST(stock AS DECIMAL(11,2)) END as CHAR(5)) AS stock","fraccion","CASE WHEN habilitado=1 THEN 'SI' ELSE 'NO' END AS articuloHabilitado","codbarras","CASE WHEN generico=1 THEN 'SI' ELSE 'NO' END AS es_generico","CASE WHEN mueve_stock=1 THEN 'SI' ELSE 'NO' END AS mueveStock");
			$xmldata=$this->mservice->getxmldata("verarticulos",$cmp,$cond," articulo desc");
				//echo $this->mservice->laststmt;
				if($this->mservice->numError()==0)
				{ 
				$this->documento->parseXsl('tpl/Excel_tpl.xsl',$xmldata);
				$file="articulos_".randString(6).".xls";

				$filereport=$this->documento->save('files/reportes/'.$file);	
					if($this->documento->numError()==0){			
					$this->fwxml->descError=	$filereport;				
					}else
					{
					$this->fwxml->numError=	$this->documento->numError();
					$this->fwxml->descError=	$this->documento->descError();
					}
				}else
				{				
							$this->fwxml->numError=	$this->mservice->numError();
							$this->fwxml->descError=	$this->mservice->descError();
				}
  		
  	} catch (Exception $e) {
  		$this->fwxml->numError=100;
    	$this->fwxml->descError=$e->getMessage();      		
  	}

		$this->fwxml->ResponseJson();		
	}//exportar
	

}//class
