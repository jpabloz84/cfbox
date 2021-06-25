<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>Sistema de gestión comercial  | Acceso</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="Es un sistema de gestión de comprobantes y control de stock que permite a distintos perfiles de usuario, administrar distintos productos, como asi llevar el control de caja y cuenta corriente de distintos clientes." name="description" />
	<meta content="jpodigital" name="author" />
	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="<?php echo BASE_FW ?>assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW ?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW ?>assets/css/animate.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW ?>assets/css/style.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW ?>assets/css/style-responsive.min.css" rel="stylesheet" />
	<link href="<?php echo BASE_FW ?>assets/css/theme/default.css" rel="stylesheet" id="theme" />
	<!-- ================== END BASE CSS STYLE ================== -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?php echo BASE_FW ?>assets/plugins/pace/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->
</head>
<body class="pace-top">
	<!-- begin #page-loader -->	
	<div id="page-loader" class="fade in"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<div class="login-cover">
	    <div class="login-cover-image"></div>
	    <div class="login-cover-bg"></div>
	</div>
	<!-- begin #page-container -->
	<div id="page-container" class="fade">
	    <!-- begin login -->
        <div class="login bg-black animated fadeInDown" style="top:40%; margin:0px 0;">
            <!-- begin brand -->
            <div class="login-header">
                <div class="brand">
                    <img  src="<?php echo BASE_FW ?>assets/img/logo_min.png" style="height:80px;width:82px"></img> 
                    <small>Sistema de gestión comercial</small>
                </div>
                <div class="icon">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>
            <!-- end brand -->
            <div class="login-content">
                <form action="index.php" method="POST" class="margin-bottom-0">
                    <div class="form-group m-b-20">
                        <input type="text" class="form-control input-lg inverse-mode no-border" placeholder="Usuario" required name="user" id="user"/>
                    </div>
                    <div class="form-group m-b-20">
                        <input type="password" class="form-control input-lg inverse-mode no-border" placeholder="Contraseña" required name="pass" id="pass" />
                    </div>                    
                    <div class="login-buttons">
                        <button type="button" class="btn btn-success btn-block btn-lg"  data-click="login-practice">Acceder</button>
                    </div>
                    
                </form>
            </div>
        </div>
        <!-- end login -->

        
	</div>
	<!-- end page container -->
  <!-- Modal -->
  <div class="modal fade"  role="dialog" id="modal-actuar-como">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Seleccione empresa/ sucursal</h4>
        </div>
        <div class="modal-body">
           <div class="row">
           <div class="form-group">                                
                   <div class="col-md-12">
                    <span class="input-group-addon" >Empresa</span>
                      <select class="form-control" id="id_empresa" onchange="changeempresa()">
                      </select>
                   </div>
            </div>
          </div>
          <div class="row">
           <div class="form-group">                                
                   <div class="col-md-12">
                    <span class="input-group-addon" >Sucursal</span>
                      <select class="form-control" id="id_sucursal" >
                      </select>
                   </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-inverse" id="btn-cerrar-actuar-como">GUARDAR</button>
        </div>
      </div>
      
    </div>
  </div>
<script type="text/javascript">
  
</script>
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery/jquery-1.9.1.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="<?php echo BASE_FW ?>assets/crossbrowserjs/html5shiv.js"></script>
		<script src="<?php echo BASE_FW ?>assets/crossbrowserjs/respond.min.js"></script>
		<script src="<?php echo BASE_FW ?>assets/crossbrowserjs/excanvas.min.js"></script>
	<![endif]-->
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery-hashchange/jquery.hashchange.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery-cookie/jquery.cookie.js"></script>
	<!-- ================== END BASE JS ================== -->
	
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?php echo BASE_FW ?>assets/js/apps.min.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->

	<script>
		$(document).ready(function() {
			App.init(ajax=true);

			 

		});

	$(document).on('click', '[data-click="login-practice"]', function() {			
		verifydata()
});
	$("#pass").keypress(function( event ) {
		  if ( event.which == 13 ) {
		     event.preventDefault();
		     verifydata()
		  }  
		});


