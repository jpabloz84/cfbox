<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$saldoafavor=0;
if(count($saldo)>0){
    $saldoafavor=$saldo[0]['saldo_afavor'];
}
$importe_total=0;
$importe_neto=0;
$importe_iva=0;
$importe_pago=0;
$deuda=0;


if(count($totales)>0){
    $importe_total=$totales[0]['importe_total'];
    $importe_neto=$totales[0]['importe_neto'];
    $importe_iva=$totales[0]['importe_iva'];
    $importe_pago=$totales[0]['importe_pago'];
    $deuda=$totales[0]['deuda'];
}

$styledeuda=($deuda>0)?"bg-red":"bg-green";


// Para controlar las versones - comentar para producción
$version =rand(0, 10000);
?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  

<link href="<?=BASE_FW?>assets/plugins/bootstrap-eonasdan-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content">     
    <input type="hidden" id="base_url" value="<?=base_url()?>" />
    <input type="hidden" id="base_fw" value="<?=BASE_FW?>" />
    <input type="hidden" id="id_cliente" value="<?=$id_cliente?>">
    <input type="hidden" id="id_empresa" value="<?=$visitante->get_id_empresa()?>">
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">                        
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-warning" id="btn-volver" >volver &nbsp;<i class="fa fa-reply"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CUENTA CORRIENTE DE <?php echo strtoupper($cliente['strnombrecompleto']) ?> - documento <?php echo strtoupper($cliente['nro_docu']) ?> (id cliente <?php echo strtoupper($cliente['id_cliente']) ?>)</h4>
            </div>             
            <div class="panel-body" id="panel-body-saldos">                    
                    <div class="row">
                    <div class="col-md-4">
                        <div class="widget widget-stats <?=$styledeuda?>" id="divsaldo">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-institution"></i></div>
                            <div class="stats-title">SALDO (incluye cuotas vencidas)</div>
                            <?php
                            $leyenda="";
                            if($deuda>0){
                                $leyenda="Debe ";
                            }
                            if($deuda<0){
                                $leyenda="saldo a favor ";
                            }
                            ?>
                            <div class="stats-number"><?=$leyenda?> $<span id="totaladeudado"><?=$deuda?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" onclick="detalledeudas()">COMPROBANTES ADEUDADOS&nbsp;<i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                            
                                 <label class="row">ACCIONES</label>
                                  <div class="form-group"> 
                                  <button type="button" class="btn btn-inverse btn-block btn-sm" onclick="operar()">
                                         <i class="fa fa-file-text-o"></i>
                                    FACTURAR</button>                                       
                                    <button type="button" class="btn btn-success btn-block btn-sm"  onclick="crear_pago()">
                                    <i class="fa fa-credit-card"></i>
                                    CARGAR PAGO</button>
                             <button type="button" class="btn btn-default btn-block btn-sm"  onclick="imputar()">
                                    <i class="fa fa-check-circle"></i>
                                    IMPUTAR</button>

                                   </div>
                            
                       </div>
                       <div class="col-md-3 col-sm-3">                            
                                 <label class="row">SECCIONES</label>                                  
                                     <div class="form-group"> 
                                            <label  class="checkbox-inline">
                                                 <input name="chk-secciones" id="chk-movimientos" value="consultarmovimientos" type="checkbox"  />MOVIMIENTOS
                                             </label>
                                    </div>
                                    <div class="form-group"> 
                                             <label  class="checkbox-inline">
                                                  <input name="chk-secciones" id="chk-pagos" value="btnconsultarpagos" type="checkbox" />PAGOS
                                             </label>
                                   </div>
                            
                       </div>
                       <?php if($cliente['cf']!=1):?>
                         <div class="col-md-2 col-sm-2">                            
                          <div class="form-group"> 
                              <button  class="btn btn-primary btn-block btn-sm" id="btn-cliente">
                                Datos cliente&nbsp;<i class="fa fa-eye"></i>
                            </button>
                           </div>                            
                       </div>

                        <?php endif; ?>
                      
                     </div>
                     <!-- begin row -->
                    <div class="row" id="row-movimientos" style="display: none">                        
                             <div class="form-group" id="tpl-table-query-movimientos">
                                <!-- reservado para tpl-movimientos-list,  -->      
                             </div>                        
                    </div>
                    <!--end row-->
                   
                </div>            
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row" id="row-pago-add" style="display: none">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">                        
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>

                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CARGAR PAGO</h4>
            </div>             
            <div class="panel-body" id="panel-body-cargar-pago">
                <form class="form-horizontal" action="/" method="POST" id='form-crea-pago'>                  
                <div class="form-group">                                        
                        <div class="col-md-4">
                            <div class="input-group">
                                    <span class="input-group-addon" id="basic-tipo_pago">Tipo pago</span>                            
                                 <select id="inp_id_tipo_pago" name="inp_id_tipo_pago"  aria-describedby="basic-tipo_pago" onchange="verificar_params()" class="form-control">
                                    <?php
                                    foreach ($tipo_pagos as $tp) {
                                        $id_tipo_pago=$tp['id_tipo_pago'];
                                        $tipo_pago=$tp['tipo_pago'];
                                        if($id_tipo_pago=="1"){
                                        echo "<option value='$id_tipo_pago' selected='selected' >$tipo_pago</option>";    
                                        }else{
                                            echo "<option value='$id_tipo_pago' >$tipo_pago</option>";    
                                        }
                                        
                                    }
                                    ?>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-monto">Importe</span>
                                <input type="number" name="inp_monto_abona" id="inp_monto_abona" class="form-control" placeholder="ingrese monto..."   aria-describedby="basic-monto"  />
                            </div>
                        </div>                        
                        
                        
                        <div class="col-md-4">                                                 
                            <div class="input-group" id="lbpago" style="display: none">
                               <span class="input-group-addon" id="basic-fecha-pago">Datos del pago</span> 
                                 <button type="button" class="btn btn-sm btn-warning" name="btn-info-pago" id="btn-info-pago" onclick="mostrar_params()" style="display: none">
                                <i class="fa fa-info"> VER</i>
                                </button>
                            </div>
                        </div>                 
                </div>
                <div class="form-group">
                        <div class="col-md-4">
                            <div class="input-group">
                               <span class="input-group-addon" id="basic-fecha-pago">Fecha de pago</span> 
                                <input type="text" name="inp_fe_pago" id="inp_fe_pago" class="form-control" placeholder=""   aria-describedby="basic-fecha-pago"  />
                            </div>
                        </div>
                        <div class="col-md-4">
                             <div class="input-group date" id="datetimepicker">
                                <span class="input-group-addon">
                                    Horario pago
                                </span>
                                <input type="text" class="form-control" id="inp_horario_pago"  />
                            </div>
                        </div>
                </div>
                <div class="form-group">
                    <div class="col-md-9">
                        <div class="input-group">
                                <span class="input-group-addon" id="basic-observaciones">Observaciones</span>
                            <input type="text" class="form-control" placeholder="" aria-describedby="basic-observaciones"  name="inp_observacion" id="inp_observacion">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group">
                        <button type='button' id="save-pago" class='btn btn-success btn btn-sm'  class="form-control" onclick="agregar_pago()">
                                <i class='fa fa-save' data-loading-text="<i class='fa fa-spinner fa-spin '></i> procesando..." ></i>
                                AGREGAR PAGO</button>
                        </div> 
                    </div>
                </div> 
            </form>           
           </div>
           <!-- end body -->
          </div> 
          <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row" id="row-consulta-pagos">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">                        
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CONSULTAR PAGOS</h4>
            </div>             
       
            <div class="panel-body"  id="panel-body-consultar-pagos">
                <form class="form-horizontal" action="/" method="POST" id='form-standard'>                  
                <div class="form-group">                                        
                    <div class="col-md-1">
                        <label>ID pago
                            <input type="text" name="id_pago" id="id_pago" class="form-control" placeholder="" />
                        </label>
                    </div>
                    <div class="col-md-3">                            
                        <label>fecha desde
                            <input type="text" name="fecha_desde" id="fecha_desde" class="form-control" placeholder="ingrese fecha..." />
                        </label>
                    </div>
                    <div class="col-md-3">
                        <label>fecha hasta
                        <input type="text" name="fecha_hasta" id="fecha_hasta" class="form-control" placeholder="ingrese fecha..." />
                        </label>
                    </div>                        
                    <div class="col-md-2">                    
                        <label>Incluir anulados
                        <input type="checkbox" name="anulados" id="anulados" class="form-control" />
                        </label>
                    </div>
                    <div class="col-md-1">                    
                        <button type='button' class='btn btn-success btn btn-sm'  class="form-control" id="btn-exportar">
                            <i class='fa fa-file-excel-o' ></i>
                            EXPORTAR</button>                        
                    </div>
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm'  class="form-control" id="btn-consultar">
                            <i class='fa fa-search' ></i>
                            BUSCAR</button>
                    </div>
               </div>
               <div class="form-group" id="table-wrapper">
                    
               </div>
            </form>
            </div>
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row" id="row-det" style="display: none">
    <!-- reservado para mostrar listado de comprobantes adeudados de objeto elementos view -->          
    </div>
    <!-- begin row -->
    <div class="row" id="row-body">
    <!-- reservado para --> 
    </div>
    
    <!-- begin row -->
    <div class="row" id="row-pagos">
    <!-- reservado para tpl-pagos-list, oPagoView -->      
    </div>
    <div class="modal fade in" id="modal-parametros" style="display: none;">
    </div>
