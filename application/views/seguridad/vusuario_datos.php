
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$urlcallback=($callback!="")?$callback:"index.php/seguridad/usuarios/ingresar_alta/";

$id_rol=$visitante->get_id_rol(); 
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-wizard/css/bwizard.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/parsley/src/parsley.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<style type="text/css">
.form-horizontal .form-group {
  margin-right: 0px; 
    margin-left: 0px;
}
</style>

<input type="hidden" id="base_url" value="<?=base_url()?>" />
<!-- begin #content -->
<div id="content" class="content">    
    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-md-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">                        
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload" data-original-title="" title="" data-init="true" ><i class="fa fa-repeat"></i></a>
                    </div>
                    <h4 class="panel-title">Usuarios Alta/Edición</h4>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" enctype="multipart/form-data" id="formuploadajax" method="post" name="form-wizard" autocomplete="off"  style="display: none">
                        <input type="hidden" id="id_persona" name="id_persona" value="<?=$id_persona?>">  
                        <input type="hidden" id="id_usuario" name="id_usuario" value="<?=$id_usuario?>">  
                        <input type="hidden" id="id_rol_user" value="<?=$id_rol?>">
                           <input type="hidden" id="modo" name="modo" value="<?=$modo?>">  
                        <div id="wizard">
                            <ol>
                                <li id="paso1" >
                                    PASO 1
                                    <small>Usuario, Clave y Rol</small>
                                </li>
                                <li  id="paso2" >
                                    PASO 2
                                    <small>Datos personales</small>
                                </li>
                                <li id="paso3" >
                                    PASO 3
                                    <small>telefono, email, Imagen de perfil, Empresa</small>
                                </li>
                                <li id="paso4" >
                                    PASO 4
                                    <small>Envio de formulario y confirmación de recepción</small>
                                </li>
                            </ol>
                            <!-- begin wizard step-1 -->
                            <div class="wizard-step-1">
                                <fieldset>
                                    <legend class="pull-left width-full">Usuario, Clave y Rol</legend>
                                    <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-6 -->
                                        <div class="col-md-3" style="padding:0 0px">
                                            <div class="form-group block1">
                                                <label>Usuario</label>
                                                <div class="controls">                                                
                                                <input type="text" name="usuario" id="usuario"  placeholder="Nombre usuario del sistema" class="form-control" data-parsley-group="wizard-step-1" required  data-parsley-type="alphanum" />                                                
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-6 -->
                                        <!-- begin col-6 -->
                                        <div class="col-md-3" >
                                            <div class="form-group">
                                                <label>Clave</label>
                                                <div class="controls">
                                                <input  data-toggle="password" data-placement="after" type="password" name="clave" id="clave"  class="form-control" data-parsley-group="wizard-step-1"  data-parsley-equalto="#clave2" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Repita Clave</label>
                                                <div class="controls">
                                                <input  data-toggle="password" data-placement="after" type="password" name="clave2" id="clave2" class="form-control" data-parsley-group="wizard-step-1"  data-parsley-equalto="#clave" />
                                               </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-3 -->
                                        <div class="col-md-3" style="padding:0 0px">
                                            <div class="form-group" >
                                                <label>Rol</label>
                                                <div class="controls">
                                                    <select id="rol"  name="rol" class="form-control"  data-parsley-group="wizard-step-1" >
                                                        <option value=""></option>
                                                        <?php                                                        
                                                                                                                                                                 
                                                        foreach ($roles as $rol) {
                                                            if($rol['id_rol']==1 && $id_rol!=$rol['id_rol'])
                                                            {
                                                                continue;
                                                            }else
                                                            {
                                                                echo "<option value='".$rol['id_rol']."'>".$rol['rol']."</option>";
                                                            }
                                                        }                                                        
                                                        ?>
                                                     </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-6 -->
                                    </div>
                                    <!-- end row -->
                                    
                                </fieldset>
                            </div>
                            <!-- end wizard step-1 -->
                            <!-- begin wizard step-2 -->
                            <div class="wizard-step-2">
                                <fieldset>
                                    <legend class="pull-left width-full">Datos personales</legend>
                                    <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-3 -->
                                        <div class="col-md-3" >
                                            <div class="form-group" >
                                                <label>Nombre (*)</label>
                                                <div class="controls">                                                
                                                <input type="text" name="nombre" id="nombre" placeholder="Ingrese nombre" class="form-control" data-parsley-group="wizard-step-2" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->  
                                        <!-- begin col-3 -->
                                        <div class="col-md-3" style="padding:0 0px">
                                            <div class="form-group" style="margin-left:0px">
                                                <label>Apellido (*)</label>
                                                <div class="controls">                                                
                                                <input type="text" name="apellido" id="apellido" placeholder="Ingrese apellido" class="form-control" data-parsley-group="wizard-step-2" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->                                      
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tipo documento (*)</label>
                                                <div class="controls">
                                                	<select id="tipo_docu" name="tipo_docu" class="form-control" data-parsley-group="wizard-step-2" >                                                		
                                                         <?php                                                        
                                                        
                                                        foreach ($documentos as $docu) {
                                                                echo "<option value='".$docu['tipo_docu']."'>".$docu['documento']."</option>";
                                                        }
                                                        
                                                        ?>
                                                	</select>                                                
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group" >
                                                <label>Nro. documento (*)</label>
                                                <div class="controls">
                                                   <input type="text" name="nro_docu" id="nro_docu" placeholder=" documento" class="form-control" data-parsley-group="wizard-step-2" data-parsley-type="number"  />                                                
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->                                        
                                    </div>
                                    <!-- end row -->
                                     <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-3 -->
                                        <div class="col-md-3" >
                                            <div class="form-group" >
                                                <label>Sexo (*)</label>
                                                <div class="controls">
                                                    <label class="radio-inline">
                                                    <input type="radio" name="sexoRadios" value="M" data-parsley-mincheck="1" data-parsley-maxcheck="1"  />
                                                    Masculino
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="sexoRadios" value="F" data-parsley-mincheck="1"  data-parsley-maxcheck="1"/>
                                                        Femenino
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                         <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                            	<label>Provincia</label>                                            
                                                <div class="controls">
                                                	<select id="provincia" name="provincia" onchange="cargar_localidades()" class="form-control">
                                                <?php
                                                
                                                        foreach ($provincias as $prov) {
                                                                if($prov['id_pro']==12)
                                                                {
                                                                     echo "<option value='".$prov['id_pro']."' selected='selected'>".$prov['descripcion_pro']."</option>";
                                                                }
                                                                else
                                                                {
                                                                     echo "<option value='".$prov['id_pro']."'>".$prov['descripcion_pro']."</option>";
                                                                }
                                                               
                                                       }
                                                            
                                                        
                                                 ?>
                                                     </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->     
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                            	<label>Localidad</label>                                            
                                                <div class="controls" id="ctrllocalidad">
                                                         <select class="combobox" id="localidad" name="localidad" data-parsley-group="wizard-step-2"  >
                                                            <option value=""></option>    
                                                         </select>                                                	
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->   
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Fecha de nacimiento</label>
                                                <div class="controls">
                                                 <input type="text"  name="fe_naci" id="fe_naci" placeholder="dd/mm/yyyy" class="form-control" data-parsley-group="wizard-step-2"   />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                    </div>
                                    <!-- end row -->


                                      <!-- begin row -->
                                    <div class="row">
                                         <!-- begin col-1 -->
                                        <div class="col-md-1" >
                                            <div class="form-group" >
                                                <label>Car. Cel.</label>                                            
                                                <div class="controls">
                                                      <input type="text" name="car_tel" id="car_tel" placeholder="sin cero" class="form-control"   data-parsley-group="wizard-step-2" ata-parsley-length="[2, 5]" maxlength="5" />
                                                </div>                                                
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                        <!-- begin col-2 -->
                                        <div class="col-md-2" >
                                            <div class="form-group" >
                                                <label>Nro. Cel. </label>                   
                                                <div class="controls">                              
                                                      <input type="text" name="nro_tel" id="nro_tel" placeholder="completar sin 15" class="form-control"  data-parsley-group="wizard-step-2" ata-parsley-length="[4, 8]" maxlength="8" />
                                                </div>                                                
                                            </div>
                                        </div>
                                        <!-- end col-2 -->
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Calle</label>
                                                <div class="controls">
                                                 <input type="text"  name="calle" id="calle" class="form-control" data-parsley-group="wizard-step-2"   />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>nro.</label>
                                                <div class="controls">
                                                 <input type="text"  name="nro" id="nro"  class="form-control" data-parsley-group="wizard-step-2"   value="0" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                        <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>Piso</label>
                                                <div class="controls">
                                                 <input type="text"  name="piso" id="piso"  class="form-control" data-parsley-group="wizard-step-2"   />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                         <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>Dpto</label>
                                                <div class="controls">
                                                 <input type="text"  name="dpto" id="dpto"  class="form-control" data-parsley-group="wizard-step-2"   />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                         <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>CP.</label>
                                                <div class="controls">
                                                 <input type="text"  name="cp" id="cp"  class="form-control" data-parsley-group="wizard-step-2"  value="" placeholder="codigo" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                    </div>
                                    <!-- end row -->
                                </fieldset>
                            </div>
                            <!-- end wizard step-2 -->
                            <!-- begin wizard step-3 -->
                            <div class="wizard-step-3">
                                <fieldset>
                                    <legend class="pull-left width-full">Estado,Email e imagen</legend>
                                    <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-3 -->
                                        <div class="col-md-2">
                                            <div class="form-group" >    
                                                <label id="txthabilitado">Habilitado</label>
                                                <div class="controls"  id="ctrlhabilitado">
                                                      <input type="checkbox" data-render="switchery" data-theme="default" checked  id="habilitado" name="habilitado"/>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->                                      
                                        <!-- begin col-3 -->
                                        <div class="col-md-3" >
                                            <div class="form-group" >
                                                <label>Email</label>
                                                <div class="controls">
                                                    <input type="text" data-parsley-type="email" name="email" id="email" placeholder="usuario@email.com" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->                                        
                                       
                                        <!-- begin col-4 -->
                                        <div class="col-md-4" >
                                            <div class="form-group">
                                                <label>Empresa / Sucursal</label>
                                                <div class="controls">
                                                    <select id="id_sucursal" name="id_sucursal"class="form-control">
                                                        <option value=""></option>
                                                        <?php
                                                
                                                        foreach ($sucursales as $suc) {
                                                            echo "<option value='".$suc['id_sucursal']."'>".$suc['sucursal']." (".$suc['empresa'].")</option>";
                                                               
                                                       }
                                                        
                                                 ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-4 -->
                                    </div>
                                    <!-- end row -->                                    
                                    <!-- begin row -->
                                    <div class="row">
                                         <!-- begin col-3 -->
                                        <div class="col-md-3" >
                                            <div class="form-group">
                                                <label>Imagen de perfil</label>
                                                <div class="controls">
                                                    <input type="file" class="form-control"  name="img_profile" id="img_profile" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                         <!-- begin col-3 -->
                                        <div class="col-md-3" >
                                            <div class="form-group">
                                                <label>Firma</label>
                                                <div class="controls">
                                                    <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" id="firma" style="height: 200px;width: auto; border-style: groove;border-width: thin;" />
                                                    <input type="button" class="btn btn-sm btn-default" id="btnfirma" value="Realizar firma"/>
                                                    <input type="hidden" name="img_firma" id="img_firma" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                    </div>
                                    <!-- end row -->                                    
                                </fieldset>
                            </div>
                            <!-- end wizard step-3 -->
                            
                            <div>
                                <div class="jumbotron m-b-0 text-center">
                                    <h4 id="titulo-confirmacion"></h4>
                                    <p id="body-confirmacion"></p>
                                    <p>
                            <a class='btn btn-success btn-lg hide' id="btnreload"  href='javascript:;' onclick='recargar()'  >CONTINUAR</a>
                                        
                                    </p>
                                </div>
                            </div>
                            <!-- end wizard step-4 -->
                        </div>
                    </form>
                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->
         <!-- ventana para puntaciones  -->
                    <div class="modal modal-message fade" id="modal-details">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="cancel-modal-details">×</button>
                                    <h6 class="modal-title" id="modal-details-titulo">Se ha encontrado una o varias personas en nuestra base de datos!</h6>
                                </div>
                                <div class="modal-body" style="width:100%">
                                    <table class="table table-striped" id="modal-details-body">
                                    <thead>
                                    <tr>
                                        <th>DOCUMENTO</th>
                                        <th>NOMBRE</th>                                        
                                    </tr>
                                    </thead>
                                    <tbody>                                    
                                    </tbody>
                                    </table>
                                    
                                </div>
                                <div class="modal-footer">
                                    <p>¿que hacemos con esto?</p>
                                    <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal" id="cancel-menssaje">Reemplazar por los datos existentes por los que estoy ingresando</a>
                                    <a href="javascript:;" class="btn btn-sm btn-primary" id="cargar-menssaje">Cargar esta persona desde la base de datos</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ventana para mensaje -->
                    <div class="modal modal-message fade" id="mensaje-cancel-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="mensaje-cancel">×</button>
                                    <h6 class="modal-title" id="mensaje-titulo"></h6>
                                </div>
                                <div class="modal-body"  id="mensaje-body" style="width:100%">                                    
                                    
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal" id="mensaje-cancel-1">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                       <!-- ventana para las confirmar -->
                   <div class="modal modal-message fade" id="modal-message-confirmar">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="cancel-message-confirmar">×</button>
                                    <h6 class="modal-title"  id="titulo-message-confirmar"></h6>
                                </div>
                                <div class="modal-body" id="body-message-confirmar" style="width:100%">
                                    
                                </div>
                                <div class="modal-footer">                                    
                                    <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal" id="cancel-emitir">Cancelar</a>
                                    <a href="javascript:;" class="btn btn-success m-r-5 m-b-5" id="confirm-evalue">Confirmar</a>
                                </div>
                            </div>
                        </div>
                    </div>


                         <!-- ventana para firma -->
                   <div class="modal modal-message fade" id="modal-firma">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="cancel-message-confirmar">×</button>
                                    <h3 class="modal-title"  id="titulo-message-confirmar">Ingrese su firma aqui</h3>
                                </div>
                                <div class="modal-body" style="width:100%">
                                <div class="row">
                                    <div class="col-lg-5" style="float: none; margin: 0 auto;">
                                        <div id="divCanvas" style="cursor:crosshair;overflow:auto;height: 400px;border-style: dotted;">
                                                          <canvas id="pizarra" style="position: absolute;"></canvas>
                                        </div>    
                                    </div>    
                                </div>
                                </div>
                                <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-2">
                                        <a href="javascript:;" class="btn btn-md btn-danger" data-dismiss="modal" id="cancel-firma">Cancelar</a>
                                    </div>
                                    <div class="col-md-10">
                                        
                                         <a href="javascript:;" class="btn btn-md btn-white"  id="limpiar-firma">Limpiar</a>
                                        <a href="javascript:;" class="btn btn-md btn-primary m-r-5 m-b-5" id="confirm-firma">Guardar</a>    
                                    </div>
                                </div>                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>