function verifydata()
{

	$.ajax({
		    	 		dataType: "xml",
						type: 'POST',
						url:'<?php echo site_url('login/verify/'); ?>',
			  			data: { user: $('#user').val(), pass:$('#pass').val() } ,			      				      
			      			success: function( xmlResponse ) {			      				
			      				var $xml = $(xmlResponse)             
	            				var numError=$xml.find('numError').text();
	            				var descError=$xml.find('descError').text();
	            				//var Response=$xml.find('Data').text();	

                      if(numError==1000){
                        $("#id_empresa").html($xml.find('Data').text())
                        $("#modal-actuar-como").modal("show")
                        changeempresa()
                        return
                      }	      			
	            				if(numError!=0)
	            				{
	            					
	            						    var targetModalHtml = ''+
								        '<div class="modal fade" data-modal-id="reset-local-storage-confirmation">'+
								        '    <div class="modal-dialog">'+
								        '        <div class="modal-content">'+
								        '            <div class="modal-header">'+
								        '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
								        '                <h4 class="modal-title"><i class="fa fa-refresh m-r-5"></i> Usuario o Contraseña incorrecto</h4>'+
								        '            </div>'+
								        '            <div class="modal-body">'+
								        '                <div class="alert alert-info m-b-0">Intente de nuevo o contáctese con el administrador del sistema si el problema persiste</div>'+
								        '            </div>'+
								        '            <div class="modal-footer">'+        
								        '                <a href="javascript:;" class="btn btn-sm btn-inverse" data-click="confirm-reset-local-storage"><i class="fa fa-check"></i> OK</a>'+
								        '            </div>'+
								        '        </div>'+
								        '    </div>'+
								        '</div>';
								        
								        $('body').append(targetModalHtml);
								        $('[data-modal-id="reset-local-storage-confirmation"]').modal('show');
								   
									    $(document).on('hidden.bs.modal', '[data-modal-id="reset-local-storage-confirmation"]', function(e) {
									        $('[data-modal-id="reset-local-storage-confirmation"]').remove();
									    });

									    $(document).on('click', '[data-click=confirm-reset-local-storage]', function(e) {
									        e.preventDefault();
									        $('[data-modal-id="reset-local-storage-confirmation"]').remove();
									    });

	            				}else
	            				{
	            					
	            					window.location.href='<?php echo base_url() ?>'+descError
	            				}	
				      		}, //success
				      		beforeSend: function(){
            				$("#page-loader").modal("show")            
                			},
            				complete: function(){
            				$("#page-loader").modal("hide")            
                			}
			    	});//ajax


}

function changeempresa(){
    $.ajax({dataType: "xml",type: 'POST',url:'<?php echo site_url('login/getsucursales/'); ?>',
      data: { id_empresa: $('#id_empresa').val() } ,
                  success: function( xmlResponse ){ 
                    var $xml = $(xmlResponse)             
                      var numError=$xml.find('numError').text();
                      var descError=$xml.find('descError').text();
                      var Response=$xml.find('Data').text();                
                      if(numError==0)
                      {
                        $("#id_sucursal").html(Response)
                      }
                  }
           });  

  }

$("#btn-cerrar-actuar-como").click(function(e){
  e.preventDefault()
  var id_sucursal=$("#id_sucursal").val()
  if(id_sucursal=="" || id_sucursal==null)return

    $.ajax({dataType: "xml",type: 'POST',url:'<?php echo site_url('login/verify/'); ?>',
      data: { id_sucursal: $('#id_sucursal').val(),user: $('#user').val(), pass:$('#pass').val() } ,
                  success: function( xmlResponse ){ 
                    var $xml = $(xmlResponse)             
                      var numError=$xml.find('numError').text();
                      var descError=$xml.find('descError').text();
                      //var Response=$xml.find('Data').text();                
                      if(numError==0)
                      {
                        window.location.href='<?php echo base_url() ?>'+descError
                      }
                  }
           });
  

})
	</script>
</body>
</html>
