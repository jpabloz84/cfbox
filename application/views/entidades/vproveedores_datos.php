
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-wizard/css/bwizard.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/parsley/src/parsley.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/jquery-tag-it/css/jquery.tagit.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<style type="text/css">
.form-horizontal .form-group {
  margin-right: 0px; 
    margin-left: 0px;
}
</style>

<!-- begin #content -->
<div id="content" class="content">    
    <input type="hidden" id="id_empresa" value="<?=$visitante->get_id_empresa();?>">
    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-md-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    </div>
                    <h4 class="panel-title">Proveedores Alta/Edición</h4>
                </div>
                <div class="panel-body" id="panel-body">
                    <form class="form-horizontal" enctype="multipart/form-data" id="formuploadajax" method="post" name="form-wizard">
                        <input type="hidden" name="id_persona" id="id_persona" value="<?=$id_persona?>">                          
                        <input type="hidden" name="id_proveedor" id="id_proveedor" value="<?=$id_proveedor?>">                          
                           <input type="hidden" id="modo" name="modo" value="<?=$modo?>">  
                        <div id="wizard">
                            <ol>
                                <li id="paso1" >
                                    PASO 1
                                    <small>Datos personales</small>
                                </li>
                                <li  id="paso2" >
                                    PASO 2
                                    <small>Datos Administrativos</small>
                                </li>
                                <li id="paso3" >
                                    PASO 3
                                    <small>Datos del proveedor</small>
                                </li>
                                <li id="paso4" >
                                    PASO 4
                                    <small>Envio de formulario y confirmación de recepción</small>
                                </li>
                            </ol>
                            <!-- begin wizard step-1 -->
                            <div class="wizard-step-1">
                                <fieldset>
                                    <legend class="pull-left width-full">Datos personales</legend>
                                    <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-3 -->
                                        <div class="col-md-2" >
                                            <div class="form-group block1">
                                                <label>Tipo de persona</label>
                                                <div class="controls">                                                
                                                <select class="form-control" data-parsley-group="wizard-step-2"  name="inp_tipo_persona" id="inp_tipo_persona">
                                                    <option value="F">FISICA</option>
                                                    <option value="J" selected="selected">JURIDICA</option>
                                                </select>
                                               </div>                                                
                                            </div>
                                          
                                        </div>
                                        <!-- end col-3 -->  
                                        <!-- begin col-3 -->
                                        <div class="col-md-4" style="padding:0 0px">
                                            <div class="form-group" >
                                                <label>Nombre(*)</label>
                                                <div class="controls">                                                
                                                <input type="text" name="inp_nombres" id="inp_nombres" placeholder="Ingrese los nombres" class="form-control" data-parsley-group="wizard-step-1" required />
                                                </div>
                                            </div>
                                           
                                        </div>
                                        <!-- end col-3 -->                                      
                                        <div class="col-md-4">
                                             <div class="form-group" style="margin-left:0px">
                                                <label>Apellido</label>
                                                <div class="controls">                                                
                                                <input type="text" name="inp_apellido" id="inp_apellido" placeholder="Ingrese apellido" class="form-control" data-parsley-group="wizard-step-1"  />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tipo documento</label>
                                                <div class="controls">
                                                    <select id="inp_tipo_docu" name="inp_tipo_docu" class="form-control" data-parsley-group="wizard-step-1" required>                                                       
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
                                                <label>Nro. documento</label>
                                                <div class="controls">
                                                   <input type="text" name="inp_nro_docu" id="inp_nro_docu" placeholder="00000000" class="form-control" data-parsley-group="wizard-step-1" data-parsley-type="number"  />                                                
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->  

                                          <div class="col-md-2" >
                                            <div class="form-group" >
                                                <label>Sexo</label>
                                                <div class="controls">
                                                    <label class="radio-inline">
                                                    <input type="radio" name="sexoRadios" value="M"   />
                                                    Masculino
                                                    </label>
                                                 </div>
                                             </div>
                                             <div class="form-group" >
                                                <div class="controls">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="sexoRadios" value="F" />
                                                        Femenino
                                                    </label>
                                                </div>
                                             </div>                                           
                                        </div>

                                         <div class="col-md-2" >
                                            <div class="form-group" >
                                           <label>Cod. Postal</label>
                                                <div class="controls">
                                                   <input type="text" name="inp_cp" id="inp_cp" placeholder="Cp 1000" class="form-control" data-parsley-group="wizard-step-1" data-parsley-type="number"  />                                                
                                                </div>               
                                           </div>                           
                                        </div>

                                    </div>
                                    <!-- end row -->
                                     <!-- begin row -->
                                    <div class="row">                                        
                                         <!-- begin col-2-->
                                        <div class="col-md-2">
                                            <div class="form-group">    
                                                <label>Provincia de residencia</label>                                           
                                                <div class="controls">
                                                    <select id="inp_provincia" name="inp_provincia" onchange="cargar_localidades('inp_localidad')" class="form-control">
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
                                        <div class="col-md-2">                                            
                                            <div class="form-group">    
                                                <label>Localidad de residencia</label>
                                                <div class="controls" id="ctrllocalidad">
                                                     <select class="combobox" id="inp_localidad" name="inp_localidad" data-parsley-group="wizard-step-1"  required>
                                                        <option value=""></option>    
                                                     </select>                                                  
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->   
                                        <!-- begin col-3 -->
                                        <div class="col-md-2">
                                            <div class="form-group">    
                                                <label>Calle (*)</label>
                                                <div class="controls">
                                                 <input type="text" name="inp_calle" id="inp_calle" class="form-control" data-parsley-group="wizard-step-1" required="">
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="col-md-2">
                                            
                                            <div class="form-group">    
                                                <label>nro.(*)</label>
                                                <div class="controls">
                                                 <input type="text" name="inp_nro" id="inp_nro" class="form-control" data-parsley-group="wizard-step-1" required="" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">    
                                                <label>Piso</label>
                                                <div class="controls">
                                                 <input type="text" name="inp_piso" id="inp_piso" class="form-control" data-parsley-group="wizard-step-1">
                                                </div>
                                            </div>                                            
                                        </div>
                                        <!-- end col-3 -->
                                        <div class="col-md-2">
                                             <div class="form-group">    
                                                <label>Dpto</label>
                                                <div class="controls">
                                                 <input type="text" name="inp_dpto" id="inp_dpto" class="form-control" data-parsley-group="wizard-step-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->
                                     <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-3 -->
                                        <div class="col-md-1" >
                                            <div class="form-group" >
                                                <label>Car. tel.</label>                                            
                                                <div class="controls">
                                                      <input type="text" name="inp_car_tel" id="inp_car_tel" placeholder="sin 0 al comienzo" class="form-control" required="" data-parsley-group="wizard-step-1" data-parsley-length="[2, 5]" maxlength="5">
                                                </div>                                                
                                            </div>                                           
                                        </div>
                                        <!-- end col-3 -->
                                        <div class="col-md-2">
                                             <div class="form-group" >
                                                <label>nro. telefono</label>                                            
                                                <div class="controls">
                                                      <input type="text" name="inp_nro_tel" id="inp_nro_tel" placeholder="completar sin el 15" class="form-control" required="" data-parsley-group="wizard-step-1" data-parsley-length="[4, 8]" maxlength="8">
                                                </div>                                                
                                            </div>
                                        </div>
                                         <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Prov. de nac. (no obligatorio)</label>                                            
                                                <div class="controls">
                                                    <select id="inp_provincia_naci" name="inp_provincia_naci" onchange="cargar_localidades('inp_localidad_naci')" class="form-control">
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
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Loc. de Nac. (no obligatorio)</label>
                                                <div class="controls" id="ctrllocalidad_naci">
                                                         <select class="combobox" id="inp_localidad_naci" name="inp_localidad_naci" data-parsley-group="wizard-step-1"  >
                                                            <option value=""></option>    
                                                         </select>                                                  
                                                </div>
                                            </div>
                                        </div>
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Fe. de nacimiento(no obligatorio)</label>
                                                <div class="controls">
                                                 <input type="text"  name="inp_fe_naci" id="inp_fe_naci" placeholder="dd/mm/yyyy" class="form-control" data-parsley-group="wizard-step-1"  />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                    </div>
                                    <!-- end row -->
                                </fieldset>
                            </div>
                            <!-- end wizard step-1 -->
                            <!-- begin wizard step-2 -->
                            <div class="wizard-step-2">
                                <fieldset>
                                    <legend class="pull-left width-full">Datos Administrativos</legend>
                                    <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-6 -->
                                        <div class="col-md-2" >
                                            <div class="form-group block1">
                                                <label>Auspiciante</label>
                                                <div class="controls">                                                
                                                <input type="checkbox" class="form-control"  id="inp_auspiciante" name="inp_auspiciante" checked="checked" />
                                               </div>                                                
                                            </div>
                                           
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Cuit (Cuil para personas juridicas)</label>
                                                <div class="controls">                         
                                                <input type="text" name="inp_cuit" id="inp_cuit"  placeholder="ingrese cuit" class="form-control" data-parsley-group="wizard-step-2"   data-parsley-type="number" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-6 -->
                                        <div class="col-md-7">
                                          <div class="controls">
                                                  <div class="form-group">
                                                    <label>Observaciones</label>
                                                    <div class="controls">
                                                    <input  type="text" name="inp_observaciones" id="inp_observaciones" class="form-control" data-parsley-group="wizard-step-2"  />
                                                   </div>
                                                </div>                                              
                                           </div>
                                        </div>
                                        <!-- end col-6 -->                                                 
                                    </div>
                                    <!-- end row -->
                                </fieldset>
                            </div>
                            <!-- end wizard step-2 -->                            
                            <!-- begin wizard step-3 -->
                            <div class="wizard-step-3">
                                <fieldset>
                                    <legend class="pull-left width-full">Datos del proveedor</legend>
                                    <!-- begin row -->
                                    <div class="row"> 
                                         <!-- begin col-4 -->
                                        <div class="col-md-4">
                                            <div class="form-group">    
                                                <label>Email (no obligatorio)</label>
                                                <div class="controls">
                                                 <input type="text" data-parsley-type="email" name="inp_email" id="inp_email" placeholder="correo@email.com" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-4 -->
                                        <!-- begin col-4 -->
                                        <div class="col-md-4">
                                            <div class="form-group">    
                                                <label>Imagen de perfil (no obligatorio)</label>
                                                <div class="controls">
                                                    <input type="file" class="form-control"  name="inp_img_profile" id="inp_img_profile" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-4 -->
                                        <!-- begin col-4 -->
                                        <div class="col-md-4">
                                            <div class="form-group">    
                                                <label>Nombre fantasia</label>
                                                <div class="controls">
                                                    <input type="text"  name="inp_proveedor" id="inp_proveedor" placeholder="ingrese nombre" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-4 -->                                        
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
                                        <?php if($callback!="")
                                        {
                                            echo "<a class='btn btn-warning btn-lg' role='button' href='#'  href='javascript:;' onclick='REGRESAR(\"$callback\")'>REGRESAR</a>";
                                        }
                                        
                                            echo "<a class='btn btn-success btn-lg'  href='javascript:;' onclick='recargar()'  >CONTINUAR CARGANDO</a>";
                                        
                                        ?>
                                        
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
                                    <a href="javascript:;" class="btn btn-sm btn-white"  id="cancel-menssaje" onclick="set_persona()">Continuar con los datos que estoy ingresando y reemplazarlos</a>
                                    <a href="javascript:;" class="btn btn-sm btn-primary" id="cargar-menssaje">Usar los datos encontrados en la base de datos</a>
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
</div>
<!-- end #content -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/javascript">   