</div>
<!-- end #content -->

<script type="text/javascript" src="<?=base_url()?>js/usuarios.js"></script>
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/javascript">   

var green = '#00acac',
red = '#ff5b57',
blue = '#348fe2',
purple = '#727cb6',
orange = '#f59c1a',
black = '#2d353c';

App.restartGlobalFunction();
App.setPageTitle('Alta/ Edición de Usuarios | Coffee APP');

var FormWizardValidation = function () {
    "use strict";
    return {
        //main function
        init: function () {
            $.getScript('<?=BASE_FW?>assets/plugins/parsley/dist/parsley.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-wizard/js/bwizard.min.js').done(function() {
                    handleBootstrapWizardsValidation();
                });
            });
        }
    };
}();  
    
 FormWizardValidation.init(); 
$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-show-password/bootstrap-show-password.js').done(function(){
            $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
             $.getScript('<?=BASE_FW?>assets/plugins/switchery/switchery.min.js').done(function() {
                 $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {
                    spinnerStart($("#panel-body"))   
                          
                    
                    if($("#modo").val()=="M")    
                    {
                     
                     ofwt.getAsync('getusuario',Array($('#id_usuario').val()),modificarload)                  
                    }else
                    {
                    cargar_localidades();
                    renderHabilitado();    
                    }
                    
                    $("#nro_docu").keypress(function(e){
                        return teclaentero(e)
                    }) 

                     $("#fe_naci").mask("99/99/9999");
                    $('#fe_naci').datepicker({
                         todayHighlight: true,
                         format: 'dd/mm/yyyy',
                         language: 'es',
                         autoclose: true
                    });

                    $("#cargar-menssaje").click(function(){
                    $("[id='modal-details']").modal("hide")
                    cargar_datos_persona(arrPersona[0])
                     }) 
                    $('[data-click="panel-reload"]').click(function(){

                            handleCheckPageLoadUrl("<?php echo base_url().$urlcallback; ?>");
                         })

                      spinnerEnd($("#panel-body"))
                    $("#formuploadajax").show();
                    });
                    });
                 });
                });
        });       

    })
