
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
		
		<!-- begin #sidebar -->
		<div id="sidebar" class="sidebar">
			<!-- begin sidebar scrollbar -->
			<div data-scrollbar="true" data-height="100%">
				<!-- begin sidebar user -->				
				<!-- end sidebar user -->
				<!-- begin sidebar nav -->
				<ul class="nav">
					<li class="nav-header">ADMINISTRACION</li>										
					<?php if($visitante->permitir('prt_pedidos',1) ){ ?>
					<li><a href="#<?php echo base_url(); ?>index.php/operaciones/pedidos/" data-toggle="ajax"><i class="fa fa-pencil-square"></i> <span>PEDIDOS</span></a></li>
					<?php } ?>	
					<?php if($visitante->permitir('prt_operaciones',2) ){ ?>
					<li><a href="#<?php echo base_url(); ?>index.php/operaciones/consultas/" data-toggle="ajax"><i class="fa fa fa-edit"></i> <span>OPERAR</span></a></li>
					<?php } ?>	
					<?php if($visitante->permitir('prt_configuracion',128)){ ?>
						<li><a href="#<?php echo base_url(); ?>index.php/fichador/" data-toggle="ajax"><i class="fa fa fa-laptop"></i> <span>FICHADOR</span></a></li>
					<?php } ?>
					<?php if($visitante->permitir('prt_clientes',1)){ ?>
					<li class="has-sub">
						<a href="javascript:;">
							<span class="badge pull-right">2</span>
							<i class="fa fa-users"></i> 
							<span>CLIENTES</span>
						</a>
						<ul class="sub-menu">
							
						    <li><a href="#<?php echo base_url(); ?>index.php/entidades/clientes/" data-toggle="ajax">Consultar</a></li>
						     <?php if($visitante->permitir('prt_clientes',2)){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/entidades/clientes/ingresar_alta" data-toggle="ajax">alta</a></li>
						    <?php } ?>					    				
						</ul>
					</li>
					<?php } ?>
					<?php if($visitante->permitir('prt_articulos',1)){ ?>					
						<li class="has-sub">
						<a href="javascript:;">
							<?php
							$cant=0;
							$modulo_articulos=$visitante->permitir('prt_articulos',1);
							if($modulo_articulos)$cant++;
							$modulo_servicios=$visitante->permitir('prt_servicios',1);
							if($modulo_servicios)$cant++;
							
							?>
							<span class="badge pull-right"><?php echo $cant ?></span>
							<i class="fa fa-credit-card"></i> 
							<span>PRODUCTOS</span>
						</a>
						<ul class="sub-menu">							
							<?php if($modulo_articulos){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/entidades/articulos/" data-toggle="ajax">Articulos</a></li>
						    <?php } ?>	
						    <?php if($modulo_servicios){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/entidades/servicios/" data-toggle="ajax">Servicios</a></li>
						    <?php } ?>		
						    				    
						   											    
						</ul>
					</li>					
					<?php } ?>										
					
																    
					<?php if($visitante->permitir('prt_caja',1)){ ?>
				<li><a href="#<?php echo base_url(); ?>index.php/operaciones/caja/" data-toggle="ajax"><i class="fa fa-desktop"></i> <span>CAJA</span></a></li>
					<?php } ?>											    
					<?php 
					if($visitante->permitir('prt_abm',1)){
						$cant=0;
						
						$modulo_talonario=$visitante->permitir('prt_abm',16);
						if($modulo_talonario)$cant++;
						$modulo_certificados=$visitante->permitir('prt_abm',32);
						if($modulo_certificados)$cant++;
						$modulo_condiciones_iva=$visitante->permitir('prt_abm',64);
						if($modulo_condiciones_iva)$cant++;
						$modulo_proveedores=$visitante->permitir('prt_abm',2);
						if($modulo_proveedores)$cant++;

					?>
					<li class="has-sub">
						<a href="javascript:;">
							<span class="badge pull-right"><?php echo $cant?></span>
							<i class="fa fa-th"></i> 
							<span>ABM</span>
						</a>
						<ul class="sub-menu">												
											
						     <?php if($modulo_talonario){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/talonarios/" data-toggle="ajax">Talonarios</a></li>
						    <?php } ?>					
						    <?php if($modulo_certificados){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/certificados/" data-toggle="ajax">Certificados</a></li>
						    <?php } ?>
						    <?php if($modulo_condiciones_iva){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/condiciones/" data-toggle="ajax">Cond. Iva</a></li>
						    <?php } ?>
						    <?php if($modulo_proveedores){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/entidades/proveedores/" data-toggle="ajax">Proveedores</a></li>
						    <?php } ?>
						</ul>						
					</li>
					<?php } ?>																			
					<?php if($visitante->permitir('prt_reportes',1)){ ?>
					<li><a href="#<?php echo base_url(); ?>index.php/reportes/" data-toggle="ajax"><i class="fa fa-file-text-o"></i> <span>REPORTES</span></a></li>					
					<?php } ?>
					<?php if($visitante->permitir('prt_proceso',1)){ ?>
					<li><a href="#<?php echo base_url(); ?>administracion/estadisticas.php" data-toggle="ajax"><i class="fa fa-cogs"></i> <span>PROCESOS</span></a></li>					
					<?php } ?>	
					<?php if($visitante->permitir('prt_usuarios',1)){ ?>						
					<li class="has-sub">
						<a href="javascript:;">
							<span class="badge pull-right">2</span>
							<i class="fa fa-user"></i> 
							<span>USUARIOS</span>
						</a>
						<ul class="sub-menu">
							<?php if($visitante->permitir('prt_usuarios',1)){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/seguridad/usuarios/" data-toggle="ajax">Consultar</a></li>
						    <?php } ?>					
						    <?php if($visitante->permitir('prt_usuarios',2)){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/seguridad/usuarios/ingresar_alta" data-toggle="ajax">Alta</a></li>
						    <?php } ?>											    
						</ul>
					</li>
					<?php } ?>	
					<?php if($visitante->permitir('prt_permisos',1)){ ?>
					<li><a href="#<?php echo base_url(); ?>index.php/seguridad/permisos/"  data-toggle="ajax" ><i class="fa fa-key"></i> <span>PERMISOS</span></a></li>									
					<?php } ?>
					
					<?php if($visitante->permitir('prt_configuracion',1)){ 
							$cant=0;
							$modulo_afip=$visitante->permitir('prt_configuracion',2);
							if($modulo_afip)$cant++;
							$modulo_test=$visitante->permitir('prt_configuracion',4);
							if($modulo_test)$cant++;
							$modulo_empresa=$visitante->permitir('prt_configuracion',8);
							if($modulo_empresa)$cant++;
							$modulo_variables_sistema=$visitante->permitir('prt_configuracion',32);
							if($modulo_variables_sistema)$cant++;
					?>						
					<li class="has-sub">
						<a href="javascript:;">
							<span class="badge pull-right"><?=$cant?></span>
							<i class="fa fa-gear"></i> 
							<span>CONFIGURACION</span>
						</a>
						<ul class="sub-menu">
							<?php 
							if($modulo_afip){
							 ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/afip/" data-toggle="ajax">Afip</a></li>
						    <?php } ?>											    
						    <?php if($modulo_test){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/test/" data-toggle="ajax">Test</a></li>
						    <?php } ?>
						    <?php if($modulo_empresa){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/entidades/empresas" data-toggle="ajax">Empresas</a></li>
						    <?php } ?>
						    <?php if($modulo_variables_sistema){ ?>
						    <li><a href="#<?php echo base_url(); ?>index.php/configuraciones" data-toggle="ajax">Variables del sistema</a></li>
						    <?php } ?>
						</ul>
					</li>
					<?php } ?>	

			        <!-- begin sidebar minify button -->
					<li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
			        <!-- end sidebar minify button -->
				</ul>
				<!-- end sidebar nav -->
			</div>
			<!-- end sidebar scrollbar -->
		</div>
		<div class="sidebar-bg"></div>
		<!-- end #sidebar -->
		
		<!-- begin #ajax-content -->
		<div id="ajax-content"></div>
		<!-- end #ajax-content -->
	
		
		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->

