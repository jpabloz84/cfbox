
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

<script type="text/template" id="tpl-table-list">     
    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%" style="color:#707478">
                            <thead>
                                <tr>                                    
                                    <th>NOMBRE</th>                                        
                                    <th>DOCUMENTO</th>                                        
                                    <th>CUIT</th>
                                    <th>CONDICION IVA</th>
                                    <th>-</th>                                        
                                </tr>
                            </thead>
                            <tbody>                                                               
        <% _.each(ls, function(elemento) {  
        
        var strdocumento=elemento.get('documento') + " -" + elemento.get('nro_docu')
        

        %>
        <tr>
            <td><%=elemento.get('strnombrecompleto')%></td>                    
            <td><%=strdocumento%></td>
            <td><%=elemento.get('cuit') %></td>        
            <td><%=elemento.get('condicion') %></td>                    
            <td>
                <button type="button" class="btn btn-xs btn-primary" name='selecItem' id="seleccionar-<%=elemento.cid %>">
                    agregar
                    <i class="fa fa-arrow-right"></i>
                </button>            
            </td>
        </tr>
        <% }); %>
        </tbody>
    </table>    
</script>


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
                       
                        
                        
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    </div>
                    <h4 class="panel-title">Clientes Alta/Edición</h4>
                </div>
                <div class="panel-body" id="panel-body">
                     <div class="row">
                        <form class="form-inline pull-left" role="search" >
                            <div class="form-group">
                              <label for="chk-data-basic">solo datos basicos
                                <input type="checkbox"  id="chk-data-basic" class="form-control" />    
                                </label> 
                            </div>
                        </form>
                           
                     </div>
                    <form class="form-horizontal" enctype="multipart/form-data" id="formuploadajax" method="post" name="form-wizard" style="display: none">
                        <input type="hidden" name="id_persona" id="id_persona" value="<?=$id_persona?>">    <input type="hidden" name="id_empresa" id="id_empresa" value="<?=$visitante->get_id_empresa()?>">                      
                        <input type="hidden" name="id_cliente" id="id_cliente" value="<?=$id_cliente?>">                          
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
                                    <small>Datos del cliente</small>
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
                                        <div class="col-md-3" >
                                            <div class="form-group" >
                                                <label>Nombre (*)</label>
                                                <div class="controls">                                                
                                                <input type="text" name="inp_nombres" id="inp_nombres" placeholder="Ingrese los nombres" class="form-control" data-parsley-group="wizard-step-1" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->  
                                        <!-- begin col-3 -->
                                        <div class="col-md-3" style="padding:0 0px">
                                            <div class="form-group" style="margin-left:0px">
                                                <label>Apellido (solo personas fisicas)(*)</label>
                                                <div class="controls">                                                
                                                <input type="text" name="inp_apellido" id="inp_apellido" placeholder="ingrese apellido" class="form-control" data-parsley-group="wizard-step-1" required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-2 -->                                      
                                        <!-- begin col-2 -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Tipo documento (*)</label>
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
                                        <!-- end col-2 -->
                                        <!-- begin col-3 -->
                                        <div class="col-md-2">
                                            <div class="form-group" >
                                                <label>Nro. documento (*)</label>
                                                <div class="controls">
                                                   <input type="text" name="inp_nro_docu" id="inp_nro_docu" placeholder="" class="form-control" data-parsley-group="wizard-step-1" data-parsley-type="number" required />                                                
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                         <!-- begin col-2 -->
                                        <div class="col-md-2" >
                                            <div class="form-group" >
                                                <label>Sexo (*)</label>
                                                <div class="controls">
                                                    <label class="radio-inline">
                                                    <input type="radio" name="sexoRadios" value="M"   />
                                                    varon
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="sexoRadios" value="F" />
                                                        mujer
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->                                        
                                    </div>
                                    <!-- end row -->

                                     <!-- begin row -->
                                    <div class="row">
                                         <!-- begin col-2 -->
                                        <div class="col-md-2">
                                            <div class="form-group">    
                                                <label>Provincia de residencia</label>                                            
                                                <div class="controls">
                                                    <select id="inp_provincia" name="inp_provincia" onchange="cargar_localidades('inp_localidad')" class="form-control" data-parsley-group="wizard-step-1">
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
                                        <!-- end col-2 -->     
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Localidad de residencia (*)</label>                                            
                                                <div class="controls" id="ctrllocalidad">
                                                         <select class="combobox" id="inp_localidad" name="inp_localidad" data-parsley-group="wizard-step-1"  required>
                                                            <option value=""></option>    
                                                         </select>                                                  
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-2 -->   
                                         <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Calle (*)</label>
                                                <div class="controls">
                                                 <input type="text"  name="inp_calle" id="inp_calle" class="form-control" data-parsley-group="wizard-step-1"  required />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>nro.(*)</label>
                                                <div class="controls">
                                                 <input type="text"  name="inp_nro" id="inp_nro"  class="form-control" data-parsley-group="wizard-step-1"  required value="0" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                        <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>Piso</label>
                                                <div class="controls">
                                                 <input type="text"  name="inp_piso" id="inp_piso"  class="form-control" data-parsley-group="wizard-step-1"   />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                         <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>Dpto</label>
                                                <div class="controls">
                                                 <input type="text"  name="inp_dpto" id="inp_dpto"  class="form-control" data-parsley-group="wizard-step-1"   />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                         <!-- begin col-1 -->
                                        <div class="col-md-1">
                                            <div class="form-group">    
                                                <label>Cod. postal</label>
                                                <div class="controls">
                                                 <input type="text"  name="inp_cp" id="inp_cp"  class="form-control" data-parsley-group="wizard-step-1"    />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                       
                                    </div>
                                    <!-- end row -->
                                     <!-- begin row -->
                                    <div class="row">
                                           <!-- begin col-1 -->
                                        <div class="col-md-1" >
                                            <div class="form-group" >
                                                <label>Car. Tel (*)</label>                                            
                                                <div class="controls">
                                                    
                                                      <input type="text" name="inp_car_tel" id="inp_car_tel" placeholder="sin 0 al comienzo" class="form-control"   data-parsley-group="wizard-step-1" data-parsley-length="[2, 5]" maxlength="5"  required />
                                                </div>                                                
                                            </div>
                                        </div>
                                        <!-- end col-1 -->
                                        <!-- begin col-2 -->
                                        <div class="col-md-2" >
                                            <div class="form-group" >
                                                <label>Nro. Tel. (*)</label>                                            
                                                <div class="controls">                                                    
                                                      <input type="text" name="inp_nro_tel" id="inp_nro_tel" placeholder="completar sin el 15" class="form-control"  data-parsley-group="wizard-step-1" data-parsley-length="[4, 8]" maxlength="8" required />
                                                </div>                                                
                                            </div>
                                        </div>
                                        <!-- end col-2 -->
                                         <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Provincia de nacimiento</label>                                            
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
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Localidad de Nacimiento</label>                                            
                                                <div class="controls" id="ctrllocalidad_naci">
                                                         <select class="combobox" id="inp_localidad_naci" name="inp_localidad_naci" data-parsley-group="wizard-step-1"  >
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
                                                 <input type="text"  name="inp_fe_naci" id="inp_fe_naci" placeholder="dd/mm/yyyy" class="form-control" data-parsley-group="wizard-step-1"   />
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
                                        <div class="col-md-2" style="padding:0 0px">
                                            <div class="form-group block1">
                                                <label>Tipo de persona</label>
                                                <div class="controls">                                                
                                                <select class="form-control" data-parsley-group="wizard-step-2"  name="inp_tipo_persona" id="inp_tipo_persona">
                                                    <option value="F">FISICA</option>
                                                    <option value="J">JURIDICA</option>
                                                </select>
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

                                         <!-- begin col-5 -->
                                        <div class="col-md-7">
                                            <div class="form-group" >    
                                                <label id="txthabilitado">Habilitado</label>                                            
                                                <div class="controls"  id="ctrlhabilitado">
                                                      <input type="checkbox" data-render="switchery" data-theme="default" checked  id="habilitado" name="habilitado"/>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->

                                    </div>
                                    <!-- end row -->
                                    <!-- begin row -->
                                    <div class="row">
                                        <!-- begin col-6 -->
                                        <div class="col-lg-12">
                                          <div class="controls">
                                                  <div class="form-group">
                                                    <label>Observaciones</label>
                                                    <div class="controls">
                                                   <!-- <input  type="text" name="inp_observaciones" id="inp_observaciones" class="form-control" data-parsley-group="wizard-step-2"  />-->
                                                    <textarea class="form-control" rows="5"  data-parsley-group="wizard-step-2" name="inp_observaciones" id="inp_observaciones"></textarea>
                                                   </div>
                                                </div>                                              
                                           </div>
                                        </div>
                                        <!-- end col-6 -->                                                 
                                    </div>
                                </fieldset>
                            </div>
                            <!-- end wizard step-2 -->                            
                            <!-- begin wizard step-3 -->
                            <div class="wizard-step-3">
                                <fieldset>
                                    <legend class="pull-left width-full">Datos del cliente</legend>
                                    <!-- begin row -->
                                    <div class="row"> 
                                         <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Email</label>
                                                <div class="controls">
                                                 <input type="text" data-parsley-type="email" name="inp_email" id="inp_email" placeholder="correo@email.com" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-3 -->
                                        <div class="col-md-3">
                                            <div class="form-group">    
                                                <label>Imagen de perfil</label>
                                                <div class="controls">
                                                    <input type="file" class="form-control"  name="inp_img_profile" id="inp_img_profile" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-3 -->
                                        <!-- begin col-2 -->
                                        <div class="col-md-2">
                                            <div class="form-group" >    
                                                <label>Condicion ante IVA</label>
                                                <div class="controls"  >
                                                    <select id="inp_condiciones" name="inp_condiciones" class="form-control">
                                                        <option value=""></option>
                                                <?php
                                                $selected="";
                                                    foreach ($condicionesiva as $con) {  
                                                    if($con['id_cond_iva']==4){
                                                        $selected="selected='selected'";
                                                    }else{
                                                        $selected="";
                                                    }               
                                                     echo "<option value='".$con['id_cond_iva']."' $selected>".$con['condicion']."</option>";
                                                   }
                                                 ?>
                                                     </select> 
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col-2 -->

                                         <!-- begin col-4 -->
                                        <div class="col-md-4">
                                            <div class="form-group">    
                                                <label>Representa</label>
                                                <div class="controls">
                                                     <input type="hidden" name="inp_representa" id="inp_representa"  />
                                                    <table class="table" id="tbl-representa">
                                                    <tbody>
                                                        <tr>
                                                            <td>ninguno
                                                            </td>
                                                            <td><a href="javascript:;" id="add-representa" class="btn btn-sm btn-primary"><i class="fa fa-search"></i>
                                                            </a>
                                                            <a href="javascript:;" id="remove-representa" class="btn btn-sm btn-inverse">
                                                                <i class="fa fa-eraser"></i>
                                                            </a>
                                                            
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                
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