var wizard=null;

    var handleBootstrapWizardsValidation = function() {
    "use strict";
   wizard=$("#wizard").bwizard({ validating: function (e, ui) { 
            //si el siguiente paso es atras , que no valide
            if(ui.index>ui.nextIndex)
            {
                if(ui.index==3){
                    $("#titulo-confirmacion").html("")
                    $("#body-confirmacion").html("")
                    $("#btnreload").hide(); //si vuelve  y el paso actual es el ultimo paso, que reinice el mensaje de carga
                }
                return true;
            }

            Pace.restart();
            if (ui.index == 0) {
                // step-1 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-1')) {
                    return false;
                }
                if(evalpaso(ui.index)==false)
                    {return false}
                
            } else if (ui.index == 1) {
                // step-2 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-2')) {
                    return false;
                }
                if(evalpaso(ui.index)==false)
                    {return false}

            } else if (ui.index == 2) {
                // step-3 validation
                if (false === $('form[name="form-wizard"]').parsley().validate('wizard-step-3')) {
                    return false;
                }
                if(evalpaso(ui.index)==false)
                {return false}
            }

        } 
    });
};


  

var renderHabilitado = function() {    
        $('#habilitado').each(function() {
            var option = {};
                option.color = green;
                option.secondaryColor = '#dfdfdf';
                option.className = 'switchery';
                option.disabled =false;
                option.disabledOpacity =  0.5;
                option.speed = '0.5s';
            var switchery = new Switchery(this, option);
        });
        $('#habilitado').live('change', function() {
        $('#txthabilitado').text(($(this).prop('checked'))?'Habilitado':'No habilitado');
        });
    
};