var green = '#00acac',
red = '#ff5b57',
blue = '#348fe2',
purple = '#727cb6',
orange = '#f59c1a',
black = '#2d353c';


App.restartGlobalFunction();
App.setPageTitle('Alta/ Edición de Proveedores | Coffe box app');
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;
var cbconsultorios;
    

$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
        $.getScript('<?=BASE_FW?>assets/plugins/switchery/switchery.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {
                  $.getScript('<?=BASE_FW?>assets/plugins/jquery-tag-it/js/tag-it.min.js').done(function() {
                    
                    
                    if($("#modo").val()=="M")    
                    {
                     var cmp=Array("id_proveedor","proveedor","observaciones","id_persona","apellido","nombres",  "documento","strnombrecompleto","car_tel","nro_tel","calle","nro","piso","dpto","cp","auspiciante","nro_docu","tipo_docu","cuit","tipo_persona","tipo_persona_desc",  "sexo",  "email","id_loc_nac","descripcion_loc_nac","DATE_FORMAT(fe_nacimiento,'%d/%m/%Y') as fe_nacimiento","id_pro_nac","descripcion_pro_nac","id_loc","descripcion_loc","id_pro","descripcion_pro","img_personal")
                     spinnerStart($("#panel-body"))
                     ofwlocal.getAsync('verproveedores',cmp,'id_proveedor='+$('#id_proveedor').val()+" and id_empresa="+$("#id_empresa").val(),'',modificarload)
                     
                    }else
                    {
                    cargar_localidades('inp_localidad');
                    cargar_localidades('inp_localidad_naci');                    
                    }
                    $("#inp_cuit").keypress(function(e){
                        return teclaentero(e)
                    }) 
                    $("#inp_nro_docu").keypress(function(e){
                        return teclaentero(e)
                    }) 
                   

                    $("#inp_fe_naci").mask("99/99/9999");
                    $('#inp_fe_naci').datepicker({
                         todayHighlight: true,
                         format: 'dd/mm/yyyy',
                         language: 'es',
                         autoclose: true
                    });
                        resetInputsPersona()
                    $("#cargar-menssaje").click(function(){
                    $("[id='modal-details']").modal("hide")
                        cargar_datos_persona(arrPersona[0])
                     }) 
                     $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/proveedores/");
                         })

                  });
                });//datepicker es
              });//datepicker js
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