<div id="modal-busqueda-representante" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" id="classModalLabel">
              Buscar  cliente
            </h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" action="/" method="POST" id="form-avanzada" autocomplete="off" >                                 
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label>Apellido y/o Nombre</label>
                                        <input type="text" class="form-control" id="b_nombres" placeholder="nombre del cliente">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Cuit</label>
                                        <input type="text" class="form-control" id="b_cuit" placeholder="cuit/cuil del cliente">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Documento</label>
                                        <input type="text" class="form-control" id="b_nro_docu" placeholder="documento">
                                    </div>                    
                                    <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-primary" id="btn-buscar" data-loading-text="<i class='fa fa-spinner fa-spin'></i> buscando...">
                                <i class="fa fa-search"></i>Buscar</button>
                                    </div>
                                </div>
                                 <div class="form-group" id="tpl-table-query">
                                 </div>
           </form>        
      </div>
      <div class="modal-footer">
        <div class="row">
            <div class="col-md-6">
            </div>            
            <div class="col-md-3">
                
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Cerrar
                </button>                
            </div>            
        </div>        
      </div>
    </div>
  </div>
</div>




<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/javascript">   

var green = '#00acac',
red = '#ff5b57',
blue = '#348fe2',
purple = '#727cb6',
orange = '#f59c1a',
black = '#2d353c';


