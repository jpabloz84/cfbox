<?php defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "1024M");
//ini_set('display_errors', 1); 
//error_reporting(E_ALL);
class Usuarios extends CI_Controller {
private $visitante;
private $definiciones=array(); //para uso del oyente
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");
		$this->load->library("listener");
		$this->load->library('Images');		
		$this->visitante=unserialize($this->session->visitante);
		$usucond="";
		if($this->visitante->get_id_rol()!=1){
		$usucond=" and id_empresa=".$this->visitante->get_id_empresa();
		}
		
		$this->definiciones['getusuario']="select usuario,clave,id_rol,habilitado,id_persona,apellido,nombres,domicilio,telefono,nro_docu,tipo_docu,sexo,email,id_loc,id_pro,calle,nro,piso,dpto,cp,car_tel,nro_tel,DATE_FORMAT(fe_nacimiento,'%d/%m/%Y') as fe_naci,id_sucursal,ifnull(firma,'') as firma from verusuarios where id_usuario=? ".$usucond;
		$this->definiciones['gethomonimos']="select id_persona,apellido,nombres,domicilio,telefono,id_loc,tipo_docu,nro_docu,sexo,email,id_pro,calle,nro,piso,dpto,cp,car_tel,nro_tel from verpersonas where nro_docu=? and sexo=? and tipo_docu=? ".$usucond;
		$this->definiciones['usuarioexistencia']="select usuario from usuario where usuario='?'";
	}
	 function index()
	{	

		if(!isset($this->visitante) || $this->visitante==null)
		{
		$this->load->view('vlogin');
		return;
		}

		if(!$this->visitante->logueado())
		{
		$this->load->view('vlogin');
		return;
		}
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		//$arrVariales['return_url']="seguridad/usuarios/";
		$this->load->view('seguridad/vusuarios',$arrVariales);
		
	}	

	function loginOn(){
		$exito=true;
		$visitante=unserialize($this->session->visitante);
		if(!isset($this->visitante) || $this->visitante==null)
		{		
		$exito=false;
		}

		if(!$this->visitante->logueado())
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

	 	if(!$this->loginOn() && !$this->visitante->permitir('prt_usuarios',1))
		{
			//$this->load->view('vlogin');
			$this->fwxml->ResponseJson();
			return; 
		}
 	$json=json_decode($this->input->post("data"),true);    
    $definicion=$this->input->post("definicion");
    
    $this->listener->get($this->definiciones[$definicion],$json);
	}//listener
	

	function ingresar_alta()
	{  

		if(!$this->loginOn() && !$this->visitante->permitir('prt_usuarios',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		

	   $this->load->model("mlocalidades");
	   $this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['roles']=$this->mpermisos->allRoles($this->visitante->get_id_rol());
		$arrVariales['provincias']=$this->mlocalidades->getProvincias();
		$arrVariales['documentos']=$this->mservice->getDocumentos();
		$arrVariales['sucursales']=$this->mpermisos->sucursales($this->visitante->get_id());
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']='A';
		$arrVariales['id_persona']=0;
		$arrVariales['id_usuario']=0;
		$arrVariales['callback']="";		

		
		$this->load->view('seguridad/vusuario_datos',$arrVariales);
	}
	
	

	function modificar($id_usuario)
	{  

		if(!$this->loginOn() && !$this->visitante->permitir('prt_usuarios',4))
		{
			return;
		}
	   $this->load->model("mlocalidades");
	   $this->load->model("mservice");
		$arrVariales=array();
		$id_empresa=$this->visitante->get_id_empresa();
		$id_rol=$this->visitante->get_id_rol();
		$where="id_usuario=$id_usuario";
		//si no es administrador de sistema, (administrador de otra indole u otro usuario con permisos no de sistema, ve lo de su empresa)
		if($id_rol!=1){
			$where.=" and id_empresa=".$id_empresa;
		}
		$rs=$this->mservice->getwhere("verusuarios",$where);
		if(count($rs)>0){
		$arrVariales['roles']=$this->mpermisos->allRoles($this->visitante->get_id_rol());
		$arrVariales['provincias']=$this->mlocalidades->getProvincias();
		$arrVariales['documentos']=$this->mservice->getDocumentos();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']='M';
		$arrVariales['id_persona']=0;
		$arrVariales['sucursales']=$this->mpermisos->sucursales($this->visitante->get_id());
		$arrVariales['id_usuario']=$id_usuario;		
		$arrVariales['callback']="index.php/seguridad/usuarios/";		
		$this->load->view('seguridad/vusuario_datos',$arrVariales);
		}

		
	}




	function obtener_usuarios()
	{$arrRow=array();
	 	if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}

  		$numrows=0;
  		$Rows=$this->musuarios->obtenerUsuarios($numrows,$this->visitante->get_id_rol());
        
        foreach ($Rows as $row) {
        	$id_usuario=$row['id_usuario'];
        	$arrCol['NOMBRES']=$row['apellido'].", ".$row['nombres']; 
            $arrCol['ROL']=$row['rol']; 
            $arrCol['USUARIO']=$row['usuario'];
            $descripcion="( ".$row['usuario']." ) ".$arrCol['NOMBRES'];
            $img_personal=$row['img_personal'];
            $arrCol['VER']="<input type='radio' value='$id_usuario' class='form-control' onclick='setdetails($id_usuario)' name='seleccion' /><input type='hidden' id='desc_$id_usuario'  value='$descripcion' /> <input type='hidden' id='img_$id_usuario'  value='$img_personal' />";
            $arrRow[]=$arrCol;
        }
         
          $arrJson=array('draw' =>1 , 'recordsTotal'=>$numrows,'recordsFiltered'=>$numrows,'data'=>$arrRow);
		echo json_encode($arrJson, JSON_HEX_QUOT);
		
	}
	

function save()
{	$exito=false;
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->load->model("mpersona");
	$numError=0;
	$descError='';
	if($this->loginOn())
	 {
	 	$id_persona=$this->input->post('id_persona');
		$modo=$this->input->post('modo');
		$usuario=$this->input->post('usuario');
		$habilitado=($this->input->post('habilitado')=='on')?1:0;
		$clave=$this->input->post('clave');
		$id_rol=$this->input->post('rol');
		$id_usuario=$this->input->post('id_usuario');
		$apellido=$this->input->post('apellido');
		$nombres=$this->input->post('nombre');

		$calle=$this->input->post('calle');
		$nro=$this->input->post('nro');
		$piso=$this->input->post('piso');
		$dpto=$this->input->post('dpto');
		$cp=$this->input->post('cp');
		$car_tel=$this->input->post('car_tel');
		$nro_tel=$this->input->post('nro_tel');
		$fe_naci=$this->input->post('fe_naci');
		$tipo_docu=$this->input->post('tipo_docu');
		$nro_docu=$this->input->post('nro_docu');
		$id_loc=$this->input->post('localidad');
		$provincia=$this->input->post('provincia');
		$id_sucursal=$this->input->post('id_sucursal');
		$sexo=$this->input->post('sexoRadios');
		$email=$this->input->post('email');
		$firma=$this->input->post('img_firma');
		$path_img="";
		$numError=0;
		$descError="";

		//forzar 
		$forzarguardadopersona=false;
	    if($id_rol==1){
	    	$forzarguardadopersona=true;
	    }
		try {
			if($firma!=""){
				 $firma=$this->images->resampleimg($firma);
	            /*if($this->images->numError()==0)
	            {
	            $firma $this->images->descError();
	            }*/
			}
		 

		    if($_FILES['img_profile']['name'] != ""){ // El campo foto contiene una imagen...        
		            // Primero, hay que validar que se trata de un JPG/GIF/PNG
		            $allowedExts = array("jpg", "jpeg", "gif", "png", "JPG", "GIF", "PNG");
		            $tmp=explode(".", $_FILES["img_profile"]["name"]);
		            $extension = end($tmp);
		            if ((($_FILES["img_profile"]["type"] == "image/gif")
		                    || ($_FILES["img_profile"]["type"] == "image/jpeg")
		                    || ($_FILES["img_profile"]["type"] == "image/png")
		                    || ($_FILES["img_profile"]["type"] == "image/pjpeg"))
		                    && in_array($extension, $allowedExts)) {
		                // el archivo es un JPG/GIF/PNG, entonces...   
		                $tmp=explode('.', $_FILES['img_profile']['name']);
		                $extension = end($tmp);
		                $foto = substr(md5(uniqid(rand())),0,10).".".$extension;
		                $directorio =  realpath(__DIR__ . '/../../../')."/files/profiles"; // directorio de tu elección	                
		                // almacenar imagen en el servidor
		                if(move_uploaded_file($_FILES['img_profile']['tmp_name'], $directorio.'/'.$foto))
		                {	$minFoto = 'thumbs_'.$foto;
		                	$resFoto = 'img_'.$foto;

		                	$ex1=resizeImagen($directorio.'/', $foto, 80,80,$minFoto,$extension);
		                	$ex2=resizeImagen($directorio.'/', $foto,700,700,$resFoto,$extension);
		                	unlink($directorio.'/'.$foto);
		                	if($ex1 && $ex2){
		                	$path_img="files/profiles/".$resFoto;	
		                	}
		               		
		             		}
		                
		            }
		        }//campo imagen
		  $data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc, 'img_profile'=>$path_img,'email'=>$email,'car_tel'=>$car_tel,'nro_tel'=>$nro_tel,'calle'=>$calle,'nro'=>$nro,'piso'=>$piso,'dpto'=>$dpto,'cp'=>$cp,'fe_nacimiento'=>$fe_naci);	  

			if($id_persona>0)
			{
				
						
				if(!$this->mpersona->actualizar($id_persona,$data,$forzarguardadopersona))
				{
				$numError=1;
				$descError='Error al actualizar persona: '.$this->mpersona->descError()."\n";
				}
			}else
			{
				$id_persona=$this->mpersona->agrega($data,$forzarguardadopersona);

				if($this->mpersona->numError()!=0)
				{
				$numError=3;
				$descError='Error al insertar persona: '.$this->mpersona->descError()."\n";
				}

			}

		}catch (Exception $e) {
		$numError=2;
		$descError='Excepción capturada: '.$e->getMessage()."\n";
		}//try
		if($numError==0 && $modo=='A')
		{$id_usuario=0;
			$id_usuario=$this->musuarios->agregar($usuario,$clave,$id_rol,$id_persona,$habilitado,$id_sucursal,$firma);
			if($this->musuarios->numError()!=0)
				{
				$numError=4;
				$descError='Error al insertar usuario: '.$this->musuarios->descError()."\n";
				}
		}
		if($numError==0 && $modo=='M')
		{ $campos=array('usuario' => $usuario,'clave' => $clave,'id_rol' => $id_rol,'habilitado' => $habilitado,'id_persona' => $id_persona);
		if($id_sucursal!="")
			{
				$campos['id_sucursal']=$id_sucursal;
			}else{
				$campos['id_sucursal']=null;
			}
			if($path_img!="")
			{
				$campos['img_profile']=$path_img;
			}
			if($firma!="")
			{
				$campos['firma']=$firma;
			}
			if(!$this->musuarios->actualizar($id_usuario,$campos))
			{
				$numError=5;
				$descError='Error al modificar usuario: '.$this->musuarios->descError()."\n";
			}
			if($this->musuarios->numError()!=0)
				{
				$numError=5;
				$descError='Error al modificar usuario: '.$this->musuarios->descError()."\n";
				}
		}
	 
	 }
	 else
	 {
	 	$numError=0;
		$descError='No inicio sesión';
	 }

	$this->fwxml->numError=$numError;
	$this->fwxml->descError=$descError;
	$this->fwxml->arrResponse=array('id_usuario' =>$id_usuario);
	$this->fwxml->ResponseJson();
 }//save