function modificarload(res){
    for (idx in res)
    {
        $("#inp_apellido").val(res[idx].apellido)
        $("#inp_nombres").val(res[idx].nombres)
        $("#inp_proveedor").val(res[idx].proveedor)
        $("#inp_nro_docu").val(res[idx].nro_docu)
        $("#inp_tipo_docu").val(res[idx].tipo_docu)
        $("#inp_email").val(res[idx].email)
        $("input:radio[name='sexoRadios']").filter("[value='"+res[idx].sexo+"']").attr('checked', true);
        $("#inp_car_tel").val(res[idx].car_tel)
        $("#inp_nro_tel").val(res[idx].nro_tel)
        $("#inp_cp").val(res[idx].cp)        
        $("#inp_calle").val(res[idx].calle)        
        $("#inp_nro").val(res[idx].nro)        
        $("#inp_piso").val(res[idx].piso)        
        $("#inp_auspiciante").prop("checked",res[idx].auspiciante==1)        
        $("#inp_dpto").val(res[idx].dpto)        
        $("#id_persona").val(res[idx].id_persona)
        $('#inp_cuit').val(res[idx].cuit);                    
        $('#inp_tipo_persona').val(res[idx].tipo_persona);
        
        var id_pro_nac=res[idx].id_pro_nac
        $('#inp_provincia_naci').val(res[idx].id_pro_nac);
        $('#inp_fe_naci').val(res[idx].fe_naci) 
        $('#inp_observaciones').val(res[idx].observaciones) 
        //$('#inp_img_profile').val(res[idx].img_personal);
        $('#inp_img_profile').val(""); //no poner archivos de servidor aca
        //$('#inp_condiciones').val(res[idx].id_cond_iva) 
        
        if(res[idx].id_pro != null)
        {
        $('#inp_provincia').val(res[idx].id_pro);
        cargar_localidades('inp_localidad',res[idx].id_loc)
        }
        var id_loc_nac=res[idx].id_loc_nac
        if(id_pro_nac != null)
        {
        $('#inp_provincia_naci').val(id_pro_nac);
        cargar_localidades('inp_localidad_naci',id_loc_nac)
        }

    }
    spinnerEnd($("#panel-body"))

}

