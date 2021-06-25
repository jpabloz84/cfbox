<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos extends CI_Controller {
  private $visitante;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
	}
	 function index()
	{	$arrVariales=array();
    $exito=true;
    $this->visitante=unserialize($this->session->visitante);
     if(!$this->loginOn() || !$this->visitante->permitir('prt_permisos',1))
    {

      $this->load->view('vlogin');
      return; 
    }

		$arrVariales['roles']=$this->mpermisos->allRoles($this->visitante->get_id_rol());
		$this->load->view('seguridad/vpermisos',$arrVariales);
		
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

	function getpermisos($id_rol)
	{	$this->load->library('rowresult');				
		$this->fwxml->numError=0;
		$this->fwxml->descError="";				
    $this->visitante=unserialize($this->session->visitante);
     if(!$this->loginOn() || !$this->visitante->permitir('prt_permisos',1))
    {

      $this->load->view('vlogin');
      return; 
    }
	  	try {
	  	$arrPermisos=$this->mpermisos->getpermisos($id_rol);
			$xmlResponse=$this->rowresult->getxmldata($arrPermisos); 		
			$this->fwxml->xmlResponse=$xmlResponse;
	  	 } catch (Exception $e) {
	  	 	$this->fwxml->numError=1;
			$this->fwxml->descError=$e->getMessage();
	  	 }   		
  		$this->fwxml->Response();
	}

	function obtener_datatable($id_usuario)
	{
    $this->visitante=unserialize($this->session->visitante);
     if(!$this->loginOn() || !$this->visitante->permitir('prt_permisos',1))
    {

      $this->load->view('vlogin');
      return; 
    }
    $arrRow=array();	 
  		$numrows=0;
  		$Rows=$this->cuentas_m->obtenerCuentasUser($id_usuario,$numrows);
        
        foreach ($Rows as $row) {
        	$id_cuenta=$row['id_cuenta'];
        	$arrCol['NOMBRE']=$row['nombre']; 
            $arrCol['WEB']=$row['url']; 
            $arrCol['USUARIO']=$row['usuario'];
            $observaciones=$row['observaciones'];
            $arrCol['VER']="<input type='radio' value='$id_cuenta' class='form-control' onclick='setdetails($id_cuenta)' name='seleccion' /><input type='hidden' id='desc_$id_cuenta'  value='$id_cuenta' /> <input type='hidden' id='obs_$id_cuenta'  value='$observaciones' />";
            $arrRow[]=$arrCol;
        }
         
          $arrJson=array('draw' =>1 , 'recordsTotal'=>$numrows,'recordsFiltered'=>$numrows,'data'=>$arrRow);
		echo json_encode($arrJson, JSON_HEX_QUOT);
		die();
	}

	function obtener($id_usuario)
	{$arrRow=array();
    $this->visitante=unserialize($this->session->visitante);
     if(!$this->loginOn() || !$this->visitante->permitir('prt_permisos',1))
    {

      $this->load->view('vlogin');
      return; 
    }
	 $this->fwxml->numError=0;
		$this->fwxml->descError="";
		$this->fwxml->xmlResponse="";
  		$numrows=0;
  		$Rows=$this->cuentas_m->obtenerCuentasUser($id_usuario,$numrows);
  		$this->fwxml->arrResponse=$Rows;
  		$this->fwxml->ResponseJson();
	}

	function save()
	{	
    $this->visitante=unserialize($this->session->visitante);
     if(!$this->loginOn() || !$this->visitante->permitir('prt_permisos',3))
    {
      $this->load->view('vlogin');
      return; 
    }

    $exito=false;
		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$numError=0;
    $descError='';
		$this->fwxml->xmlResponse="";
		$xmldata=$this->input->post('strxml');
		$strxml=loadXML($xmldata);
		$res=$strxml->resultados;
		foreach ($res->rol as $rol)
        {
            $id_rol=$rol['id_rol'];
            foreach ($rol->permiso_modulo as $permiso_modulo) 
            {
                 $suma_logica=$permiso_modulo['suma'];
                 $nro_permiso_modulo=$permiso_modulo['nro_permiso_modulo'];
                 $exito=$this->mpermisos->rolmodulo_update($id_rol,$nro_permiso_modulo,$suma_logica);               
               if($exito)
               {
               		$exito=$this->mpermisos->rolpermiso_clear($nro_permiso_modulo);               
                    
                    if($exito)
                    {
                        foreach ($permiso_modulo->permiso as $permiso) 
                        {
                            if($exito)
                            {
                                $nro_permiso=$permiso['nro_permiso'];
                                $permiso_desc=$permiso;
                                $exito=$this->mpermisos->rolpermiso_add($nro_permiso,$nro_permiso_modulo,$permiso_desc);              
                            	if(!$exito)
                            	{
                            	$numError=3;
                                $descError='error al insertar permiso';
                               break;
                            	}
                            
                               
                            }
                        
                        }    
                    }
                    else
                    {
                        $numError=2;
                        $descError='error rolpermiso_clear';
                        break;
                    }
                
               }else
               {

                        $numError=1;
                        $descError='error rolmodulo_update';
                        break;
               }                
            
         	}
     }

     $this->fwxml->numError=$numError;
		$this->fwxml->descError=$descError;
		$this->fwxml->Response();
}//save

}//class
