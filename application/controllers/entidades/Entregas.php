<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Entregas extends CI_Controller {
private $visitante;
private $definiciones=array(); //para uso del oyente
private $selectPremiosClientes=""; //varible que utilizo para cambiar la consulta segun parametros que vengan
private $id_sucursal;
private $id_empresa;
private $id_usuario;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");		
		$this->load->library("listener");
		$this->load->model("mservice");
    $this->load->model("operaciones/mentregas");
    $this->load->library("cartones");
		$this->visitante=unserialize($this->session->visitante);
		$cond=($this->visitante->get_id_empresa()!="")?"id_empresa=".$this->visitante->get_id_empresa():" 1=1";
		$this->selectPremiosClientes="select id_carton,id_bingo,id_cliente,codigo,strnombrecompleto,nro_docu,documento,sexo,car_tel,telefono,domicilio,calle,nro,piso,dpto,descripcion_loc,descripcion_pro,cp,img_personal,usuario,usuario_mail,img_profile,id_empresa,nro_premio,id_item_bingo,item,empate,DATE_FORMAT(fe_sorteado,'%d-%m-%Y %h:%i:%s') as fe_sorteado,entregado,DATE_FORMAT(fe_entregado,'%d-%m-%Y %h:%i:%s') as fe_entregado,id_mapa,usuario_entrega from verganadores where $cond and nro_docu=? and strnombrecompleto like '?%' and id_carton=? and entregado=? order by strnombrecompleto,nro_docu";
    /*si id_bingo esta definido en el front , es porq busco por codigo QR en selpremios_clientes2, sino por patrones de busqueda manual ingresando patrones usando la definicion selpremios_clientes*/
		$this->definiciones['selpremios_clientes']=$this->selectPremiosClientes;
    $this->definiciones['selpremios_clientes2']="select id_carton,id_bingo,id_cliente,codigo,strnombrecompleto,nro_docu,documento,sexo,car_tel,telefono,domicilio,calle,nro,piso,dpto,descripcion_loc,descripcion_pro,cp,img_personal,usuario,usuario_mail,img_profile,id_empresa,nro_premio,id_item_bingo,item,empate,DATE_FORMAT(fe_sorteado,'%d-%m-%Y %h:%i:%s') as fe_sorteado,entregado,DATE_FORMAT(fe_entregado,'%d-%m-%Y %h:%i:%s') as fe_entregado,id_mapa,usuario_entrega from verganadores where $cond and id_bingo=? and id_carton=? and nro_premio=? order by strnombrecompleto,nro_docu";
    
    $this->definiciones['selpremios']="select * from verpremios where $cond and id_bingo=? ORDER BY orden,nro_premio";
    $this->definiciones['selparametros']="select * from verpremios_parametros where $cond and id_bingo=? and nro_premio=? ORDER BY numero";
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
		$this->load->view('ventregas',$arrVariales);
		
	}	

	function loginOn(){
		$exito=true;
		
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

		
	function listener(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_entregas',1))
		{
			$this->load->view('vlogin');
			return; 
		}
    $json=json_decode($this->input->post("data"),true);    
    $definicion=$this->input->post("definicion");
    if($definicion=="selpremios_clientes"){
      $params=array();
    	$nro_docu=$json['nro_docu'];
      $apenom=$json['apenom'];
    	$id_carton=$json['nro_carton'];    	
    	
    	
      $selectPremiosClientes=$this->selectPremiosClientes;
     
    	if($nro_docu==""){
    	 $selectPremiosClientes=str_replace("and nro_docu=?", "", $selectPremiosClientes);
    	}else{
        $params['nro_docu']=$nro_docu;
      }
     
    	if($apenom==""){
    	 $selectPremiosClientes=str_replace("and strnombrecompleto like '?%'", "", $selectPremiosClientes);
    	}else{
       // $params['strnombrecompleto']=$apenom;
         $selectPremiosClientes=str_replace("and strnombrecompleto like '?%'","and strnombrecompleto like '%$apenom%'", $selectPremiosClientes);
      }
      if($id_carton==""){        
        $selectPremiosClientes=str_replace("and id_carton=?", "", $selectPremiosClientes);
      }else{
        $params['id_carton']=(int)$id_carton;
      }
      if($json['sin_entregar']==1){
      $params['entregado']=0;  
      }else{
        $selectPremiosClientes=str_replace("and entregado=?", "", $selectPremiosClientes);
      }
    	
    	$json=$params;
    	
			$this->definiciones[$definicion]=$selectPremiosClientes;
    
    }//if si la definicion es selecbingos
    $this->definiciones[$definicion];
    $this->listener->get($this->definiciones[$definicion],$json); 
  }

  function guardar(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_entregas',1))
		{
			$this->fwxml->numError=0;
      $this->fwxml->descError="sin permisos";
      $this->fwxml->ResponseJson();
			return; 
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');		
		
		
    
		
		$campos=json_decode(file_get_contents('php://input'), true);
    $metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
    
    if($metodo=="PUT")
    {
    $entregado=($campos['entregado']==1)?1:0;
    $fecha=date("Y-m-d H:i:s");
    if(($entregado==1 && $this->visitante->permitir('prt_entregas',2))
      ||($entregado==0 && $this->visitante->permitir('prt_entregas',3))){
      $this->mentregas->marcar_entrega($entregado,$this->visitante->get_id(),$campos['id_carton'],$campos['nro_premio']);
    
    $this->fwxml->numError=$this->mentregas->numError();
    $this->fwxml->descError=$this->mentregas->descError();
    }

    if($entregado==1 && !$this->visitante->permitir('prt_entregas',2)){
     $this->fwxml->descError="no tiene permisos para otorgar (entregar) premios";
    }
    if($entregado==0 && !$this->visitante->permitir('prt_entregas',4)){
     $this->fwxml->descError="no tiene permisos para deshacer entregas";
    }
   
   
    }

    
    if($this->mservice->numError()!=0)
    {
    $this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
		$this->fwxml->ResponseJson();
	}


  function create_img($base64_string){
    $directorio =  realpath(__DIR__ . '/../../../')."/files/premios/"; // directorio
    if (!is_dir($directorio)) {
      mkdir($directorio);
    }
    $path_img="";
    try {
    $data = explode(',', $base64_string);
    $extension ="jpg";
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
    $file = fopen($output_file, "wb");
    fwrite($file, base64_decode($imgdata));
    fclose($file);
    $minFoto = 'thumbs_'.$imgtmpname;
    $resFoto = 'img_'.$imgtmpname;
    resizeImagen($directorio.'/', $imgtmpname, 65, 65,$minFoto,$extension);
    resizeImagen($directorio.'/', $imgtmpname,700,700,$resFoto,$extension);
    unlink($directorio.'/'.$imgtmpname);
    $path_img="files/premios/".$resFoto;
    } catch (Exception $e) {
      
    }
    return $path_img;
  }
	

}//class
