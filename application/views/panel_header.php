<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="es" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title id="page-title">Coffee | sistema de gestión</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/css/animate.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/css/style.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/css/style-responsive.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW?>assets/css/theme/black.css" rel="stylesheet"  />
  <link href="<?php echo base_url()?>assets/css/custom.css" rel="stylesheet"  />
  <link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
  <link href="<?=BASE_FW?>assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" />  
	<!-- ================== END BASE CSS STYLE ================== -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?php echo BASE_FW; ?>assets/plugins/pace/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	<?php echo $permisos_front; ?>
</head>
<body>
	<input type="hidden" id="last_nro_pedido" value="<?=$last_nro_pedido?>">
	<input type="hidden" id="base_url_parent" value="<?=base_url()?>">
	<div class="pace  pace-inactive">
		<div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
        <div class="pace-progress-inner"></div>
		</div>
		<div class="pace-activity"></div>
	</div>
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade in"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	<input type="hidden" name="empresa_nombre" id="empresa_nombre" value="<?php echo $empresaname ?>" />
<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $id_usuario ?>" />
			<!-- #modal-dialog -->
                    <div class="modal fade" id="modal-dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title">Finalizar sesión</h4>
                                </div>
                                <div class="modal-body">
                                    ¿Desea realmente salir del sistema?
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Cancelar</a>
                                    <a href="javascript:;" class="btn btn-sm btn-success" data-click="salir">Salir del sistema</a>
                                </div>
                            </div>
                        </div>
                    </div>

	
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">

		<!-- begin #header -->
		<div id="header" class="header navbar navbar-inverse navbar-fixed-top">
			<!-- begin container-fluid -->
			<div class="container-fluid">
				<!-- begin mobile sidebar expand / collapse button -->
				<div class="navbar-header">
					<?php
					$logo=BASE_FW."/assets/img/logo_min.png";
					if(count($empresa)>0){
						if(isset($empresa['logo']) && $empresa['logo']!="")
						{
							$logo=base_url().$empresa['logo'];
						}
					}?>
					<a href="./panel" class="navbar-brand"><img src="<?=$logo?>" style="width:auto;height:36px" /></a>

					<button type="button" class="navbar-toggle" data-click="sidebar-toggled">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<!-- end mobile sidebar expand / collapse button -->
        <p class="navbar-text navbar-left"><?php echo ($empresaname!='')?strtoupper($empresaname):""?></p>
	
				<!-- begin header navigation right -->
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown navbar-user">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<img src="<?php echo $visitante->get_thumbs_profile(); ?>" alt="usuario logueado" /> 
							<span class="hidden-xs"><?php echo $visitante->get_user(); ?> - (<?php echo $visitante->get_name(); ?>)</span> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu animated fadeInLeft">
							<li class="arrow"></li>
								<!-- <li><a href="javascript:;">Edit Profile</a></li>
							<li><a href="javascript:;"><span class="badge badge-danger pull-right">2</span> Inbox</a></li>
							<li><a href="javascript:;">Calendar</a></li>
							<li><a href="javascript:;">Setting</a></li>-->
							<li class="divider"></li>
							<li><a href="<?php echo base_url();?>">RECARGAR</a></li>
							<li><a href="javascript:;">EDITAR PERFIL</a></li>              
							<li><a href="javascript:;" data-click='deslogearse'>SALIR</a></li>
						</ul>
					</li>
				</ul>
				<!-- end header navigation right -->
			</div>
			<!-- end container-fluid -->
		</div>
		<!-- end #header -->