</div>

<div id="modal-cliente-datos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" >DATOS DEL CLIENTE</h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" action="/" method="POST" autocomplete="off" >                                 
                <div class="form-group">
                    <div class="col-md-6">
                        <label>Nombre completo</label>                        
                          <input type="text" id="cli_strnombrecompleto" value="<?=$cliente['strnombrecompleto']?>" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Documento</label>
                        
                        <input type="text" id="cli_documento" value="<?=$cliente['documento']?> - <?=$cliente['nro_docu']?>" class="form-control" readonly>
                    </div>                 
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <label>Domicilio</label>
                        <input type="text" id="cli_domicilio" value="<?=$cliente['domicilio']?> - <?=$cliente['descripcion_loc']?>(<?=$cliente['descripcion_pro']?>)" class="form-control" readonly>
                        
                    </div>                    
                    
                </div>                
                <div class="form-group">
                    <div class="col-md-6">
                        <label>Telefono</label>
                        <input type="text" id="cli_telefono" value="<?=trim($cliente['telefono'])?>" class="form-control">
                        <a href="javascript:;" class="btn btn-sm btn-primary" id="copiartel" >copiar</a>
                        <a href="javascript:;" class="btn btn-sm btn-primary" id="wptel" >whatsapp</a>
                    </div>                    
                
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="text" id="cli_email" value="<?=trim($cliente['email'])?>" class="form-control">
                         <a href="javascript:;" class="btn btn-sm btn-primary" id="copiaremail" >copiar</a>
                    </div>                    
                    
                </div>   
                
                 <div class="form-group">
                    <div class="col-md-4">
                        <label>Estado</label>
                        <?php
                        $Habilitado="";
                        if($cliente['habilitado']==1){
                            $Habilitado="Habilitado desde el ".datetostr($cliente['fe_estado'],'dd-mm-yyyy');
                        }else{
                            $Habilitado="No habilitado ";
                        }
                        ?>
                        <label><?=$Habilitado?></label>                        
                    </div>
                </div>            
             </form>
        
      </div>
      <div class="modal-footer">               
                <button type="button" class="btn btn-sm btn-warning" id="editar-cliente">
                    EDITAR DATOS
                </button>   
                  <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">
                    CERRAR
                </button>     
      </div>
    </div>
  </div>
