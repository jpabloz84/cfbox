<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$default="";
$estado="";
if(isset($comp))
{
    $estado=$comp['estado'];
} 
$pagosids=0;
if(isset($pagos_id))
{
   $pagosids=$pagos_id;
}
$conf=array();
foreach ($visitante->get_conf() as $var) {
$conf[$var['variable']]=$var['valor'];
} 

$id_empresa=$visitante->get_id_empresa();
$conf_comprobantes=$conf['comprobantes'];
$conf_orden_pedido=$conf['orden_pedido'];
$conf_guardar_compra=$conf['guardar_compra'];
// Para controlar las versones - comentar para producción
$version = rand(0, 10000);


?>


<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list">     
    <div class="table-responsive">
    <table id="data-table" class="table table-condensed table-hover" style="color:#707478">
                            <thead>
                                <tr>
                                    <th>PRODUCTO</th>                                        
                                    <th>STOCK</th>                                        
                                    <th>PRECIO</th>
                                    <th>CANTIDAD</th>
                                    <th>-</th>                                        
                                </tr>
                            </thead>
                            <tbody>                                                               
        <% _.each(ls, function(elemento) {  
        
        var strproducto="("+elemento.get('nro_tipo') + ") -" + elemento.get('producto')
        if(elemento.get('categoria')!="" && elemento.get('categoria')!=null)
        {
            strproducto=strproducto + " " + elemento.get('categoria')
        }
        var strstock='no mueve stock este '+elemento.get('tipo')
        if(elemento.get('mueve_stock')==1)
        {
            strstock=elemento.get('stock')+' '+elemento.get('fraccion_plural')+' de este '+elemento.get('tipo')
        }

        %>
        <tr>        
            <td><%=strproducto%></td>
            <td><%=strstock%></td>            
            <td>$ <%=elemento.get('importe_tipo').toFixed("2") %></td>        
            <td>
                <%
                if(elemento.get('mueve_stock')==1)
                {%>
                    <input type='text' value='1' class="form-control" name='inp-cant' id='inp-cant-<%=elemento.cid %>'  style='width:68px'/>
                <%}else
                {%>
                <input type='hidden' value='1' name='inp-cant' id='inp-cant-<%=elemento.cid %>' />
                <label>1</label>
                <%}%>
            </td>        
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
    </div>  
</script>

<script type="text/template" id="tpl-table-list-recibos">   

    <table id="data-table-print-recibos" class="display compact hover table table-striped nowrap" width="100%" style="color:#707478">
                            <thead>
                                <tr>
                                    <th>PAGO</th>
                                    <th>MAIL</th> 
                                    <th>VER</th>
                                    <th>LINK</th>       
                                    <th>WHATSAPP</th>       
                                </tr>
                            </thead>
                            <tbody>                                                               
        <% _.each(ls, function(elemento) {
        if(!elemento.get('propio'))return  //si el pago no es propio, no se muestra
        var id_tipo_pago=elemento.get('id_tipo_pago').toString()   
        var tipospagos=elemento.get('tipospagos')              
        var tipo_pago_str=""
        var tp=tipospagos.findWhere({id_tipo_pago:id_tipo_pago})
        if(tp!=null){
            tipo_pago_str=tp.get('tipo_pago')
        }
        var monto=elemento.get('monto_abona')
        var strpago="("+elemento.get('id_pago')+") - "+tipo_pago_str+" - $" +monto

        var link=base_url+"index.php/operaciones/pagos/recibo/"+elemento.get('token')
        %>
        <tr>        
            <td><%=strpago%></td>           
            <td>
                <button type="button" class="btn btn-xs btn-primary" name='selecItempagomail' id="mail-recibo-<%=elemento.cid %>" idpago="<%=elemento.get('id_pago')%>">
                    enviar
                    <i class="fa fa-mail-forward"></i>
                </button>            
            </td>
             <td>
                <button type="button" class="btn btn-xs btn-primary" name='selecItempagopdf' id="print-recibo-<%=elemento.cid %>" idpago="<%=elemento.get('id_pago')%>">
                    imprimir
                    <i class="fa fa-file-pdf-o"></i>
                </button> 
            </td>
             <td>
                <button type="button" class="btn btn-xs btn-primary" name='selecItempagocopy' id="copy-recibo-<%=elemento.cid %>" idpago="<%=elemento.get('id_pago')%>">
                    copiar
                    <i class="fa fa-unlink"></i>
                </button> 
                <input type="text" id="portapapeles-<%=elemento.cid %>" name="portapapeles-<%=elemento.cid %>"  value="<%=link%>" />
            </td>
            <td>
                <button type="button" class="btn btn-xs btn-primary" name='selecItempagophone' id="phone-recibo-<%=elemento.cid %>" idpago="<%=elemento.get('id_pago')%>">
                    enviar
                    <i class="fa fa-mobile-phone"></i>
                </button>            
            </td>

        </tr>
        <% }); %>
        </tbody>
    </table>    
</script>


<style type="text/css">
    .popover{
    max-width:100%;
}
</style>

<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <input type="hidden" id="id_localidad" value="<?=$id_loc_default?>"/>
    <input type="hidden" id="id_cliente" value="<?=$id_cliente?>"/>
    <input type="hidden" id="id_comp" value="<?=$id_comp?>"/>    
    <input type="hidden" id="pagosID" value="<?=$pagosids?>"/>
    <input type="hidden" id="estado" value="<?=$estado?>"/>
    <input type="hidden" id="base_url" value="<?=base_url()?>" />
    <input type="hidden" id="base_fw" value="<?=BASE_FW?>" />
    <input type="hidden" id="id_empresa" value="<?=$visitante->get_id_empresa()?>"/>
    <input type="hidden" id="conf_comprobantes" value="<?=$conf_comprobantes?>"/>
    <input type="hidden" id="conf_orden_pedido" value="<?=$conf_orden_pedido?>"/>
    <input type="hidden" id="conf_guardar_compra" value="<?=$conf_guardar_compra?>"/>
    <input type="hidden" id="id_usuario_vende" value="0"/>
    <input type="hidden" id="id_pedido" value="<?=$id_pedido?>"/>
    <!--<a id="whatsapp-send" href=""   name="whatsapp-send">algo</a>-->
    <!-- begin row -->
  <div class="row" id="tpl_opera_cliente">
  </div>
    <!-- end row -->    
<!-- begin de tabs row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active" id="li-default-tab-carrito"><a href="#default-tab-carrito" data-toggle="tab">CARRO DE COMPRA (F7)</a></li>
                <li class="" id="li-default-tab-pago"><a href="#default-tab-pago" data-toggle="tab">REALIZAR PAGO (F8)</a></li>
                <li class="" id="li-default-tab-impresiones"><a href="#default-tab-impresiones" data-toggle="tab">IMPRESIONES</a></li>                
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="default-tab-carrito">
                    <div class="row">
                    <div class="panel panel-success">
                       <div class="panel-heading">                        
                        <div class="btn-group pull-right" data-toggle="buttons" id="btn-accion">
                               <a href="javascript:;" class="btn btn-primary btn-sm"  id="btn-manual-busqueda">
                                    <i class="fa fa-file-archive-o"></i> BUSQUEDA MANUAL (F6)
                                </a>
                                <a href="javascript:;" class="btn btn-primary btn-sm"  id="btn-info">
                                    <i class="fa fa-info"></i> 
                                </a>
                         </div>
                         <h4 class="panel-title" id="text-buscador" >SELECCIÓN DE PRODUCTOS</h4>
                       </div>             
                        <div class="panel-body bg-green text-white" id="panel-body-inputs">
                             <form class="form-horizontal" action="/" method="POST"  id='form-standard' autocomplete="off">
                                <div class="form-group">
                                    <div class="col-md-6">
                                            <input type="text" class="form-control" id="buscador"  placeholder="buscador...">
                                    </div>
                                    <div class="col-md-3">
                                            <select class="form-control" id="tipo" >
                                                <option value="">TODOS</option>
                                                    <?php 
                                                    $selected="selected='selected'";
                                                    foreach ($tipos_productos as $tipo) {
                                                        if($tipo['default']==1)
                                                        $default=$tipo['id_tipo'];

                                                        if($tipo['id_tipo']==$default){
                                                            echo "<option value='".$tipo['id_tipo']."' $selected >".$tipo['tipo']."</option>";
                                                        }else{
                                                            echo "<option value='".$tipo['id_tipo']."' >".$tipo['tipo']."</option>";
                                                        }

                                                        
                                                    }
                                                     ?>
                                            </select>
                                    </div>                    
                                    <div class="col-md-3">
                                            <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">APLICAR<span class="caret"></span></button>
                                             <ul class="dropdown-menu"> 
                                                <li><a href="javascript:aplicar('descuento');" >DESCUENTO GENERAL</a></li>
                                                <li><a href="javascript:aplicar('recargo');">RECARGO GENERAL</a></li>
                                            </ul> 
                                            </div>
                                    </div>
                               </div>               
                            </form>                            
                        </div>
                </div>
                <!--panel succes end-->
                 </div>   
                 <!-- end row productos seleccion -->    
                    <!-- begin row carrito-->    
                    <div class="row" id="tpl_opera_carrito">
                    </div>
                    <!-- end row carrito -->
                    <!-- begin row pagar-->    
                    <div class="row">
                        <div class="col-md-2 col-sm-2">
                            <h5><i class="fa fa-shopping-cart"></i> TOTAL $<span name="total_parcial">0</span> </h5>
                        </div>
                      
                       
                        <div class="col-md-7 col-sm-7" >
                        </div>
                        <div class="col-md-3 col-sm-3">                        
                            <p class="text-right m-b-0">
                            <a href="#default-tab-pago" data-toggle="tab" class="btn btn-sm btn-primary m-r-5 "  onclick="ActivarTab('default-tab-pago')">                                
                            PAGAR  (F8)
                            <i class="fa fa-chevron-circle-right"></i>
                            </a>
                            </p>
                        </div>
                        
                    </div>
                    <!-- end row pagar -->     
                </div>
                <div class="tab-pane fade" id="default-tab-pago">               
                    <!-- begin row -->    
                        <div class="row" >
                            <div class="col-md-12" >  
                          <div class="panel panel-info">
                           <div class="panel-heading">
                            <div class="panel-heading-btn">                 
                            </div>
                                <h4 class="panel-title">
                                  SELECCIONE FACTURACIÓN Y FORMA DE PAGO 
                                  <span id="spansaldo" style="display: none">- SALDO A FAVOR $<span id="saldoafavor"></span></span>               
                                </h4>
                           </div>             
                                <div class="panel-body bg-aqua text-white" id="panel-body-pago">
                                    <form class="form-horizontal"  action="/" method="POST" autocomplete="off">
                                    <fieldset>
                                        <?php
                                        $displayorden=($conf_orden_pedido==0)?"none":"inline";
                                        $displaycomprobantes=($conf_comprobantes==0)?"none":"";
                                        $checkedorden=($conf_comprobantes==0)?"checked":"";
                                        ?>
                                        <legend id="legend-comp"></legend>
                                        <div class="form-group">
                                            <div class="col-md-2" style="display:<?=$displayorden ?>">
                                            <label>ORDEN DE PEDIDO
                                             <input type="checkbox" id="chkorden" name="chkorden" class="form-control" checked="<?=$checkedorden?>" style="display:<?=$displaycomprobantes?>" />
                                             </label>
                                            </div>


<!--aca1--->
    <?php if($conf_comprobantes==1){?>
    <div class="col-md-3" >
        <label>COMPROBANTE
        <select id="id_tipo_comp" class="form-control" >
        <option name="" value="">SIN CONDICION</option>
        <?php                                            
            foreach ($tipo_comprobantes as $tipo_comp) {
            echo "<option name='".trim($tipo_comp['tipo'])."' value='".$tipo_comp['id_tipo_comp']."'>".$tipo_comp['tipo_comp']."</option>";
            }
        ?>
         </select>
         </label>
    </div>
    <?php } ?>
                                           
                                            <div class="col-md-3">                                    
                                                <div class="widget widget-stats bg-black">
                                                    <div class="stats-icon stats-icon-xs"><i class="fa fa-shopping-cart fa-fw"></i></div>
                                                    <div class="stats-title">TOTAL</div>
                                                    <div class="stats-number" >$<span name="total_parcial" id="total_parcial">0</span></div>
                                                </div>
                                            </div>                                
                                            <div class="col-md-2">
                                                <div class="widget widget-stats bg-red">
                                                    <div class="stats-icon stats-icon-xs"><i class="fa fa-hand-o-left fa-fw"></i></div>
                                                    <div class="stats-title">RESTA PAGAR</div>
                                                    <div class="stats-number" >$<span id="total_resta">0</span></div>
                                                </div>                                    
                                            </div>
                                            <div class="col-md-2">
                                                <div class="widget widget-stats bg-black">
                                                    <div class="stats-icon stats-icon-xs"><i class="fa fa-money fa-fw"></i></div>
                                                    <div class="stats-title">VUELTO</div>
                                                    <div class="stats-number" >$<span id="cambio">0</span></div>
                                                </div>                                    
                                            </div>
                                            
                                        </div>
                                </fieldset>
                            </form>
                             <form class="form-horizontal"  action="/" method="POST" id="tbl-pagos">                    
                            </form>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- end row -->
                    <!-- begin row carrito return-->    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="btn-group btn-group-justified" >
                                <a href="#default-tab-carrito" data-toggle="tab" class="btn btn-sm btn-primary m-r-5 "  onclick="ActivarTab('default-tab-carrito')">
                                    <i class="fa fa-chevron-circle-left"></i>
                                    CARRO DE COMPRAS (F7)
                                </a>                                                
                             </div>
                        </div>
                        <div class="col-md-5" ></div>
                        <div class="col-md-3" >
                            <div class="btn-group btn-group-sm" id="btn-acciones" >                            
                            <button type="button" class="btn btn-sm btn-success" id="btn-pendiente" onclick="valida_orden()" data-loading-text="<i class='fa fa-spinner fa-spin '></i> procesando..."><i class="fa fa-save"></i> SOLO GUARDAR</button>  &nbsp;
                             <?php if($conf_comprobantes==1){
                             ?>
                            <button type="button" class="btn btn-sm btn-primary" id="btn-pagar" onclick="valida_comp()" data-loading-text="<i class='fa fa-spinner fa-spin '></i> procesando...">
                                        <i class="fa fa-credit-card"></i> GENERAR COMPROBANTE</button>
                             <?php
                                }
                             ?>
                             </div>
                        </div>
                    </div>

                    <!--aca2--->
                    <!-- end row carrito return -->  
                      <form action='javascript:void(0)' method='POST' id="formmp" style="display:none">
                            <script id="tagscript"></script>
                        </form> 

                </div>
                <!--tab pago end -->
                <!--tab impresiones/ begin -->
                <div class="tab-pane fade" id="default-tab-impresiones">
                  <div class="row" >
                        <div class="col-md-3">
                          <form class="form-inline">
                            <div class="form-group">
                              <label for="linkmp">Link mercado pago</label>
                              <input type="text" class="form-control" id="linkmp" >
                            </div>    
                            <button class="btn btn-primary" id="copylinkmp"><i class="fa fa-unlink"></i></button>
                          </form>
                        </div>
                  </div>
                    <div class="row" id="tpl-table-print">
                        
                    </div>
                </div>
                <!--tab impresiones end -->

            </div>
        </div>
    </div>
<!-- end row tabs  -->

    

<div class="modal fade in" id="modal-parametros" style="display: none;">
</div>
<input type="hidden" id="tipos_productos_default" value="<?=$default?>">
</div>
<!-- end #content -->
<div id="modal-manual-busqueda" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" id="classModalLabel">
              Buscar  productos
            </h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" action="/" method="POST" id="form-avanzada" autocomplete="off" >                                 
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label>ID</label>
                                        <input type="text" class="form-control" id="inp_nro_tipo" placeholder="identificador del producto">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="inp_nombre" placeholder="nombre del producto">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Categoria</label>
                                        <select class="form-control" id="inp_id_categoria">
                                                                <option value=''>TODAS</option>
                                                                <?php foreach ($categorias as  $cat) {
                                                                    $id_categoria=$cat['id_categoria'];
                                                                    $categoria=$cat['categoria'];
                                                                  echo "<option value='$id_categoria'>$categoria</option>";
                                                                }
                                                                ?>
                                         </select> 
                                    </div>                    
                                </div>
                                <div class="form-group">
                                    <div class="col-md-8">
                                           <label>Codigo de barras</label>
                                            <input type="text" class="form-control" id="inp_codbarras" placeholder="ingrese codigo">                               
                                    </div>
                                    <div class="col-md-4"> 
                                    
                                        <button type="button" class="btn btn-primary btn-block" id="btn-buscar" data-loading-text="<i class='fa fa-spinner fa-spin '></i> buscando...">
                                            <i class="fa fa-search"></i>
                                        Buscar</button>
                                    </div>
                                </div>
                                 <div class="form-group" id="tpl-table-query">
                                 </div>
                            </form>
        
      </div>
      <div class="modal-footer">
        <div class="row">
            <div class="col-md-6">
                <div class="form-check form-check-inline text-left">
                    <input class="form-check-input" type="checkbox" id="modalvisible" value="1">
                    <label class="form-check-label" for="modalvisible">mantener ventana</label>
                    <input class="form-check-input" type="checkbox" id="seachvisible" value="1">
                    <label class="form-check-label" for="seachvisible">mantener resultados</label>
                </div>
                
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



<div id="modal-vendedor-seleccion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" >
              Seleccione vendedor para operar
            </h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" action="/" method="POST" id="form-vendedor" autocomplete="off" >                                 
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>Vendedores</label>
                                        <select class="form-control" id="inp_vendedor_opera">
                                                                
                                         </select> 
                                    </div>                    
                                </div>
                                
                            </form>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-warning" style="display: none" onclick="actualizar_vendedor()" id="btnactualizarvendedor" data-loading-text="<i class='fa fa-spinner fa-spin '></i> actualizando...">
                    ACTUALIZAR OPERADOR
                </button>                
        <button type="button" class="btn btn-sm btn-success" id="seleccionarvendedor">
                    SELECCIONAR
                </button>                
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">
                    CERRAR
                </button>                
              
      </div>
    </div>
  </div>
</div>


<div id="modal-cliente-pedido" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  
</div>


<script type="text/template" id="tpl-select_descuentos">   
<form class="form-horizontal">                      
  <div class="form-group">
                <label for="selec_descuentos">SELECCIONE TIPO DE DESCUENTO</label>                            
                  <select name="selec_descuentos" id="selec_descuentos" class="form-control">
                 <% _.each(ls, function(elemento) { %>
                <option value="<%=elemento.get('id_descuento')%>"><%=elemento.get('descripcion')%></option>
                <% })%>
                </select>
  </div>
  <div class="form-group">
    <label for="selec_descuentos">INGRESE EL VALOR</label>                            
    <input class="form-control" aria-label="ingrese valor" placeholder="0.00" id="inp_valor_descuento" value="" /></input>     
  </div>                          
</form>
</script>
<script type="text/template" id="tpl-select_recargos">   
<form class="form-horizontal">                      
  <div class="form-group">
            <label for="selec_recargos">SELECCIONE TIPO DE RECARGO</label>
                  <select name="selec_recargos" id="selec_recargos" class="form-control">
                 <% _.each(ls, function(elemento) { %>
                <option value="<%=elemento.get('id_recargo')%>"><%=elemento.get('descripcion')%></option>
                <% })%>
                </select>
  </div>
  <div class="form-group">
    <label for="inp_valor_recargo">INGRESE EL VALOR</label>                            
    <input class="form-control" aria-label="ingrese valor" placeholder="0.00" id="inp_valor_recargo" value="" /></input>
  </div>                          
</form>
</script>


<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalaplica" id="modal-aplica">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content" >
        <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modal-aplica-title"></h4>
        </div>
         <div class="modal-body" id="body-aplica">
         </div>
         <div class="modal-footer">
             <button type="button" class="btn btn-sm btn-success" id="btnaplicar">
                    APLICAR
                    <i class="fa fa-plus-circle"></i>
             </button>                
                <button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">
                    CERRAR
                </button>     
         </div>
    </div>
  </div>
</div>

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="<?=base_url()?>js/operaciones.js?v=<?php echo $version ?>"></script>
<script src="<?=base_url()?>js/operaciones_mod_clientes.js?v=<?php echo $version ?>"> </script>
<script src="<?=base_url()?>js/operaciones_mod_recargos.js?v=<?php echo $version ?>"></script>
<script src="<?=base_url()?>js/operaciones_mod_pagos.js?v=<?php echo $version ?>"> </script>
<script src="<?=base_url()?>js/operaciones_mod_descuentos.js?v=<?php echo $version ?>"></script>
<script src="<?=base_url()?>js/checkout.js?v=<?php echo $version ?>"></script>
<!--<script src="<?=base_url()?>js/operaciones_mod_clientes.js"> </script>
<script src="<?=base_url()?>js/operaciones_mod_recargos.js"></script>
<script src="<?=base_url()?>js/operaciones_mod_pagosV1.1.js"> </script>
<script src="<?=base_url()?>js/operacionesV1.3.js"></script>-->
<!--<script src="<?=base_url()?>js/operaciones_mod_descuentos.js"></script>-->
<!-- ================== END PAGE LEVEL JS ================== -->
<script type="text/javascript">


$(document).on('shown.bs.tab','a[href="#default-tab-carrito"]', function (e) {
        if(!isMobile()){
         $("#buscador").focus();       
        }    
    
    })

$(document).on('shown.bs.tab','a[href="#default-tab-pago"]', function (e) {    
        if(!isMobile()){
        $("input[name=monto_abona]").focus();
        }
    })

$(document).ready(function(){   

 
if(!isMobile()){
    $("#buscador").focus();
}    
   $(document).keydown(function(e) {    
      switch (e.which){
        case 117: //f6
        //$('#modal-manual-busqueda').modal('show')
        click_btnmanual()
        break;
        case 118: //f7
        ActivarTab("default-tab-carrito")        
        break;
        case 119: //f8
        ActivarTab("default-tab-pago")
        break;
      }
   });
   
})



$('#modal-manual-busqueda').on('hidden.bs.modal', function (e) { 
    if(!isMobile()){   
  $("#buscador").focus();
    }
})
$("#btn-manual-busqueda").click(function(){
    
    click_btnmanual()
})


function ActivarTab(tabname){
    switch (tabname){
        case "default-tab-carrito":        
        $('a[href="#default-tab-carrito"]').tab('show');         
        break;
        case "default-tab-pago":
        $('[data-toggle="popover"]').popover('hide');
        $('a[href="#default-tab-pago"]').tab('show'); 
        break;
    }
}

//accion al agregar boton de mercadopago 
$(document).bind('DOMNodeInserted', function(e) {
  var element=e.target;
  if(element.className=="mercadopago-button"){
    e.preventDefault();
    e.stopPropagation();
    $(".mercadopago-button").trigger("click")
    return
  }                 
})

$("#btn-info").click(function(){
    var texto="[nro_producto],[cantidad*nro_producto],[codigobarras] o si tipo de producto está seleccionado[cantidad*preciogenerico]"
    swal("metodos abreviados",texto,"info")
})

<?php

if($visitante->permitir('prt_operaciones',8)):?>

    ofwlocal.getAsync("verusuarios_sucursales",Array("id_usuario","usuario","apellido","nombres"),"id_empresa="+$("#id_empresa").val()+" and habilitado=1 and id_rol IN(6,2)","usuario",function(rs){
        
        for(r in rs){
            var rw=rs[r]
           $("#inp_vendedor_opera").append("<option value='"+ rw["id_usuario"]+"'>"+ rw["usuario"]+" ("+ rw["apellido"]+", "+ rw["nombres"]+")</option>")
        }
    });


function actualizar_vendedor(){
swal({
          title: "Atención",
          text: "¿Desea actualizar el vendedor de esta venta?",
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
            }, function () {
                $("#modal-vendedor-seleccion").modal("hide");
                $("#btnactualizarvendedor").button("loading")
                var id_usuario=$("#inp_vendedor_opera").val()
                var base_url=$("#base_url").val()
                $.ajax({url: base_url+"index.php/operaciones/ventas/actualizar_operador",
                type: 'POST', dataType: "json",data: {id_vendedor_opera:id_usuario,id_comp:$("#id_comp").val()} ,
                success: function(jsonResponse){   
                    
                $("#btnactualizarvendedor").button("reset")              
                    if(jsonResponse.numerror==0){
                      swal({title:"Actualizado!",
                                            timer: 3000,
                                            text: "",
                                            type: "success"})
                    }else{
                       swal("Atención","hubo un problema al guardar información. Consulte con el administrador","error")
                    return   
                    }
                }
                })//ajax
            
            });


}   //actualizar vendedor 

<?php
endif;
?>
var acciononview="";
function aplicar(accion){
    if(accion=="descuento"){
        $("#modal-aplica").modal("show")
        oDescuentosView.render();

    }
    if(accion=="recargo"){        
        $("#modal-aplica").modal("show")
        oRecargosView.render();
    }
    acciononview=accion
}

$("#btnaplicar").click(function(e){
    e.preventDefault();
    if(acciononview=="descuento"){
        oDescuentosView.calcular();
    }
    if(acciononview=="recargo"){
        oRecargosView.calcular();
    }
    $("#modal-aplica").modal("hide")
})
</script>