function modificarload(res){
    if (res.length>0)
    {
        $("#apellido").val(res[0].apellido)
        $("#nombre").val(res[0].nombres)
        $("#nro_docu").val(res[0].nro_docu)
        $("#tipo_docu").val(res[0].tipo_docu)
        $("#email").val(res[0].email)        
        $("input:radio[name='sexoRadios']").filter("[value='"+res[0].sexo+"']").attr('checked', true);
        

        $("#car_tel").val(res[0].car_tel)
        $("#nro_tel").val(res[0].nro_tel)
        $("#calle").val(res[0].calle)
        $("#nro").val(res[0].nro)
        $("#piso").val(res[0].piso)
        $("#dpto").val(res[0].dpto)
        $("#fe_naci").val(res[0].fe_naci)
        $("#cp").val(res[0].cp)

        $("#usuario").val(res[0].usuario)
        $("#rol").val(res[0].id_rol)
        $("#clave").val(res[0].clave)
        $("#clave2").val(res[0].clave)
        $("#id_persona").val(res[0].id_persona)
        $("#id_sucursal").val(res[0].id_sucursal)
        cargarhabilitado(res[0].habilitado)
        cargar_localidades(res[0].id_loc)

        
            if(res[0].firma!=""){
                $("#firma").attr("src",res[0].firma)
                $("#img_firma").val(res[0].firma)
            }
        
    }
    
                    
}