function set_persona()
{
    $("[id='modal-details']").modal("hide")    
    $("#id_persona").val(arrPersona[0].id_persona)
}


function evalpaso(paso)
{

var mensaje=""
    if(paso==0)
    {
        var sexo=$('input[name=sexoRadios]:checked').val();
        mensaje=mensaje+(((sexo=="")?"No ha completado el campo sexo<br/>":""))        
        mensaje=mensaje+((($("#inp_localidad").val()=="")?"No ha completado la localidad<br/>":""))
        
        if(mensaje.trim()!="")
        {
            $('[id="mensaje-titulo"]').html("Atención: Revise los siguientes campos")
                   $('[id="mensaje-body"]').html(mensaje)
                   $('[id="mensaje-cancel-modal"]').modal("show"); 
                   
                   return false
        }
          return buscar_similitudes();
    
    }//paso2

    /*if(paso==1) //paso 1 no hay nada que evaluar por ahora
    {   
        
    }*/


    if(paso==2)
    {    
     var proveedor=$('#inp_proveedor').val();
        mensaje=mensaje+(((proveedor=="")?"No ha ingresado el nombre fantasia del proveedor<br/>":""))
        
        if(mensaje.trim()!="")
        {
            $('[id="mensaje-titulo"]').html("Atención: Revise los siguientes campos")
                   $('[id="mensaje-body"]').html(mensaje)
                   $('[id="mensaje-cancel-modal"]').modal("show"); 
                   
                   return false
        }
    return enviardatos()
    }//paso3
    
    
return true
}