</div>


<script src="<?=BASE_FW?>assets/plugins/bootstrap-daterangepicker/moment.js"></script>
<script src="<?=BASE_FW?>assets/plugins/bootstrap-eonasdan-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script> 

<!-- end #content -->

<script type="text/template" id="tpl-cuotas-list"> 
<table id="data-table-cuotas" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>fecha cuota</th>                                        
                                <th>detalle</th>                                        
                                <th>comprobante</th>
                                <th>emitido por</th>
                                <th>observaciones</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {          

    var detalle=elemento.get('tipo_socio')+" - $"+elemento.get('importe')
    var comprobante=""    
    var emitido_por=""
    var obs=""
    if(es_entero(elemento.get("id_comp"))){
    comprobante="<a href='"+base_url + elemento.get("path_comp")+"' target='_blank'>comp "+elemento.get("id_comp")+" - emisión: "+elemento.get('fe_comprobante_str')+"</a>"
    emitido_por=elemento.get('usuario_estado')+" - suc.: "+elemento.get('sucursal')
    }
    if(es_numero(elemento.get("deuda")) &&  parseFloat(elemento.get("deuda"))>0){
    obs="El comprobante tiene deuda "
    }

    if(elemento.get("vencida")==1){
    obs+=((obs=="")?"la cuota está vencida":" - la cuota está vencida")
    }
    
    %>
    <tr>        
        <td><span style="display:none"><%=elemento.get('fe_vencimiento')%></span><%=datetimetostr(elemento.get('fe_vencimiento'))%></td>
        <td><%=detalle%></td>
        <td><%=comprobante%></td>      
        <td><%=emitido_por%></td>          
        <td><%=obs%></td>
        <td>
            <% if(es_numero(elemento.get('id_comp'))){%>
            <a href="javascript:;" class="btn btn-white btn-xs" name="cuota_comp" id="comp-<%=elemento.get('id_comp') %>"><i class="fa fa-eye"></i></a>
            <%}%>
        </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>

<script type="text/template" id="tpl-movimientos-list"> 
<table id="data-table-movimientos" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>fecha</th>                                        
                                <th>movimiento</th>                                        
                                <th>importe</th>
                                <th>tipo pago</th>
                                <th>condicion venta</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {          

    var movimiento=''
    var deuda_str=(elemento.get('tipo')=='comp' && parseFloat(elemento.get('deuda'))>0.01 && elemento.get('estado')!='A')?' (debe $'+format_number(elemento.get('deuda'),'#.00')+') ':''
    var estado_str=(elemento.get('estado')=='A')?' (ANULADO) ':''
    var importe_str=(elemento.get('tipo')=='pagos')?format_number(elemento.get('importe_pago'),'#.00'):format_number(elemento.get('monto'),'#.00')
        if(elemento.get('tipo') =='pagos'){
        movimiento="PAGO ("+elemento.get('id_reg')+")";
        }else{
            if(elemento.get('afip')==1){
            var str_talonario=format_number(elemento.get('nro_talonario'),'0000')
            var str_nro_comp=format_number(elemento.get('nro_comp'),'00000000')
            movimiento=elemento.get('tipo_comp')+" "+str_talonario+" - "+str_nro_comp+" (id comp: "+elemento.get('id_reg')+")";
            }else{            
            movimiento="ORDEN DE PEDIDO (id comp:"+elemento.get('id_reg')+")"
            }
        }
        movimiento+=estado_str
        
    
    %>
    <tr>        
        <td><span style="display:none"><%=elemento.get('fecha')%></span><%=datetimetostr(elemento.get('fecha'))%></td>
        <td><%=movimiento %></td>
        <td>$ <%=importe_str %><%=deuda_str%></td>      
        <td><%=elemento.get('tipo_pago') %></td>          
        <td><%=elemento.get('condicion_venta') %></td>
        <td><a href="javascript:;" class="btn btn-white btn-xs" name="<%=elemento.get('tipo') %>" id="<%=elemento.get('tipo') %>-<%=elemento.get('id_reg') %>"><i class="fa fa-eye"></i></a></td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>


<script type="text/template" id="tpl-pagos-list">
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                       
                </div>
                <h4 class="panel-title">RESULTADO DE LA BUSQUEDA</h4>
    </div>             
    <div class="panel-body" id="panel-body-pagos">                
        <table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>pago</th>
                                <th>tipo pago</th>
                                <th>usuario</th>                                                         
                                <th>estado</th>                                        
                                <th>imputado</th>                                        
                                <th>recibo</th>                                        
                                <th>ver</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        
                        <% _.each(ls, function(elemento) { 
                        var strpago='N° '+elemento.get('id_pago')+' - $ ' + elemento.get('pago') + ' ('+ elemento.get('fecha_pago') + ')';

                        var str_imputado=''
                        if(parseFloat(elemento.get('pago'))==parseFloat(elemento.get('imputado'))){
                        str_imputado="<a href='javascript:;' class='btn btn-success btn-xs' id='id_imputado_"+elemento.cid+"'>$ "+elemento.get('imputado')+"</a>"
                        }

                        if(parseFloat(elemento.get('pago'))!=parseFloat(elemento.get('imputado'))  && parseFloat(elemento.get('imputado'))==0){
                        str_imputado="<a href='javascript:;' class='btn btn-inverse btn-xs' >$ "+elemento.get('imputado')+"</a>"
                        }

                        if(parseFloat(elemento.get('pago'))!=parseFloat(elemento.get('imputado'))  && parseFloat(elemento.get('imputado'))!=0){
                        str_imputado="<a href='javascript:;' class='btn btn-warning btn-xs' id='id_imputado_"+elemento.cid+"'>$ "+elemento.get('imputado')+"</a>"
                        }

                        var str_estado="<a href='javascript:;' class='btn btn-"+elemento.get('btnclass_pago')+" btn-xs'>"+elemento.get('estado_pago_desc')+"</a>"
                        

                        var strtipo_pago=''                        
                        var params=elemento.get("parametros");
                        if(typeof params !="undefined")
                        {
                            if(params.length>0)
                            {
        strtipo_pago="<a href='javascript:;' id='id_tipo_pago_"+elemento.cid+"-"+elemento.get('id_tipo_pago')+"'>"+elemento.get("tipo_pago")+"</a>"
                            }else{
                                    strtipo_pago=elemento.get("tipo_pago")
                            }
                        }
                        
                        
                         %>
                        <tr>
                            <td><%=strpago%></td>        
                            <td><%=strtipo_pago%></td>
                            <td><%=elemento.get('usuario')%></td>                            
                            <td><%=str_estado%></td>
                            <td><%=str_imputado%></td>                             
                            <td>
                        <a href="javascript:;" class="btn btn-white btn-xs" id="recibo-link-<%=elemento.cid%>"><i class="fa fa-file-pdf-o"></i></a>
                            </td>
                            <td>
                        <a href="javascript:;" class="btn btn-white btn-xs" id="pago-link-<%=elemento.cid%>"><i class="fa fa-search-plus"></i></a>
                            </td>
                        </tr>
                        <% }); %>
                        </tbody>
        </table>
    </div>
   </div>
</div>
</script>

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/template" id="tpl-table-list"> 
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">                        
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove" data-original-title="" title="" data-init="true"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">&nbsp;&nbsp;&nbsp;COMPROBANTES ADEUDADOS</h4>
    </div>             
    <div class="panel-body" id="panel-body-comprobantes">
        <table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
        <thead>
            <tr>
                <th>COMPROBANTE</th>
                <th>FECHA</th>                                
                <th>IMPORTE</th>                                        
                <th>ESTADO</th>
                <th>PAGOS</th>                                        
                <th>DEUDA</th>                                        
                <th>VER</th>                                        
            </tr>
        </thead>
        <tbody>                                                               
            <% _.each(ls, function(elemento) { 
                var str_talonario=format_number(elemento.get('nro_talonario'),'0000')
                var str_nro_comp=format_number(elemento.get('nro_comp'),'00000000')
                var comprobante=(elemento.get('afip')!=1)?('orden de pedido N°:'+elemento.get('id_comp')):(elemento.get('tipo_comp')+' N° '+str_talonario+' '+str_nro_comp+'  ('+elemento.get('id_comp')+')')
                var str_pagos='' 

                if(parseFloat(elemento.get('importe_pago'))< parseFloat(elemento.get('importe_total')) && parseFloat(elemento.get('importe_pago')) > 0)
                {
                    str_pagos="<a href='javascript:;' class='btn btn-warning btn-xs'>$ "+format_number(elemento.get('importe_pago'),'#.00')+"</a>"
                }

                if(parseFloat(elemento.get('importe_pago'))>=parseFloat(elemento.get('importe_total')))
                {
                str_pagos="<a href='javascript:;' class='btn btn-success btn-xs'>$ "+format_number(elemento.get('importe_pago'),'#.00')+"</a>"
                }

                if(parseFloat(elemento.get('importe_pago'))<parseFloat(elemento.get('importe_total')) && parseFloat(elemento.get('importe_pago'))<=0)
                {
                str_pagos="<a href='javascript:;' class='btn btn-danger btn-xs'>$ "+format_number(elemento.get('importe_pago'),'#.00')+"</a>"
                }


             %>
            <tr>        
                <td><%=comprobante%></td>
                <td><%=elemento.get('fe_creacion')%></td>
                <td>$<%=elemento.get('importe_total')%></td>        
                <td><a href="javascript:;" class="<%=elemento.get('estadoclass')%>"><%=elemento.get('descripcion')%></a>
                </td>
                <td><%=str_pagos%></td>     
                <td>$<%=elemento.get('deuda')%></td>     
                <td><a href="javascript:;" class="btn btn-white btn-xs" id="view-link-<%=elemento.get('id_comp')%>"><i class="fa fa-eye"></i></a></td>
            </tr>
            <% }); %>
            </tbody>
        </table>        
    </div>
   </div>
</div>
</script>

<script src="<?=base_url()?>js/cuenta_corriente.js"></script>

<script>
App.restartGlobalFunction();
App.setPageTitle('Consulta de cuenta corriente| Coffee APP');


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {                
                            inicializacion_contexto();     
                            $('[data-click="panel-reload"]').click(function(){
                                
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/cuentacc/"+$("#id_cliente").val());
                             })
                          });
                        });
                    });
                });
            });
        });
    });
});


$("#copiaremail").click(function(e){
    var copyText = document.getElementById('cli_email');
    copyText.select();
    document.execCommand('copy');
    swal("copiado","","success")
})
$("#copiartel").click(function(e){
    var tel=$("#cli_telefono").val().replace(" ","");
    $("#cli_telefono").val(tel)
    var copyText = document.getElementById('cli_telefono');
    copyText.select();
    document.execCommand('copy');
    swal("copiado","","success")
})

$("#wptel").click(function(e){
    var telefono=$("#cli_telefono").val()
      var referencia="https://wa.me/54"+telefono+"?text="
   window.open(referencia,'_blank');
})

$("#editar-cliente").click(function(e){
    if(permitir(prt_clientes,4))
    {
    handleLoadPage("#<?php echo base_url(); ?>index.php/entidades/clientes/modificar/"+$('#id_cliente').val())   
    }else{
        //win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
        swal("atención","No tiene permiso para realizar esta acción","error")
    }    
})
</script>
<!-- ================== END PAGE LEVEL JS ================== -->