function evalpaso(paso)
{
  
var mensaje=""
    
    if(paso==0)
    {   
        var cmp=Array('id_usuario')
        if($("#modo")=="A")
        {
            
            arrUsuario=ofwtlocal.get('usuarioexistencia',Array($("#usuario").val()));  
            if(arrUsuario.length>0){
              mensaje=mensaje+"Usuario no disponible. Debe ingresar otro\n"  
            }
            
        }
        

        if(mensaje.trim()!="")
        {
           swal("Atención",mensaje,"warning")            
            return false;
        }
    
        
    }

    if(paso==1)
    {
        var sexo=$('input[name=sexoRadios]:checked').val();
        mensaje=mensaje+(((sexo=="")?"No ha completado el campo sexo\n":""))        
        
        
        if(mensaje.trim()!="")
        {
            swal("Atención",mensaje,"warning")
           
                   
                   return
        }
          buscar_similitudes();
    
    }//paso2


    if(paso==2)
    {    
     
    return validardatos()
    }//paso3
    
    
return true
}

var enviando_datos=false


function validardatos()
{
//si esta llamando le metodo que envia datos, que devuelta true, asi no valida para que se muestre
if(enviando_datos){
    return true;
}
$("#titulo-confirmacion").html("")
$("#body-confirmacion").html("")

                      
            var mensajeErrors=""
            var mensajeWarning=""
                    var usuario=$("#usuario").val()                 
                    var clave=$("#clave").val()                                        
                    var apellido=$("#apellido").val()                 
                    var nombres=$("#nombre").val()                    
                    var rol=$("#rol").val()
                    var tipo_docu=$('#tipo_docu').val();
                    var nro_docu=$('#nro_docu').val();
                    var localidad=$('#localidad').val();
                    var provincia=$('#provincia').val();
                    var id_sucursal=$('#id_sucursal').val();
                    
                    var sexo=$('input[name=sexoRadios]:checked').val();
                    var email=$('#email').val();
                    
                    if(usuario =="")                    
                     mensajeErrors+="<li>No completo el campo de usuario</li>";

                 if(clave =="" && rol!=7)                    
                     mensajeErrors+="<li>No completo el campo de clave</li>";


                 if(tipo_docu =="" && rol!=7)                    
                     mensajeErrors+="<li>No seleccionó un tipo de documento</li>";

                 //cliente web
                 if(tipo_docu =="" && rol==7)                    
                     mensajeWarning+="No seleccionó un tipo de documento. ";
                 
                 if(nro_docu =="" && rol!=7)                    
                     mensajeErrors+="<li>No ingresó  un numero de documento</li>";

                 //cliente web
                 if(nro_docu =="" && rol==7)                    
                     mensajeWarning+="No ingresó  un numero de documento";

                 if(rol =="")                    
                     mensajeErrors+="<li>No seleccionó un rol</li>";

                 if(apellido =="")                    
                     mensajeErrors+="<li>No ingresó apellido</li>";

                 if(nombres =="")                    
                     mensajeErrors+="<li>No ingresó un nombre</li>";

                  if(email =="")                    
                     mensajeErrors+="<li>No ingresó un email</li>";

                 //los clientes web y los administradores no seleccionan sucursal
                 if(id_sucursal =="" && rol!=1 && rol!=7)                    
                     mensajeErrors+="<li>No seleccionó una empresa/ sucursal</li>";

                 


                 if(mensajeErrors!="")
                 {
                   $('[id="mensaje-titulo"]').html("Atención: Revise los siguientes campos")
                   $('[id="mensaje-body"]').html(mensajeErrors)
                   $('[id="mensaje-cancel-modal"]').modal("show");
                   return false;
                 }

                 

if(enviando_datos)return false;

/*$("#titulo-message-confirmar").html("CONFIRMAR DATOS")
$("#body-message-confirmar").html("<div class='alert alert-success m-b-0' ><h6><i class='fa fa-info-circle'></i>¿Seguro que desea enviar este formulario?</h6></div>")
$('[id="modal-message-confirmar"]').modal("show")*/
if(mensajeWarning!=""){
 swal({ title: "CONFIRMAR DATOS",
          text: mensajeWarning+ '¿Desea continuar de todos modos?',
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true
            }, function () {
           var formData = new FormData(document.getElementById("formuploadajax"));  
           enviardatos(formData)
            });
}else{
    swal({ title: "CONFIRMAR DATOS",
          text: '¿Desea continuar y guardar estos datos?',
          type: "info",
          showCancelButton: true,
          closeOnConfirm: true
            }, function () {
           var formData = new FormData(document.getElementById("formuploadajax"));  
           enviardatos(formData)
            });
}


    
return false

}//enviar datos