App.restartGlobalFunction();
App.setPageTitle('Alta/ Edición de Clientes | Coffe box app');
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;

var ofwtlocal=new fwt('<?=base_url()?>index.php/entidades/clientes/get')

var oClientesView;

var oClientes=null; 
var oCliente=null; 
var eventos = _.extend({}, Backbone.Events);

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
                    inicializacion();
                    handleBootstrapWizardsValidation();
                });
            });
        }
    };
}();    

    
FormWizardValidation.init(); 
$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
        $.getScript('<?=BASE_FW?>assets/plugins/switchery/switchery.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {
                  $.getScript('<?=BASE_FW?>assets/plugins/jquery-tag-it/js/tag-it.min.js').done(function() {
                        //$("#formuploadajax").hide();
                        spinnerStart($("#panel-body"))  
                         
                    if($("#modo").val()=="M")    
                    {
                       var cmp=Array('id_cond_iva','id_persona','apellido','nombres','calle','nro','piso','dpto','cp','car_tel','nro_tel','nro_docu','tipo_docu','cuit','sexo','email','id_loc','id_pro','tipo_persona','observaciones','id_pro_nac','id_loc_nac','DATE_FORMAT(fe_nacimiento,"%d/%m/%Y") as fe_naci,id_cliente_representante,strnombrecompleto_representante,cuit_representante,habilitado')
                     
                     ofwlocal.getAsync('verclientes_representantes',cmp,'id_cliente='+$('#id_cliente').val(),'',modificarload)
                     
                    }else
                    {
                    cargar_localidades('inp_localidad');
                    cargar_localidades('inp_localidad_naci');                    
                    cargarhabilitado(1)
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
                    
                    $("#cargar-menssaje").click(function(){
                    $("[id='modal-details']").modal("hide")
                        cargar_datos_persona(arrPersona[0])
                     }) 
                     $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/clientes/");
                         })
                     spinnerEnd($("#panel-body"))
                    $("#formuploadajax").show();

                  });
                });//datepicker es
              });//datepicker js
           });
        });       

    })
    