var enviando_datos=false

var strxml=""
function enviardatos()
{

$("#titulo-confirmacion").html("")
$("#body-confirmacion").html("")
            var formData = new FormData(document.getElementById("formuploadajax"));            
            var mensajeErrors=""
                    strxml="<body><extras>"
                    var tipo_persona=$("#inp_tipo_persona").val()
                    var nombres=$("#inp_nombres").val()
                    var apellido=$("#inp_apellido").val()                 
                    var tipo_docu=$('#inp_tipo_docu').val();
                    var nro_docu=$('#inp_nro_docu').val();
                    var cuit=$('#inp_cuit').val();
                    var sexo=$('input[name=sexoRadios]:checked').val();
                    var localidad=$('#inp_localidad').val();
                    var proveedor=$('#inp_proveedor').val();
                    var localidad_naci=$('#inp_localidad_naci').val();
                    var auspiciante=($('#inp_auspiciante').prop("checked")?1:0);
                    //var condicioniva=$('#inp_condiciones').val();
                                        
                    if(tipo_persona=="F"){
                        if(tipo_docu =="")                    
                        mensajeErrors+="<li>No completo el campo de tipo de documento</li>";                                     

                        if(nro_docu =="")                    
                        mensajeErrors+="<li>No seleccionó un numero de documento</li>";

                        if(apellido =="")                    
                        mensajeErrors+="<li>No ingresó apellido</li>";
                    }

                

                 

                 if(nombres =="")                    
                     mensajeErrors+="<li>No ingresó un nombre</li>";

                 if(localidad =="")                    
                     mensajeErrors+="<li>No seleccionó una localidad</li>";
                 
                 /*if(localidad_naci =="")                    
                     mensajeErrors+="<li>No seleccionó una localidad de nacimiento</li>";*/

                 

                 if(proveedor =="")                    
                     mensajeErrors+="<li>No ingresó un nombre fantasia para el proveedor</li>";   

                 /*if(condicioniva =="")                    
                     mensajeErrors+="<li>No ingresó una condición ante iva del proveedor</li>";   */
                 

                 if(mensajeErrors!="")
                 {
                   $('[id="mensaje-titulo"]').html("Atención: Revise los siguientes campos")
                   $('[id="mensaje-body"]').html(mensajeErrors)
                   $('[id="mensaje-cancel-modal"]').modal("show");
                   return false;
                 }
                 strxml+="</extras></body>"
                 
                formData.append('extra',strxml)
if(enviando_datos)return false;

$("#titulo-message-confirmar").html("CONFIRMAR DATOS")
$("#body-message-confirmar").html("<div class='alert alert-success m-b-0' ><h6><i class='fa fa-info-circle'></i>¿Seguro que desea enviar este formulario?</h6></div>")
$('[id="modal-message-confirmar"]').modal("show")

    $("#confirm-evalue").click(function()
    {


        if(enviando_datos)return;
        $('[id="modal-message-confirmar"]').modal("hide")
        event.stopPropagation();
        enviando_datos=true;


        $.ajax({url:'<?=base_url()?>index.php/entidades/proveedores/save',
                    type: "post",
                    dataType: "json",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function(){
                        spinnerStart($('#panel-body'));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body'));
                    }
                    })
                    .done(function(response){
        
                        
                         
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;
                        
                        if(numError == 0)
                        {   
                            $("#inp_apellido").val("")                 
                            $("#inp_nombres").val("")                    
                            $("#inp_proveedor").val("")                    
                            $("#inp_nro_docu").val("") 
                            $("#inp_domicilio").val("")                    
                            $('#inp_tipo_docu').val(1);
                            $('#inp_nro_docu').val("");                    
                            $('#inp_cuit').val("");                    
                            $('#inp_tipo_persona').val("J");                            
                            $('input[name=sexoRadios]').each(function(e){                                
                            $(this).prop("checked", false);    
                            });
                            
                            $('#inp_provincia').val(12);
                            $('#inp_provincia_naci').val(12);
                            $('#inp_email').val("");                                                
                            $("#inp_car_tel").val("")                        
                            $("#inp_nro_tel").val("")                        
                            $("#inp_nro").val("")                        
                            $("#inp_cp").val("")                        
                            $("#inp_auspiciante").prop("checked",true)
                            $('#inp_calle').val("") 
                            $('#inp_piso').val("") 
                            $('#inp_dpto').val("") 
                            $('#inp_fe_naci').val("") 
                            $('#inp_observaciones').val("") 
                            $('#inp_localidad_naci').find("option").remove().end();
                            $('#inp_img_profile').val("");
                            

                            $("#confirm-evalue").off();
                            if($("#modo").val()=="A")
                            {
                            $("#titulo-confirmacion").html("El proveedor se creó correctamente")    
                            }else
                            {
                                $("#titulo-confirmacion").html("El proveedor se modificó correctamente")    
                            }
                            
                            $("#body-confirmacion").html("")
                            $("#confirm-evalue").off();
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

        })
return true

}//enviar datos

var arrPersona=Array();
function buscar_similitudes()
{
    
if($("#inp_tipo_persona").val()!="J"){
    var nro_docu=$("#inp_nro_docu").val();
    var tipo_docu=$("#inp_tipo_docu").val();
    var sexo=$('input[name=sexoRadios]:checked').val();
    var exito=true
    //$("#panel-loader").modal("show")
    spinnerStart($('#panel-body'));
    if($("#modo").val()=="M")
    {
    var cmp=Array('id_persona','apellido','nombres','calle','nro','piso','dpto','car_tel','nro_tel','cp','id_loc','tipo_docu','nro_docu','sexo','email','id_pro','id_loc_nac','id_pro_nac','DATE_FORMAT(fe_nacimiento,"%d/%m/%Y") as fe_naci','tipo_persona','cuit','email','img_personal')
    arrPersona=ofw.get('verpersonas',cmp,'nro_docu='+nro_docu+' and sexo="'+sexo+'" and tipo_docu='+tipo_docu);  
        if(arrPersona.length>0)
        {var trow=""
            if(arrPersona[0].id_persona!=$("#id_persona").val())
            {$("#id_persona").val(arrPersona[0].id_persona)
                for (l in arrPersona)
                {per=arrPersona[l]
                 nro_docu= per.nro_docu         
                 apellido= per.apellido
                 nombres= per.nombres
                 trow+="<p>La persona "+per.documento+""+nro_docu+" - "+apellido+", "+nombres+" existe en el sistema y no se puede modificar en esta instancia</p>";
                }  
            $('[id="mensaje-titulo"]').html("Atención")
            $('[id="mensaje-body"]').html(trow)                    
            $("[id='mensaje-cancel-modal']").modal("show")    
            exito=false
            }
            
        }
    }

    if($("#modo").val()=="A")
    {
    var cmp=Array('id_persona','apellido','nombres','domicilio','telefono','id_loc','tipo_docu','nro_docu','sexo','email','id_pro','id_loc_nac','id_pro_nac','DATE_FORMAT(fe_nacimiento,"%d/%m/%Y") as fe_naci','tipo_persona','cuit','email')
    arrPersona=ofwlocal.get('verpersonas',cmp,'nro_docu='+nro_docu+' and sexo="'+sexo+'" and tipo_docu='+tipo_docu);  
        if(arrPersona.length>0)
        {var trow=""
            if(arrPersona[0].id_persona!=$("#id_persona").val())
            {
                for (l in arrPersona)
                {per=arrPersona[l]
                 nro_docu= per.nro_docu         
                 apellido= per.apellido
                 nombres= per.nombres
                 trow+="<tr><td>"+nro_docu+"</td><td>"+apellido+", "+nombres+" <td></tr>";
                }  
            $("#modal-details-body > tbody").html(trow)                    
            $("[id='modal-details']").modal("show")    
            }
            exito=true
        }else
        {
            $("#id_persona").val(0)
            exito=true
        }

    }

    
//fin de persona juridica    
}else{
var inp_nombres=$("#inp_nombres").val();
     if($("#modo").val()=="A")
    {
    var cmp=Array('id_persona','apellido','nombres','domicilio','telefono','id_loc','tipo_docu','nro_docu','sexo','email','id_pro','id_loc_nac','id_pro_nac','DATE_FORMAT(fe_nacimiento,"%d/%m/%Y") as fe_naci','tipo_persona','cuit','email')
    arrPersona=ofwlocal.get('verpersonas',cmp,'nombres="'+inp_nombres+'" and tipo_persona="J"');  
        if(arrPersona.length>0)
        {   var trow=""
            if(arrPersona[0].id_persona!=$("#id_persona").val())
            {
                for (l in arrPersona)
                {per=arrPersona[l]
                 nro_docu= per.nro_docu         
                 apellido= per.apellido
                 nombres= per.nombres
                 trow+="<tr><td>"+nro_docu+"</td><td>"+apellido+", "+nombres+" <td></tr>";
                }  
            $("#modal-details-body > tbody").html(trow)                    
            $("[id='modal-details']").modal("show")    
            }
            exito=true
        }else
        {
            $("#id_persona").val(0)
            exito=true
        }

    }

}//fin de persona fisica

    

spinnerEnd($('#panel-body'));
//$("#panel-loader").modal("hide")
return exito;
}



function cargar_datos_persona(oPersona)
{
    
$("#id_persona").val(oPersona.id_persona)
$("#inp_nro_docu").val(oPersona.nro_docu)
$("#inp_tipo_docu").val(oPersona.tipo_docu)
$("input:radio[name='sexoRadios']").filter("[value='"+oPersona.sexo+"']").attr('checked', true);
$("#inp_nombres").val(oPersona.nombres)
$("#inp_apellido").val(oPersona.apellido)
$("#inp_tipo_persona").val(oPersona.tipo_persona)


if(oPersona.email!=null)
$("#inp_email").val(oPersona.email)

if(oPersona.telefono!=null)
$("#inp_telefono").val(oPersona.telefono)

if(oPersona.domicilio!=null)
$("#inp_domicilio").val(oPersona.domicilio)

if(oPersona.fe_naci!=null)
$("#inp_fe_naci").val(oPersona.fe_naci)

if(oPersona.cuit!=null)
$("#inp_cuit").val(oPersona.cuit)


if(oPersona.id_pro != null)
{
$("#inp_provincia").val(oPersona.id_pro)
cargar_localidades('inp_localidad',oPersona.id_loc)
}

if(oPersona.id_pro_nac != null)
{
$("#inp_provincia_naci").val(oPersona.id_pro_nac)
cargar_localidades('inp_localidad_naci',oPersona.id_loc_nac)    
}



}


function cargar_localidades(id_input,id_loc='')
{
var id_pro=(id_input=='inp_localidad')?$("#inp_provincia").val():$("#inp_provincia_naci").val()
if(id_pro=="")
{
    return
}
//$("#provincia_desc").val($("#provincia option:selected" ).text());
var cmp=Array('id_loc','descripcion_loc')
var arr=ofw.getAsync('localidades',cmp,'id_pro='+id_pro,'descripcion_loc asc', function(e){cargarCombo(e,id_input,id_loc)});  

}


function cargarCombo(arr,id_input,defaultValue)
{
if(id_input=='inp_localidad')
{
    $("#ctrllocalidad").html("<select class='combobox' id='"+id_input+"' name='"+id_input+"' data-parsley-group='wizard-step-1'  required><option value=''></option></select>")
}

if(id_input=='inp_localidad_naci')
{
    $("#ctrllocalidad_naci").html("<select class='combobox' id='"+id_input+"' name='"+id_input+"' data-parsley-group='wizard-step-1' ><option value=''></option></select>")
}

    for (l in arr)
    {loc=arr[l]
       id_loc= loc.id_loc
       descripcion= loc.descripcion_loc
       if(id_loc==defaultValue)
        {    
        $('#'+id_input).append('<option value="'+id_loc+'" selected="selected">'+descripcion+'</option>');
        }else
        {
        $('#'+id_input).append('<option value="'+id_loc+'">'+descripcion+'</option>');
        }
        
    }
    $("#"+id_input).combobox();
}



function recargar()
{
    handleLoadPage("#<?php echo base_url(); ?>index.php/entidades/proveedores/ingresar_alta") 
}

function REGRESAR(url)
{
    handleLoadPage("#"+url) 
}


$("#tipo_persona").change(function(){
    
    resetInputsPersona()
    
})

function resetInputsPersona(){
var tipo_persona=$("#inp_tipo_persona").val()
if(tipo_persona=="J"){
$("#inp_apellido").prop("disabled",true)
$("#inp_nro_docu").prop("disabled",true)
$("#inp_tipo_docu").prop("disabled",true)
$("#inp_provincia_naci").prop("disabled",true)
$("input[name='sexoRadios']").prop("disabled",true)
$("#ctrllocalidad_naci").hide();

$("#inp_fe_naci").prop("disabled",true)
}

if(tipo_persona=="F"){
$("#inp_apellido").prop("disabled",false)
$("#inp_nro_docu").prop("disabled",false)
$("#inp_tipo_docu").prop("disabled",false)
$("#inp_provincia_naci").prop("disabled",false)
$("input[name='sexoRadios']").prop("disabled",false)
$("#ctrllocalidad_naci").show();
$("#inp_fe_naci").prop("disabled",false)
}

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->