function enviardatos(formData){
$("#titulo-confirmacion").html("")
 $("#body-confirmacion").html("")   
if(enviando_datos)return;
        
        $("#body-confirmacion").html("Enviando datos...")
        enviando_datos=true;
        $("#wizard").bwizard("show",3)//muetro el paso 4
        event.stopPropagation();
        
        $.ajax({url:'<?=base_url()?>index.php/seguridad/usuarios/save',
                    type: "post",
                    dataType: "json",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function(){
                        spinnerPanel()
                    },
                    complete: function(){}
                    })
                    .done(function(response){
                        
                         
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;
                        $("#btnreload").show();
                        if(numError == 0)
                        {   
                            $("#usuario").val("")                 
                            $("#clave").val("")                    
                            $("#clave2").val("")
                            $("#car_tel").val("")
                            $("#nro_tel").val("")
                            $("#calle").val("")
                            $("#nro").val("")
                            $("#piso").val("")
                            $("#dpto").val("")
                            $("#cp").val("")
                            $('#tipo_docu').val(1);
                            $('#nro_docu').val("");                    
                            $('#provincia').val(12);
                            $('#email').val("");                    
                            $("#apellido").val("")                 
                            $("#nombre").val("")                 
                            $("#rol").val("")                                         
                            $("#id_sucursal").val("")                                         
                                                   
                            $('#localidad').find("option").remove().end();                                                
                            $('#img_profile').val("");
                            $('#img_profile_persona').val("");
                            
                            $("#titulo-confirmacion").html("Perfecto!!!")
                            if($("#modo").val()=="A")
                            {
                            
                            $("#body-confirmacion").html("El usuario se dió de alta correctamente.")    
                            }else
                            {
                                $("#body-confirmacion").html("El usuario se modificó correctamente")    
                            }
                            
                            
                            
                            enviando_datos=false;

                        }else
                        {
                            $('[id="mensaje-titulo"]').html("Problemas!")
                            $('[id="mensaje-body"]').html("Ups! Tuvimos un inconveniente al tomar su formulario. Intente luego")
                            $('[id="mensaje-cancel-modal"]').modal("show");                        
                            $("#titulo-confirmacion").html("No se pudo recibir su solicitud")
                            $("#body-confirmacion").html("")
                            enviando_datos=false;
                        }

                    });//done ajax

}//enviardatos