function modificarload(res){
    for (idx in res)
    {
        $("#inp_apellido").val(res[idx].apellido)
        $("#inp_nombres").val(res[idx].nombres)
        $("#inp_nro_docu").val(res[idx].nro_docu)
        $("#inp_tipo_docu").val(res[idx].tipo_docu)
        $("#inp_email").val(res[idx].email)
        $("input:radio[name='sexoRadios']").filter("[value='"+res[idx].sexo+"']").attr('checked', true);
         $("#inp_car_tel").val(res[idx].car_tel)
        $("#inp_nro_tel").val(res[idx].nro_tel)
        $("#inp_calle").val(res[idx].calle)
        $("#inp_nro").val(res[idx].nro)
        $("#inp_piso").val(res[idx].piso)
        $("#inp_dpto").val(res[idx].dpto)
        $("#inp_cp").val(res[idx].cp)        
        $("#id_persona").val(res[idx].id_persona)
        $('#inp_cuit').val(res[idx].cuit);                    
        $('#inp_tipo_persona').val(res[idx].tipo_persona);
        var id_pro=(res[idx].id_pro==null)?12:res[idx].id_pro
        var id_pro_nac=(res[idx].id_pro_nac==null)?12:res[idx].id_pro_nac
        $('#inp_provincia_naci').val(id_pro_nac);
        $('#inp_fe_naci').val(res[idx].fe_naci) 
        $('#inp_observaciones').val(res[idx].observaciones) 
        $('#inp_img_profile').val("");
        $('#inp_condiciones').val(res[idx].id_cond_iva) 
        
        if(res[idx].id_cliente_representante != null){
        $('#inp_representa').val(res[idx].id_cliente_representante);
        $('#tbl-representa td:first').html(res[idx].strnombrecompleto_representante+" - cuit:"+res[idx].cuit_representante);   
        }else{
            $('#inp_representa').val("");
        $('#tbl-representa td:first').html("ninguno");   
        }
        cargarhabilitado(res[idx].habilitado)

        $('#inp_representa').val(res[idx].id_cliente_representante);
        
        if(id_pro != null)
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
        var sexo=$('input[name="sexoRadios"]:checked').val();
        mensaje=mensaje+(((sexo=="" || sexo==null)?"No ha completado el campo sexo<br/>":""))        
        mensaje=mensaje+((($("#inp_localidad").val()=="" && !$("#chk-data-basic").is(":checked"))?"No ha completado la localidad<br/>":""))
        
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
     var condicion=$('#inp_condiciones').val();
        mensaje=mensaje+(((condicion=="")?"No ha seleccionado una condicion ante iva<br/>":""))                
        
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
                    var nombres=$("#inp_nombres").val()
                    var apellido=$("#inp_apellido").val()                 
                    var tipo_docu=$('#inp_tipo_docu').val();
                    var nro_docu=$('#inp_nro_docu').val();
                    var cuit=$('#inp_cuit').val();
                    var sexo=$('input[name="sexoRadios"]:checked').val();
                    var localidad=$('#inp_localidad').val();
                    var localidad_naci=$('#inp_localidad_naci').val();
                    var condicioniva=$('#inp_condiciones').val();
                                        
                    

                if(tipo_docu =="")                    
                     mensajeErrors+="<li>No completo el campo de tipo de documento</li>";                                     

                 if(nro_docu =="")                    
                     mensajeErrors+="<li>No seleccionó un numero de documento</li>";

                 if(sexo =="" || sexo==null)                    
                     mensajeErrors+="<li>No completo el sexo/li>";

                 if(apellido =="")                    
                     mensajeErrors+="<li>No ingresó apellido</li>";

                 if(nombres =="")                    
                     mensajeErrors+="<li>No ingresó un nombre</li>";

                 if(localidad =="" && !$("#chk-data-basic").is( ":checked" ))                    
                     mensajeErrors+="<li>No seleccionó una localidad</li>";
                 

                 

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


        $.ajax({url:'<?=base_url()?>index.php/entidades/clientes/save',
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
                            $("#inp_nro_docu").val("") 
                            $("#inp_domicilio").val("")                    
                            $('#inp_tipo_docu').val(1);
                            $('#inp_nro_docu').val("");                    
                            $('#inp_cuit').val("");                    
                            $('#inp_tipo_persona').val("F");                            
                            $('input[name="sexoRadios"]').each(function(e){                                
                            $(this).prop("checked", false);    
                            });
                            
                            $('#inp_provincia').val(12);
                            $('#inp_provincia_naci').val(12);
                            $('#inp_email').val("");                                                
                             $('#inp_email').val("");                                                
                            $("#inp_piso").val("")                        
                            $("#inp_nro").val("")                        
                            $("#inp_calle").val("")                        
                            $("#inp_dpto").val("")                        
                            $("#inp_cp").val("") 
                            $('#inp_fe_naci').val("") 
                            $('#inp_observaciones').val("") 
                            $('#inp_localidad_naci').find("option").remove().end();
                            $('#inp_img_profile').val("");
                            

                            $("#confirm-evalue").off();
                            if($("#modo").val()=="A")
                            {
                            $("#titulo-confirmacion").html("El cliente se creó correctamente")    
                            }else
                            {
                                $("#titulo-confirmacion").html("El cliente se modificó correctamente")    
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
var nro_docu=$("#inp_nro_docu").val();
var tipo_docu=$("#inp_tipo_docu").val();
var sexo=$('input[name="sexoRadios"]:checked').val();
var exito=true
//$("#panel-loader").modal("show")
spinnerStart($('#panel-body'));
if($("#modo").val()=="M")
{
var cmp=Array('id_persona','apellido','nombres','calle','nro','piso','dpto','cp','car_tel','nro_tel','id_loc','tipo_docu','nro_docu','sexo','email','id_pro','id_loc_nac','id_pro_nac','DATE_FORMAT(fe_nacimiento,"%d/%m/%Y") as fe_naci','tipo_persona','cuit','email')
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
var cmp=Array('id_persona','apellido','nombres','calle','nro','piso','dpto','cp','car_tel','nro_tel','id_loc','tipo_docu','nro_docu','sexo','email','id_pro','id_loc_nac','id_pro_nac','DATE_FORMAT(fe_nacimiento,"%d/%m/%Y") as fe_naci','tipo_persona','cuit','email')
arrPersona=ofwlocal.get('verpersonas',cmp,'nro_docu='+nro_docu+' and sexo="'+sexo+'" and tipo_docu='+tipo_docu+" and id_empresa="+$("#id_empresa").val());  
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

if(oPersona.car_tel!=null)
$("#inp_car_tel").val(oPersona.car_tel)
if(oPersona.nro_tel!=null)
$("#inp_nro_tel").val(oPersona.nro_tel)
if(oPersona.calle!=null)
$("#inp_calle").val(oPersona.calle)
if(oPersona.nro!=null)
$("#inp_nro").val(oPersona.nro)
if(oPersona.piso!=null)
$("#inp_piso").val(oPersona.piso)
if(oPersona.dpto!=null)
$("#inp_dpto").val(oPersona.dpto)
if(oPersona.cp!=null)
$("#inp_cp").val(oPersona.cp)

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
if(id_pro=="" || id_pro==null)
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
    $("#ctrllocalidad_naci").html("<select class='combobox' id='"+id_input+"' name='"+id_input+"' data-parsley-group='wizard-step-1'  ><option value=''></option></select>")
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
    handleLoadPage("#<?php echo base_url(); ?>index.php/entidades/clientes/ingresar_alta") 
}

function REGRESAR(url)
{
    handleLoadPage("#"+url) 
}


$("#add-representa").click(function(){
    $("#modal-busqueda-representante").modal("show");
})

$('#modal-manual-busqueda').on('hidden.bs.modal', function (e) {
  
})

$("#remove-representa").click(function(){
    $("#inp_representa").val("");
    $("#tbl-representa td:first").html("ninguno")
})

function buscar_representante(){
   var patron={nro_docu:$("#b_nro_docu").val(),cuit:$("#b_cuit").val(),nombres:$("#b_nombres").val()};
   oClientesView.render(patron);
}


var Cliente=Backbone.Model.extend({idAttribute:'id_cliente',
defaults:{
"id_cliente":0
},
initialize:function(options){
    
    this.options=options || {}
    this.options['puedefacturar']=false;
    this.options['eventos']=eventos;
    thats=this;
    
    if(typeof this.options.id_cliente!="undefined")
    {   
        cond="id_cliente="+this.options.id_cliente;
        var cuit=this.get("cuit")
        if(cuit!=null){
            if(cuit.length!=11){
            this.options.puedefacturar=false;
            }
        }
        
    }
},
getsaldo:function(){
    var saldo=0;
    //definicion: consulta en tiempo real si tiene saldo a favor
    if(typeof this.options.id_cliente!="undefined")
    {   if(this.get("cf")!=1){
        cond="id_cliente="+this.options.id_cliente;
        var cols=Array("saldo_afavor")
        rs=ofwlocal.get("versaldo_afavor",cols,cond,"")    
        var datos=null;
        if(rs.length>0)
        saldo=parseFloat(rs[0].saldo_afavor);
        this.saldo_afavor=saldo;
        }else{
            this.saldo_afavor=0;
        }
        
    }
return saldo;
}
});//clientemodel




var ClientesView=Backbone.View.extend(
{   el:$('#tpl_opera_cliente'),
    clientes:null,    
    initialize:function(options)
    {
        this.options=options || {};
        that=this;
        eventos.on("initload_buscar",this.loading,this)
        eventos.on("endload_buscar",this.endload,this)
        
    },
    render:function(patrones)
    {
        var that=this;
        
        
        
        oClientes.loadAsync(patrones)
        
        return this;
    },//render        
    events:{        
            "click button[name='selecItem']":'seleccionar'
    },
    loading:function(oRes)
    {  
    $("#btn-buscar").button("loading") 
    
    },
    endload:function(oRes)
    { 
      $("#btn-buscar").button("reset")     
        this.cargar(oRes)
        
    },
    cargar:function(oRes)
    {
        var that=this;
        olist=oRes.models          
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));        
        
        $('#data-table').DataTable({responsive: true,searching:false,"columns": [
            { "orderable": true },
            { "orderable": true },                        
            { "orderable": true },                        
            { "orderable": true },                        
            { "orderable": false}]}); 
        
    },
    seleccionar:function(e)
    {   
           var id_button=(e.target.id.indexOf("seleccionar-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return
            
            //si contiene la leyenda de agregado , no se agrega
        if($("#"+id_button).html().indexOf("agregado")>=0){
            return
        }

        id_model=id_button.replace("seleccionar-","");
        var modelo=oClientes.get(id_model)
        
        if(typeof modelo=="undefined")        
            return        

        $("#inp_representa").val(modelo.get("id_cliente"))
        $("#tbl-representa td:first").html(modelo.get("strnombrecompleto")+" - cuit: "+modelo.get("cuit"))
        $("#modal-busqueda-representante").modal("hide");

        
    }
});//ClientesView


var Clientes=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {} 
        this.options['totalcargados']=0;
        this.options['totalesperado']=0;
    },
    loadAsync:function(patrones){
        
        var that=this;
        that.reset();        
        var cond="cf<>1 and id_cliente<>"+$("#id_cliente").val();
        
        if(typeof patrones['nombres'] !="undefined")
        {patrones['nombres']=patrones['nombres'].trim();
            if(patrones['nombres']!="")
            {
            cond+=' and strnombrecompleto like "'+patrones['nombres']+'%"'    
            }
            
        }
        if(typeof patrones['nro_docu'] !="undefined")
        {patrones['nro_docu']=patrones['nro_docu'].trim();
            if(patrones['nro_docu']!="")
            {
            cond+=' and nro_docu="'+patrones['nro_docu']+'"'
            }   
        }
        if(typeof patrones['cuit']!="undefined")
        {   if(patrones['cuit']!="")
            {
            patrones['cuit']=patrones['cuit'].trim();
            cond+=' and cuit="'+patrones['cuit']+'"'
            }
            
        }
                
       ofwlocal.onload=function(){
        that.options.eventos.trigger("initload_buscar",that);
       }
       
        ofwlocal.getAsync("verclientes",Array("*"),cond,"strnombrecompleto asc,nro_docu asc",function(rs){ 

            that.cargar(rs) 

        })


    },    
    cargar:function(rs)
    {   
     for (c in rs)
     { 
      this.add(rs[c])
     }

     this.options.eventos.trigger("endload_buscar",this);
    },
    model:Cliente //viene de backbone-utiles.js
});

 $("#btn-buscar").click(function(){  
        buscar_representante();              
    })


function inicializacion(){
    oClientes=new Clientes({eventos:eventos});    
    oClientesView=new ClientesView({el:$('#tpl-table-query')})
}


function cargarhabilitado(habilitado=1)
{
var checked=(habilitado==1)?'checked':''
$("#ctrlhabilitado").html("<input type='checkbox' data-render='switchery' data-theme='default' "+checked+"  id='habilitado' name='habilitado'/>")
renderHabilitado()

}

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
}

$("#chk-data-basic").click(function(){
    
var databasic=$(this).is( ":checked" )
var lbl
if(databasic){

    $("#inp_localidad").removeAttr("required")
    lbl=$("#inp_localidad").parent().parent().find("label")
    lbl.html(lbl.html().replace("(*)",""))
    $("#inp_calle").removeAttr("required")
    lbl=$("#inp_calle").parent().parent().find("label")
    lbl.html(lbl.html().replace("(*)",""))
    lbl=$("#inp_nro").parent().parent().find("label")
    lbl.html(lbl.html().replace("(*)",""))
    $("#inp_car_tel").removeAttr("required")
     lbl=$("#inp_car_tel").parent().parent().find("label")
    lbl.html(lbl.html().replace("(*)",""))
    $("#inp_nro_tel").removeAttr("required")
    lbl=$("#inp_nro_tel").parent().parent().find("label")
    lbl.html(lbl.html().replace("(*)",""))

    
}else{
    $("#inp_localidad").prop("required",true)
    lbl=$("#inp_localidad").parent().parent().find("label")
    lbl.append("(*)")
    $("#inp_calle").prop("required",true)
    lbl=$("#inp_calle").parent().parent().find("label")
    lbl.append("(*)")
    lbl=$("#inp_nro").parent().parent().find("label")
    lbl.append("(*)")
    $("#inp_car_tel").prop("required",true)
    lbl=$("#inp_car_tel").parent().parent().find("label")
    lbl.append("(*)")
    $("#inp_nro_tel").prop("required",true)
    lbl=$("#inp_nro_tel").parent().parent().find("label")
    lbl.append("(*)")

}
})

</script>
<!-- ================== END PAGE LEVEL JS ================== -->