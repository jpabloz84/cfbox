<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mventas extends CI_model{
private $id;
private $numError=0;
private $descError="";
private $CI=null;
	function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance(); 		  
  	$this->CI->load->model('seguridad/mtoken');
	}
	//crea el comprobante AFIP complementario que lo anula
	function crear_companula($id_comp,$id_usuario,$id_sucursal,&$id_comp_new)
	{
		$this->load->database();		
		$this->numError=0;
	  $this->descError="";
	  $exito=false;
	  $comp=array();
	  try {
	  			$stmt="select * from vercomprobantes where id_comp=$id_comp";
			  	$query=$this->db->query($stmt);			
			  	$error=$this->db->error();
		      if (isset($error['code']) && $error['code']>0) {            	
		      	$this->numError=$error["code"];
		        $this->descError=$error['message'];	
		        return $exito;
		      }else
		      {            
		          $cmp=$query->result_array();
		          if(!count($cmp)>0)
		          {
		          			$this->numError=1;
		        				$this->descError="no hay comprobante";	
		        				return $exito;
		          }
		          $comp=$cmp[0];
		          $id_tipo_anula=0;
		          $id_tipo_comp=$comp['id_tipo_comp'];							
							$stmt="select * from tipo_comprobante where id_tipo_comp=$id_tipo_comp";
			  			$query=$this->db->query($stmt);			
			  			$tipocmpanula=$query->result_array();
							if(count($tipocmpanula)>0)
							{
								$id_tipo_anula=(int)$tipocmpanula[0]['id_tipo_anula'];
							}
							if(!$id_tipo_anula>0)
							{
							$this->numError=2;
				    	$this->descError="No hay comprobante afip para anular";		
				    	return $exito;
							}
							$stmt="select * from vertalonarios where habilitado=1 and id_tipo_comp=$id_tipo_anula and id_sucursal=".$id_sucursal;
			  			$query=$this->db->query($stmt);			
			  			$talonarios=$query->result_array();							
							if(!count($talonarios)>0)
					 		{
					 		$this->numError=3;
				    	$this->descError="No se pudo encontrar un talonario para este comprobante";		
				    	return $exito;
					 		}
					 		$id_talonario=$talonarios[0]['id_talonario'];

							$stmt="INSERT INTO comp(id_talonario,id_cliente,importe_total,importe_neto,importe_base,importe_iva,importe_exento,importe_descuento,fe_creacion,cuit,estado,fe_estado,id_usuario,id_usuario_estado,afip,id_sucursal,id_cliente_representante) SELECT $id_talonario as id_talonario,id_cliente,importe_total,importe_neto,importe_base,importe_iva,importe_exento,importe_descuento,NOW() AS fe_creacion,cuit,'E' AS estado,NOW() AS fe_estado,$id_usuario,$id_usuario,1 as afip,$id_sucursal as id_sucursal,id_cliente_representante FROM comp WHERE id_comp=$id_comp";
				  		$query=$this->db->query($stmt);			
				  		$error=$this->db->error();
				      if (isset($error['code']) && $error['code']>0) {
				      	$this->numError=$error["code"];
				        $this->descError=$error['message'];	
				        return $exito;
				      }
				      $id_comp_new=$this->db->insert_id();
				      $stmt="INSERT INTO comp_det (id_comp,nro_item,id_tipo,nro_tipo,precio_base,precio_iva,precio_venta,importe_item,detalle,cantidad,importe_descuento,iva) SELECT $id_comp_new,nro_item,id_tipo,nro_tipo,precio_base,precio_iva,precio_venta,importe_item,detalle,cantidad,importe_descuento,iva FROM comp_det WHERE id_comp=$id_comp";
				      $query=$this->db->query($stmt);			
				  		$error=$this->db->error();
				      if (isset($error['code']) && $error['code']>0) {
				      	$this->numError=$error["code"];
				        $this->descError=$error['message'];	
				        return $exito;
				      }
		          $exito=true;
		  		}
	  }catch (Exception $e) {
	  	$this->numError=-1;
	  	$this->descError=$e->getMessage();
	  }
	  return $exito;
	}

	function devolver_stock($id_comp)
	{ $this->load->database();		
		$this->numError=0;
	  $this->descError="";
	  $exito=false;
	  $comp_det=array();
	  	//los tipos que devuelven stock, son los articulos
		try {
					$stmt="SELECT p.nro_tipo,det.cantidad,det.id_tipo FROM comp_det det 
								JOIN verproductos p ON det.`nro_tipo`=p.`nro_tipo` AND det.`id_tipo`=p.`id_tipo`
								WHERE    det.id_comp=$id_comp and p.mueve_stock=1";
			  	$query=$this->db->query($stmt);			
			  	$error=$this->db->error();
		      if (isset($error['code']) && $error['code']>0) {            	
		      	$this->numError=$error["code"];
		        $this->descError=$error['message'];	
		      }else
		      {            
		          $comp_det=$query->result_array();
		          foreach ($comp_det as $det) {
		          	$q = $this->db->get_where('tipo_item', array('id_tipo' => $det['id_tipo']));	
								$tipo_item=($q->result_array())[0];
								$tabla_item=$tipo_item['tabla']; //tabla que contiene el elemento stockeable
								$pk_tabla_item=$tipo_item['pk']; //primary key de la tabla
								$nombre_item=$tipo_item['tipo'];

		          	//print_r($det);
		          	$nro_tipo=$det['nro_tipo'];
		          	$cantidad=$det['cantidad'];
		          	$stmt="update ".$tabla_item." set stock=stock + ".$cantidad." where ".$pk_tabla_item."=".$nro_tipo;
			  				$query=$this->db->query($stmt);			
			  				$error=$this->db->error();
			  				if (isset($error['code']) && $error['code']!=0) {            	
		      					$this->numError=$error["code"];
		        				$this->descError=$error['message'];	
		        				return $exito;
		      				}
		          }
		          $exito=true;
		      }

		      
			}catch(Exception $e){
			$this->numError=1;
	  	$this->descError=$e->getMessage();
		}
		return $exito;
			
	 }//devolver stock