var arrPersona=Array();
function buscar_similitudes()
{
    if($("#modo")!="A") return

 var nro_docu=$("#nro_docu").val();
 var tipo_docu=$("#tipo_docu").val();
 var sexo=$('input[name=sexoRadios]:checked').val();;
 

    arrPersona=ofwtlocal.get('gethomonimos',Array(nro_docu,sexo,tipo_docu));
    if(arrPersona.length>0)
    {var trow=""
        for (l in arrPersona)
        {per=arrPersona[l]
         nro_docu= per.nro_docu
         $("#id_persona").val(per.id_persona)
         apellido= per.apellido
         nombres= per.nombres
         trow+="<tr><td>"+nro_docu+"</td><td>"+apellido+", "+nombres+" <td></tr>";
        }  

        $("#modal-details-body > tbody").html(trow)                    
        $("[id='modal-details']").modal("show")
    }else
    {
        $("#id_persona").val(0)
    }

}



function cargar_datos_persona(oPersona)
{

$("#id_persona").val(oPersona.id_persona)
$("#nro_docu").val(oPersona.nro_docu)
$("#tipo_docu").val(oPersona.tipo_docu)
$("input:radio[name='sexoRadios']").filter("[value='"+oPersona.sexo+"']").attr('checked', true);
cargarHabilitado(oPersona.habilitado)
$("#nombre").val(oPersona.nombres)
$("#apellido").val(oPersona.apellido)


if(oPersona.car_tel!=null)
$("#car_tel").val(oPersona.car_tel)
if(oPersona.nro_tel!=null)
$("#nro_tel").val(oPersona.nro_tel)
if(oPersona.calle!=null)
$("#calle").val(oPersona.calle)
if(oPersona.nro!=null)
$("#nro").val(oPersona.nro)
if(oPersona.piso!=null)
$("#piso").val(oPersona.piso)
if(oPersona.dpto!=null)
$("#dpto").val(oPersona.dpto)
if(oPersona.cp!=null)
$("#cp").val(oPersona.cp)
$("#email").val(oPersona.email)
$("#provincia").val(oPersona.id_pro)
cargar_localidades(oPersona.id_loc)

}