function eliminar(){
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$id_usuario=$this->input->post("id_usuario");
	if(!$this->loginOn() || !$this->visitante->permitir('prt_usuarios',8))
	 {
	 	$this->fwxml->numError=1;
		$this->fwxml->descError="no tiene permisos para realizar esta accion";
	 	$this->fwxml->ResponseJson();
	 	return;
	 }

	try {
		$exito=$this->musuarios->eliminar($id_usuario);
		if(!$exito){
				$this->fwxml->numError=$this->musuarios->numError();
				$this->fwxml->descError=$this->musuarios->descError();
		}
	} catch (Exception $e) {
	$this->fwxml->numError=1;
	$this->fwxml->descError=$e->getMessage();		
	}
	
	$this->fwxml->ResponseJson();
}

function create_img($base64_string){
    $directorio =  realpath(__DIR__ . '/../../../')."/files/firmas/"; // directorio
    if (!is_dir($directorio)) {
      mkdir($directorio);
    }
    $path_img="";
    try {
    $data = explode(',', $base64_string);
    $extension ="png";
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
    $resFoto = 'img_'.$imgtmpname;
    
    resizeImagen($directorio.'/', $imgtmpname,200,200,$resFoto,$extension);
    unlink($directorio.'/'.$imgtmpname);
    $path_img="files/firmas/".$resFoto;
    } catch (Exception $e) {
      
    }
    return $path_img;
  }

  function resampleimg($base64_string){

      $directorio =  realpath(__DIR__ . '/../../../')."/files/tmp/"; // directorio
    if (!is_dir($directorio)) {
      mkdir($directorio);
    }
    $path_img="";
    try {
    $data = explode(',', $base64_string);
    $extension ="png";
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
    $png=imagecreatefrompng($output_file);	
    imagealphablending($png, false);
	imagesavealpha($png, true);
	imagepng($png);
	imagedestroy($png);
   }catch (Exception $e) {
      
    }
    }

}//class