function imputar_pagos_comp($id_comp,$id_sucursal,$id_usuario,$id_caja_opera,&$pagos){
	 	date_default_timezone_set('America/Argentina/Buenos_Aires');		
 		$this->numError=0;
  	$this->descError="";
  	$exito=false;
  	$id_cliente=0;
  	$id_empresa=0;
  	$importe_total=0;
  	$comp=array();
  	$tipospagosRs=array();
  	$pagosimpropios=array(); //acomulo los id_comp_pago de los pagos que no sean propios (porque despues necesito eliminar las imputaciones que quedaron de mas y no son propias)
  	$bitsaldo=false;
		try{

			/*obtengo la empresa de la sucursal*/

		  $q=$this->db->get_where("versucursales",array('id_sucursal'=> $id_sucursal));
		  $r=$q->result_array();		  
		  $sucursal=$r[0];
		  $id_empresa=$sucursal['id_empresa'];
		 		//Consulto los datos del comprobante
	 		$stmt="select * from comp where id_comp=$id_comp";
		  $query=$this->db->query($stmt);			
		  $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {            	
      	$this->numError=$error["code"];
        $this->descError=$error['message'];	
        return $exito;
      }
      $a=$query->result_array();
      if(!count($a)>0){
      $this->numError=-20;
        $this->descError="comprobante no existente";	
        return $exito;	
      }
      $comp=$a[0];
      $id_cliente=$comp['id_cliente'];
      $importe_total=(float)$comp['importe_total'];
	 		//elimino imputaciones hechas a este comprobante pero solo aquellas que sean propias
	 		$stmt="delete from comp_pagos where id_comp=$id_comp and id_pago in(select id_pago from pagos where id_comp_ref=$id_comp)";
		  	$query=$this->db->query($stmt);			
		  	$error=$this->db->error();
	      if (isset($error['code']) && $error['code']>0) {            	
	      	$this->numError=$error["code"];
	        $this->descError=$error['message'];	
	        return $exito;
	      }

	     //elimino los parametros delos pagos realizados por este comprobante
	 		$stmt="delete from pagos_parametros where id_pago in(select id_pago from pagos where id_comp_ref=$id_comp)";
	  	$query=$this->db->query($stmt);			
	  	$error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {            	
      	$this->numError=$error["code"];
        $this->descError=$error['message'];	
        return $exito;
      }

		      //elimino los pagos realizados por este comprobante
		 	$stmt="delete from pagos where id_comp_ref=$id_comp";
	  	$query=$this->db->query($stmt);			
	  	$error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {            	
      	$this->numError=$error["code"];
        $this->descError=$error['message'];	
        return $exito;
      }
      		      
	     $key_pago_saldo=0; //id del pago que se ingresa con saldo, luego se usa para completar el pago con comp_pagos
		      //inserto los pagos	
		      foreach ($pagos as $key => $pagoitem){
		      	$id_pago=0;		      	
		      	$espropio=true;
		      	$id_comp_pago=0;
		      	$arrcompagos=array();
		      	$pago=array();
		      	$monto_abona=(float)$pagoitem['monto_abona'];
						if($monto_abona<=0){
							//paso al siguiente pago si no es mayor a cero
							continue;
						}
						
						if(isset($pagoitem['propio'])){							
							$espropio=($pagoitem['propio']==1)?true:false;
						}
						if(isset($pagoitem['id_pago'])){
							if((int)$pagoitem['id_pago']>0){
							$id_pago=(int)$pagoitem['id_pago'];
							$pago['id_pago']=$id_pago;
							}
						}

						if(isset($pagoitem['id_pago'])){
							if((int)$pagoitem['id_pago']>0){
							$id_pago=(int)$pagoitem['id_pago'];
							$pago['id_pago']=$id_pago;
							}
						}
						
						//en el caso de que el array de pagos imputados existe, lo seteo						
						if(isset($pagoitem['id_comp_pago'])){
							if(count($pagoitem['id_comp_pago'])>0){
							$arrcompagos=$pagoitem['id_comp_pago'];							
							}
						}
						$pago['pago']=$monto_abona;
						$pago['id_tipo_pago']=$pagoitem['id_tipo_pago'];						
						$pago['fe_pago']=date('Y-m-d H:i:s');
						$pago['fe_estado']=date('Y-m-d H:i:s');
						$pago['id_cliente']=$id_cliente;
						$pago['observacion']=$pagoitem['observacion'];						
						$pago['id_usuario']=$id_usuario;
						$pago['id_usuario_estado']=$id_usuario;
						$pago['id_caja_op']=$id_caja_opera;
						$pago['id_comp_ref']=$id_comp;
						$pago['id_sucursal']=$id_sucursal;
						$estado_inicial='P';
						$rsTipos=$this->db->query("select * from tipo_pagos where habilitado=1 and id_tipo_pago=".$pagoitem['id_tipo_pago']);
						$tipospagosRs=$rsTipos->result_array();
						if(count($tipospagosRs)>0){
							$estado_inicial=$tipospagosRs[0]['estado_inicial'];
						}
						$pago['estado']=$estado_inicial;
						$pagoitem['estado']=$estado_inicial;
						
						if($espropio){
						/*echo "pago 1 <br/>";
						print_r($pago);*/
							$this->db->set($pago);
            	$this->db->insert("pagos");
            	$error=$this->db->error();
	            if (isset($error['code']) && $error['code']!=0){
		            $this->numError=$error["code"];
		            $this->descError='Error al insertar pago:'.$error['message'];
		            return $exito;
	            }	
						}						

            //solo en caso de que sea una insercion nueva, hago el insert_id, si id_pago es mayor a cero es porque se trata de una actualizacion
            if(!$id_pago>0){
            $id_pago=$this->db->insert_id();
            $this->CI->mtoken->rollbackonerror=false;
            $parametros=array('id_empresa' =>$id_empresa ,'id_pago'=>$id_pago);
            $token=$this->CI->mtoken->generar_token(1,$id_usuario,$parametros);	
            $this->numError=$this->CI->mtoken->numError();
            $this->descError=$this->CI->mtoken->descError();
            $this->db->update("pagos",array('token'=>$token),array('id_pago'=>$id_pago)); 
            }
            
            $pagos[$key]['id_pago']=$id_pago;
            $qp = $this->db->get_where('tipo_pagos_parametros', array('id_tipo_pago' => $pagoitem['id_tipo_pago']));

		      	$artpparams=$qp->result_array();
						if(count($artpparams)>0 && $espropio)
						{	
							$parametros=array();
							foreach ($pagoitem['tipospagos'] as $opcion){
								if($opcion['id_tipo_pago']==$pagoitem['id_tipo_pago'])
								{
								$params=$opcion['parametros'];
									break;
								}
							}							
							foreach ($params as $paramitem) {
								$parametros['id_pago']=$id_pago;
								$parametros['nro_parametro']=$paramitem['nro_parametro'];
								$parametros['valor']=$paramitem['valor'];
								$this->db->set($parametros);
            		$this->db->insert("pagos_parametros");
								$error=$this->db->error();
            		if (isset($error['code']) && $error['code']!=0){
            		$this->numError=$error["code"];
            		$this->descError='Error al insertar parametros:'.$error['message'];
            		return $exito;
            		}
							}
						}//insercion de parametros
						//si el pago es pendiente (por ser cheque, deposito, transferencia u mercado pago)
						if($pagoitem['estado']=='P'){ //se imputan luego
							continue;
						}
						//bit que indica que sí se puede pagar con saldo
						if($pagoitem['id_tipo_pago']==6){
							$key_pago_saldo=$key;
							$bitsaldo=true;
							continue; //salto este item porque imputo al final							
						}
						//para los pagos comunes (que nose sean saldos) tiene sentido esto, pero para otros tipos de pagos, no
						if(count($arrcompagos)>0){
						$id_comp_pago=(int)$arrcompagos[0]; 	
						}
						//acumulo los id_comp_pagos no propios
						if(!$espropio && $id_comp_pago>0)
						{
							$pagosimpropios[]=$id_comp_pago;
						}
						$diff=$importe_total-$monto_abona;
						//si el comprobante es mayor o igual a lo que se abona
						if($diff>=0 && $importe_total>0 && $espropio)
						{
							$comp_pago=array();
							if($id_comp_pago>0){
							$comp_pago['id_comp_pago']=$id_comp_pago;	
							}
							$comp_pago['id_comp']=$id_comp;
							$comp_pago['id_pago']=$id_pago;
							$comp_pago['monto']=$monto_abona;
							$comp_pago['fecha']=date('Y-m-d H:i:s');
							$comp_pago['id_usuario']=$id_usuario;							
							$this->db->set($comp_pago);
            	$this->db->insert("comp_pagos");
            	
           
								$error=$this->db->error();
            		if (isset($error['code']) && $error['code']!=0){
            		$this->numError=$error["code"];
            		$this->descError='Error al insertar comp_pagos:'.$error['message'];
            		return $exito;
            	}
            	if(!$id_comp_pago>0){
            	$id_comp_pago=$this->db->insert_id();
            	$arrcompagos[]=$id_comp_pago;	
            	}            	
							$importe_total=$importe_total-$monto_abona;							
							$pagos[$key]['id_comp_pago']=$arrcompagos;
							//paso al siguiente pago
							continue;
						}

						/*si el importe total del comprobante es menor a lo que se abona 
						(se abona mas de lo q queda por saldar)*/
						if($diff<0 && $importe_total>0 && $espropio)
						{
							$comp_pago=array();
							if($id_comp_pago>0){
							$comp_pago['id_comp_pago']=$id_comp_pago;	
							}
							$comp_pago['id_comp']=$id_comp;
							$comp_pago['id_pago']=$id_pago;
							$comp_pago['monto']=$importe_total;
							$comp_pago['fecha']=date('Y-m-d H:i:s');							
							$comp_pago['id_usuario']=$id_usuario;
							
						/*echo "comppago 2 <br/>";
						print_r($comp_pago);*/
							$this->db->set($comp_pago);
            				$this->db->insert("comp_pagos");
								$error=$this->db->error();
            		if (isset($error['code']) && $error['code']!=0){
            		$this->numError=$error["code"];
            		$this->descError='Error al insertar comp_pagos:'.$error['message'];
            		return $exito;
            	}
            	//solo en el caso de que no hay insertado nunca esta imputacion
            	if(!$id_comp_pago>0){
            	$id_comp_pago=$this->db->insert_id();
            	$arrcompagos[]=$id_comp_pago;	
            	}
            	
							$importe_total=0;
							$pagos[$key]['id_comp_pago']=$arrcompagos;
							//paso al siguiente pago
							continue;
						}
		      
		      }//for
		      //elimino los pagos impropios que esten de mas, los que paso en el array , son los que hay que mantener
		      $ex=$this->conservar_pagos_impropios($id_comp,$pagosimpropios);
		      if(!$ex)
		      {
		      	return $exito;
		      }
		      //si aun queda resto por imputar y uno de los pagos indico que se paga con saldo, imputaremos saldo restante siempre y cuando tenga con que saldar
		      if($importe_total>0 && $bitsaldo){
		      	
		      	 $CI =& get_instance(); 
		      	 $CI->load->model('operaciones/mpagos'); 
		      	 $comppago=array();
		      	 $ex=$CI->mpagos->pagar_comp_saldo($id_comp,$id_cliente,$id_empresa,$comppago,$id_usuario);
		      	 if(!$ex){
		      	 	$this->numError=$CI->mpagos->numError();
		      	 	$this->descError=$CI->mpagos->descError();
		      	 	return $exito;
		      	 }
		      	 $pagos[$key_pago_saldo]['id_comp_pago']=$comppago;
		      }

		      $exito=true;
		 	}catch(Exception $e){
				$this->numError=1;
		  	$this->descError=$e->getMessage();
			}//try
			return $exito;
	 }//imputar_pagos_comp

	function conservar_pagos_impropios($id_comp,$pagosimpropios){
		//elimino los pagos impropios que esten de mas, los que paso en el array , son los que hay que mantener
		$exito=false;
		$strid_comp_pagos="";
		$stmtextra="";
		try {			
			foreach ($pagosimpropios as $id_comp_pago) {
				if($strid_comp_pagos==""){
				$strid_comp_pagos.="$id_comp_pago";
				}else{
					$strid_comp_pagos.=",$id_comp_pago";
				}
			}
			if($strid_comp_pagos!=""){
			$stmtextra.=" and id_comp_pago not in($strid_comp_pagos)";
			}
			$stmt="delete from comp_pagos where id_comp=$id_comp and id_pago not in(select id_pago from pagos where id_comp_ref=$id_comp) ".$stmtextra;
				 
			  	$query=$this->db->query($stmt);			
			  	$error=$this->db->error();
		      if (isset($error['code']) && $error['code']>0) {            	
		      	$this->numError=$error["code"];
		        $this->descError=$error['message'];	
		        return $exito;
		      }
			
			$exito=true;
		} catch (Exception $e) {
			$this->numError=1;
		 $this->descError=$e->getMessage();			
		}
		return $exito;
	}//conservar_pagos_impropios



	function actualizar_stock($id_tipo,$nro_tipo,$solicitado=0,$comp_det_old){
		//$this->load->database();
		$tipos_no_stockeables=array(2,4,5,6,7); //tipo de productos no stockeables
		$errores=array();
		$diff_old=0;
		$cantidad=0;
		$cant_old=0;
		if(in_array($id_tipo, $tipos_no_stockeables) || $solicitado<=0){
			return $errores;
		}
		$cant_old=0;
		if(count($comp_det_old)>0){
			foreach ($comp_det_old as $det) {
				if($det['nro_tipo']==$nro_tipo && $det['id_tipo']==$id_tipo){
					$cant_old+=(int)$det['cantidad'];
				}
			}
		/*$q = $this->db->query("SELECT IFNULL(SUM(cantidad),0) as cant FROM comp_det WHERE id_comp=$id_comp and nro_tipo=$nro_tipo and id_tipo=$id_tipo");
		$cant_old=(float)($q->result_array())[0]['cant'];*/
		}
		$q = $this->db->get_where('tipo_item', array('id_tipo' => $id_tipo));	
		$tipo_item=($q->result_array())[0];
		$tabla_item=$tipo_item['tabla']; //tabla que contiene el elemento stockeable
		$pk_tabla_item=$tipo_item['pk']; //primary key de la tabla
		$nombre_item=$tipo_item['tipo'];
		$q = $this->db->get_where($tabla_item, array($pk_tabla_item => $nro_tipo));	
		$elemento=($q->result_array())[0];
		$stock_actual=(int)$elemento['stock'];
		switch ($id_tipo) {
			case 1: //para articulos
				if($elemento['mueve_stock']!=1)return $errores; //si no mueve stock, que retorne

				if($elemento['habilitado']!=1){
					$errores[]="El elemento ".$elemento['articulo']." no está habilitado para la venta";
				}
				break;
			
			default:
				# code...
				break;
		}//switch

		if(count($comp_det_old)>0){
		$diff_old=$solicitado-$cant_old;	
		}
		
		
		if(count($errores)==0){
			/*el articulo mueve stock y no estuvo en un comprobante anterior pero esta cantidad supera al stock actual,
		o si el articulo mueve stock y lo que se esta llevando, supera a lo que se llevo, esta diferencia positiva 
			hay que compararla con el stock actual*/
			if(($stock_actual<$solicitado && $cant_old==0)||($cant_old>0 && $diff_old>0 && $diff_old>$stock_actual)){
			$errores[]="El ".$nombre_item." ".$elemento['detalle']." no tiene suficientes cantidades disponibles para completar esta venta. El stock actual es: ".$stock_actual." (solicitado ".$solicitado.")";
			}
		}//si no hay errores
		if(count($errores)==0){

			$cantidad=$stock_actual-($solicitado+$diff_old);
			//echo "cantidad: $cantidad, stock actual $stock_actual, solicitado: $solicitado, diff old: $diff_old";
			$stmt="update ".$tabla_item." set stock=".$cantidad." where ".$pk_tabla_item."=".$nro_tipo;
			$query=$this->db->query($stmt);			
			$error=$this->db->error();
			if (isset($error['code']) && $error['code']!=0) {            	
					$this->numError=$error["code"];
  				$this->descError=$error['message'];	  			
				}
		}

		return $errores;
	}

	
	function actualizar_vendedor($id_comp,$id_vendedor_opera){

					$this->db->query("update comp set id_usuario=$id_vendedor_opera where id_comp=$id_comp");
							$error=$this->db->error();
						if (isset($error['code']) && $error['code']!=0) {            	
							$this->numError=$error["code"];
		  				$this->descError=$error['message'];	 
		  				return false;
						}
						return true;

	}


   function numError()
	 {
		return $this->numError;
	 }

	function descError()
	{
		return $this->descError;
	}	  

	//defino las consultas del front
function definiciones($id_empresa=0){
$definiciones=array();
$definiciones['selcliente_presupuesto']="SELECT * FROM verclientes_presupuesto WHERE id_empresa=$id_empresa  {pedido} ";
$definiciones['selcliente']="SELECT * FROM verclientes_representantes WHERE id_empresa=$id_empresa  {cliente} ";
$definiciones['pedido_detalle']="SELECT * FROM vercheckout_detalle WHERE id_empresa=$id_empresa  {pedido} ";
$definiciones['productos']="SELECT * FROM verproductos WHERE id_empresa=$id_empresa  {idproducto}";




return $definiciones;
}


}

?>