function cargarhabilitado(habilitado=1)
{
var checked=(habilitado==1)?'checked':''
$("#ctrlhabilitado").html("<input type='checkbox' data-render='switchery' data-theme='default' "+checked+"  id='habilitado' name='habilitado'/>")
renderHabilitado()

}


function cargar_localidades(id_loc='')
{


var id_pro=$("#provincia").val()
if(id_pro=="")
{
    return
}

$("#provincia_desc").val($("#provincia option:selected" ).text());
var cmp=Array('id_loc','descripcion_loc')
var arr=ofw.getAsync('localidades',cmp,'id_pro='+id_pro,'descripcion_loc asc', function(e){cargarCombo(e,id_loc)});  

}


function cargarCombo(arr,defaultValue)
{

$("#ctrllocalidad").html("<select class='combobox' id='localidad' name='localidad' data-parsley-group='wizard-step-2'  ><option value=''></option></select>")
    for (l in arr)
    {loc=arr[l]
       id_loc= loc.id_loc
       descripcion= loc.descripcion_loc
       if(id_loc==defaultValue)
        {    
        $('#localidad').append('<option value="'+id_loc+'" selected="selected">'+descripcion+'</option>');
        }else
        {
        $('#localidad').append('<option value="'+id_loc+'">'+descripcion+'</option>');
        }
        
    }
    $(".combobox").combobox();
}

//http://localhost:8080/coffee/index.php/panel#http://localhost:8080/coffee/index.php/seguridad/usuarios/

function recargar()
{
    $("#btnreload").hide();
     handleLoadPage("#<?php echo base_url().$urlcallback; ?>") 
}






</script>
<!-- ================== END PAGE LEVEL JS ================== -->