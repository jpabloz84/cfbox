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
    $deuda=$totales[0]['deuda']; //$importe_total-$importe_pago;
}

?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content">     
    <input type="hidden" id="base_url" value="<?=base_url()?>" />
    <input type="hidden" id="base_fw" value="<?=BASE_FW?>" />
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="btn-group pull-left">
                        <button type="button" class="btn btn-success btn-xs" onclick="crear_pago()">
                            <i class="fa fa-credit-card"></i>
                            CREAR PAGO
                        </button>
                        </div>
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CUENTA CORRIENTE DE <?php echo strtoupper($cliente['strnombrecompleto']) ?></h4>
            </div>             
            <div class="panel-body" id="panel-body-saldos">
                    <div class="col-md-1">
                        <button  class="btn btn-warning btn btn-sm" id="btn-volver">
                            <i class="fa fa-reply"></i>
                            VOLVER</button>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-stats bg-black">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-money"></i></div>
                            <div class="stats-title">SALDO A FAVOR</div>
                            <div class="stats-number">$<span id="saldo"><?=$saldoafavor?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" onclick="imputar()">IMPUTAR SALDO &nbsp;<i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>                
                    <div class="col-md-4">
                        <div class="widget widget-stats bg-black">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-shopping-cart"></i></div>
                            <div class="stats-title">TOTAL FACTURADO</div>
                            <div class="stats-number">$<span id="totalfacturado"><?=$importe_total?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" onclick="detallefacturado()">CONSULTAR DETALLES&nbsp;<i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget widget-stats bg-red">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-institution"></i></div>
                            <div class="stats-title">DEUDA</div>
                            <div class="stats-number">$<span id="totaladeudado"><?=$deuda?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" onclick="detalledeudas()">COMPROBANTES ADEUDADOS&nbsp;<i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>
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
                        <div class="col-md-2">
                            <label>TIPO DE PAGO
                                <select id="inp_id_tipo_pago" name="inp_id_tipo_pago" onchange="verificar_params()" class="form-control">
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
                            </label>
                        </div>
                        <div class="col-md-3">                            
                            <label>IMPORTE
                                <input type="text" name="inp_monto_abona" id="inp_monto_abona" class="form-control" placeholder="ingrese monto..." />
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label id="lbpago" style="display: none"><br/>DATOS DEL PAGO
                            <button type="button" class="btn btn-sm btn-warning" name="btn-info-pago" id="btn-info-pago" onclick="mostrar_params()" style="display: none">
                                <i class="fa fa-info"> VER</i>
                            </button>
                            </label>
                        </button>
                        </div>
                        <div class="col-md-3">
                            <label>OBSERVACIÓN
                            <input type="text" name="inp_observacion" id="inp_observacion" class="form-control" placeholder="observacion..." class="form-control" />
                        </label>
                        </div>    
                        <div class="col-md-2">
                        <button type='button' id="save-pago" class='btn btn-success btn btn-sm'  class="form-control" onclick="agregar_pago()">
                                <i class='fa fa-save' data-loading-text="<i class='fa fa-spinner fa-spin '></i> procesando..." ></i>
                                AGREGAR PAGO</button>
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
<!-- end #content -->
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
                        var strpago='N° '+elemento.get('id_pago')+' - $ ' + elemento.get('pago') + ' ('+ elemento.get('fe_pago') + ')';

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
<script>
App.restartGlobalFunction();
App.setPageTitle('Consulta de cuenta corriente| Coffee APP');
var base_url='<?=base_url()?>index.php/'
var ofwlocal=new fw(base_url)
var win= new fwmodal();
ofwlocal.guardarCache=false;

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
                                
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/cuenta/"+(<?=$id_cliente?>).toString());
                             })
                          });
                        });
                    });
                });
            });
        });
    });
});
var oColecciones=null;
var oDetalles=null;
var olista=null;
var olsDetalles=null;
var ocampoView=null;
var ocampopagoView=null;
var oPagosView=null;
var oPagoadd=null;
var oPagos=null;
var eventos = _.extend({}, Backbone.Events);
var viendopago=true;

var Pago=Backbone.Model.extend({    
     url:'<?=base_url()?>index.php/operaciones/pagos/listener',
    default:{
        id_tipo_pago:1,//tipo pago seleccionado por defecto        
        monto_abona:0,
        observacion:'',
        tipospagos:null,
        eliminable:true,
        fe_estado_pago:'',
        anulado:false,
        id_usuario_estado:0,
        id_pago:0,
        fe_pago:'',  
        strnombrecompleto:'',
        observacion:'',          
        usuario:'',
        sucursal:'',  
        usuario_estado:'',
        anulado:'',
        fe_estado_pago:''
    }
});//itempago
var Pagos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
        this.tipospagos=globales['tipo_pagos']
    },
    loadAsync:function(patrones){
        var that=this;
        that.reset();
        var cond=" id_cliente=<?php echo $id_cliente ?>";
        var id_pago=0;
        if(typeof patrones['id_pago'] != "undefined")
        {         
            if(patrones['id_pago']!=""){
            id_pago=parseInt(patrones['id_pago']);
            cond+=" and id_pago="+patrones['id_pago'];         
            }
        }

        if(typeof patrones['anulados'] != "undefined")
        {         
            if(!patrones['anulados']){            
            cond+=" and estado<>'A'"
            }
        }

        if(typeof patrones['id_proceso'] != "undefined")
        {
        cond+=" and id_proceso="+patrones['id_proceso'];    
        }
        if(typeof patrones['fecha_desde'] !="undefined"){
            if(patrones['fecha_desde']!="" && !(id_pago>0))
            {var strdate=todate(patrones['fecha_desde'],103);
                cond+=' and fe_pago>="'+strdate+'"';
            }    
        }
        
        if(typeof patrones['fecha_hasta'] != "undefined"){
            if(patrones['fecha_hasta']!="" && !(id_pago>0))
            {
                var strdate=todate(patrones['fecha_hasta'],103);
                cond+=" and fe_pago< ADDDATE('"+strdate+"',1)";
            }    
        }
        
        /*
        if(typeof patrones['condeuda'] != "undefined")
        {   if(patrones['condeuda']){
                cond+=' and deuda>0';
            }
            
        }*/
        

        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_pagos",this);
        }        
        var cmp=Array("id_pago","pago","DATE_FORMAT(fe_pago,'%d/%m/%Y %r') AS fe_pago","id_cliente","observacion","id_tipo_pago","tipo_pago","id_usuario","usuario","estado as estado_pago","id_usuario_estado","usuario_estado","DATE_FORMAT(fe_estado,'%d/%m/%Y %r') AS fe_estado_pago","imputado","resta_imputar","id_proceso","btnclass_pago","estado_pago_desc");
        ofwlocal.getAsync("verpagos_imputar",cmp,cond,"id_pago",function(rs){             
            that.cargar(rs,that) 
        })
    },    
    cargar:function(rs,that)
    {   
     for (c in rs)
     {
        var tps=this.tipospagos.findWhere({id_tipo_pago:rs[c]['id_tipo_pago']});
        rs[c]['parametros']=tps.get('parametros');
      that.add(rs[c])
     }
       if(typeof that.options.eventos !="undefined")
        {
        that.options.eventos.trigger("endload_pagos",that);
        }
    },
    model:Pago
});

var PagosView=Backbone.View.extend({   
    initialize:function(options)
    {
        this.options=options || {};
        this.onview=false;
    },
    render:function(patrones)
    {   
        var that=this;
        if(oPagos ==null)
        {
        oPagos=new Pagos({eventos:eventos});    
        }
        eventos.on("initload_pagos",this.loading,this)
        eventos.on("endload_pagos",this.endload,this)                
        oPagos.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-consultar-pagos'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },

    events:{
    "click a[id*='id_imputado_']":'vercomp_afectados',
    "click a[id*='pago-link-']":'verpago',
    "click a[id*='recibo-link-']":'verrecibo',
    "click a[id*='id_tipo_pago_']":'verparams'},
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-pagos-list').html());                
        this.$el.html(tpl({ls:olist}));
         spinnerEnd($('#panel-body-consultar-pagos'));
    },
    vercomp_afectados:function(e){        
        event.stopPropagation();
        if(this.onview){
            return;
        }
        
        this.onview=true;
        var i=e.target.id.indexOf('id_imputado_')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('id_imputado_','')        
        }else{
            id_model=e.target.parentNode.id.replace('id_imputado_','')        
        }
        
        var id_pago=(oPagos.get(id_model)).get("id_pago")
        var cmp=Array("id_comp_pago","id_comp","monto","id_pago","pago","DATE_FORMAT(fe_pago,'%d/%m/%Y %r') as fe_pago","id_tipo_pago","tipo_pago","id_usuario","usuario","id_sucursal","sucursal","id_cliente","strnombrecompleto","afip","nro_comp","nro_talonario","descripcion","importe_total","importe_pago","deuda","tipo_comp");
        
        var rs=ofwlocal.get("vercomprobantes_pagos",cmp,"id_pago="+id_pago,"");
        if(rs.length>0)
        {
        var titulo= " COMPROBANTES AFECTADOS CON PAGO N° "+rs[0]['id_pago']
        cuerpo="<ul>"
        for(c in rs){
            var comp=rs[c];
            var strcomp='ORDEN DE PEDIDO N° '+comp.id_comp;
            if(comp.afip=="1"){
                if(comp.nro_talonario=="" || comp.nro_talonario==null){
                    comp.nro_talonario=0;
                }
                if(comp.nro_comp=="" || comp.nro_comp==null){
                    comp.nro_comp=0;
                }
                strcomp=comp.tipo_comp+" N° "+format_number(comp.nro_talonario,"0000")+" - "+format_number(comp.nro_comp,"00000000")+" ("+comp.id_comp+")";
            }
            cuerpo+="<li>"+strcomp+" $"+rs[c]['monto']+" de $"+rs[c]['importe_total']+"</li>"
        }
        cuerpo+="</ul>"
        win.alert(cuerpo,titulo,3)    

        }
    this.onview=false;
    },//ver
    verrecibo:function(e){                
        
        $("#row-pagos").hide();
        var permite='VED'
        
        var i=e.target.id.indexOf('recibo-link-')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('recibo-link-','')        
        }else{
            id_model=e.target.parentNode.id.replace('recibo-link-','')        
        }        
        var modelo=oPagos.get(id_model);    
        
        window.open('<?php echo base_url() ?>index.php/operaciones/pagos/pdfpago/'+modelo.get('id_pago')+'/','_blank');
        return ;
    },
    verpago:function(e,modo='V'){        
        e.stopPropagation();
        if(this.onview){
            return;
        }
        $("#row-pagos").hide();
        var permite='VED'
        this.onview=true;
        var i=e.target.id.indexOf('pago-link-')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('pago-link-','')        
        }else{
            id_model=e.target.parentNode.id.replace('pago-link-','')        
        }
        
        var modelo=oPagos.get(id_model)
        var oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modelo.get('id_pago'),nombre:'id_pago',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);        
        cCampo=new Campo({valor:"$ "+modelo.get('pago'),nombre:'pago',tipo:'readonly',etiqueta:'Monto Abonado',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);        
        cCampo=new Campo({valor:modelo.get('fe_pago'),nombre:'fe_pago',tipo:'readonly',etiqueta:'Fecha de pago',esdescriptivo:false,obligatorio:false});
        var tipobs='text'
        if(modelo.get('estado')=="A")
        {   permite='V'
            tipobs='readonly'
        }else{
            //si no tiene permisos para elmiinar, que solomuestre la visualizacion o edicion
            /*if(permitir(prt_pagos,8)){
                permite='VED'
            }else{
                
            }*/
            permite='VE'
        }
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('observacion'),nombre:'observacion',tipo:tipobs,etiqueta:'Observación',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('usuario'),nombre:'usuario',tipo:'readonly',etiqueta:'Cajero',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        var strestado=modelo.get('estado_pago_desc')+" - fecha: "+modelo.get('fe_estado_pago')+" - usuario: "+modelo.get('usuario_estado') 
        cCampo=new Campo({valor:strestado,nombre:'usuario_estado',tipo:'readonly',etiqueta:'Estado del pago',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);    
        

        var parametros=modelo.get('parametros')
        if(parametros.length>0)
        {   //mparams=parametros.models
            for(p in parametros){
                param=parametros[p]
                cparams=new Campo({valor:param.get('parametro_valor'),nombre:param.get('parametro'),tipo:'text',etiqueta:param.get('descripcion'),esdescriptivo:false,obligatorio:false});
                oCampos.add(cparams);
            }
            
        }
        
       var that=this;
       viendopago=true;
       if(ocampopagoView ==null)
       {
           ocampopagoView=new campoView({campos:oCampos,modelo:modelo,permite:permite,base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
           ocampopagoView.options.volver=function(){

            if(!viendopago) 
            return
                        $("#row-pagos").show();    
                        $("#row-body").hide();    
                        $("#row-body").html("");
                        if (!$("#panel-body-pagos").is(":visible")) { //si no se ve, que lo abra
                            $("#panel-body-pagos").slideToggle();            
                        }
                        if (!$("#panel-body-consultar-pagos").is(":visible")) { //si no se ve, que lo abra
                            $("#panel-body-consultar-pagos").slideToggle();            
                        } 
                        ocampopagoView=null;
             }//volver
             

            ocampopagoView.options.onafterrender=function(){
                event.stopPropagation()  
                
                var mo=ocampopagoView.options.modelo                        
                if(permitir(prt_pagos,8)){
                 $("#botonera2").html("<button type='button' id='eliminarpago' class='btn btn-sm btn-danger'><i class='fa fa-eraser'></i>ANULAR PAGO</button>"); 
                $("#eliminarpago").click(function(){                
                   var id_pago=mo.get('id_pago')
                   that.confirmarAnulaPago(id_pago)
                })
                }//if para boton eliminar
            
            }//afterrenderd
        }
        else
        {
            ocampopagoView.options.campos=oCampos
            ocampopagoView.options.modelo=modelo
        }
         
        if ($("#panel-body-pagos").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-pagos").slideToggle();            
        }
        if ($("#panel-body-consultar-pagos").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-consultar-pagos").slideToggle();            
        } 
              
        ocampopagoView.render(modo)
        if(modo=='A' || modo=='E')
        {
            ocampopagoView.options.validar=function(e)
            {
                return true;
            }
        }

        ocampopagoView.options.success=function(e)
        {   spinnerEnd($("#panel-body-view"))          
            
            if (!$("#panel-body-pagos").is(":visible")) { //si estacerrado, que lo abra
                    $("#panel-body-pagos").slideToggle();            
            }


            if (!$("#panel-body-consultar-pagos").is(":visible")) { //si estacerrado, que lo abra
                    $("#panel-body-consultar-pagos").slideToggle();            
            }
            $("#row-body").hide();    
            $("#row-body").html("");
            consultar();
        }
        ocampopagoView.options.before=function()
        {
            spinnerStart($("#panel-body-view"))          
        }//antes
        ocampopagoView.options.error=function()
            {
                spinnerEnd($('#panel-body-view'));
                
             consultar();
            }

        
    this.onview=false;
    },//ver
    confirmarAnulaPago:function(id_pago){
        var that=this        
        win.dialog("Tenga en cuenta que este pago puede estar imputado a algún comprobante. Los cambios son irreversibles.",'¿Desea eliminar realmente este pago?',4, function(t){            
                that.eliminarPago(t);
         },id_pago);
    },
    eliminarPago:function(id_pago){
    
        $.ajax({url:'<?=base_url()?>index.php/operaciones/pagos/anular',
                    type: "post",
                    dataType: "json",
                    data: {id_pago:id_pago},                    
                    beforeSend: function(){
                        
                        spinnerStart($("#panel-body-consultar-pagos"));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body-consultar-pagos'));
                    },
                    error:function(){
                      spinnerEnd($('#panel-body-consultar-pagos'));
                      console.log("Hubo error en ajax");  
                    }
                    })
                    .done(function(response){
                        
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;
                        if(numError == 0){
                             win.alert("El pago se anuló correctamente."," Realizado",1) 
                            ocampopagoView.options.volver();
                            consultar();
                            }else{
                                win.alert("Hubo una falla al consultar. Consulte con el administrador "," Atención",3) 
                            }                          
                        
                    });


    },//eliminar pago
    verparams:function(e){
        event.stopPropagation();
        if(this.onview){
            return;
        }
        this.onview=true;
        var i=e.target.id.indexOf('id_tipo_pago_')
        var identificador=''
        if(i>=0)
        {
            identificador=e.target.id.replace('id_tipo_pago_','')        
        }else{
            identificador=e.target.parentNode.id.replace('id_tipo_pago_','')        
        }
        var ids=identificador.split("-")
        var id_pago=ids[0]
        var id_tipo_pago=ids[1]
        var cmp=Array("id_pago","nro_parametro","valor","id_tipo_pago","tipo_pago","parametro","tipo_dato","descripcion","parametro_valor")
        var rs=ofwlocal.get("verparametros_pago",cmp,"id_tipo_pago="+id_tipo_pago+" and id_pago="+id_pago," descripcion");
        
        var titulo= " PARAMETROS PAGO "+rs[0]['tipo_pago']
        var cuerpo="<ul>"
        for (i in rs)
        {
        cuerpo+="<li>"+rs[i]['descripcion']+": "+rs[i]['parametro_valor']+"</li>"
        }
        cuerpo+="</ul>"
        win.alert(cuerpo,titulo,3)    
        this.onview=false;
    }

})//pagos view

var Pagosparametrosview=Backbone.View.extend(
 {   el:$('#tbl-pagos'),
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(pago,tipopago,bancos,tarjetas)
    { 
        var that=this;
        this.pago=pago;
        this.tipopago=tipopago;
        var mbancos=bancos.models;

        var mtarjetas=tarjetas.models;
        var parametros=tipopago.get('parametros');
        $.get(this.options.base_url+'tpl/opera_pago_parametros.html', function (data) {
            tpl = _.template(data, {});
            
            htmlrender=tpl({campos:parametros,bancos:mbancos,tarjetas:mtarjetas,base_url:that.options.base_url})
            that.$el.html(htmlrender);
            
            for (i in parametros)
            {
                if(parametros[i].get('parametro') !='id_banco' && parametros[i].get('parametro') !='id_tarjeta')
                {
                   if(parametros[i].get('tipo_dato')=='int')
                   {    
                        $("#"+parametros[i].get('parametro')).keypress(function(e){ return teclaentero(e)})
                        
                   }
                   if(parametros[i].get('tipo_dato')=='float')
                   {
                    $("#"+parametros[i].get('parametro')).keypress(function(e){ return teclamoney(e)})
                   }
                }
            }
        })
        
    },
    events:{            
            "click #save-params":'validateparams'
    },
    validateparams:function(){
        
         var strhtml=""
         var parametros=this.tipopago.get('parametros');
        for (i in parametros)
        { var parametro=parametros[i].get('parametro')
          var tipo_dato=parametros[i].get('tipo_dato')
            var desc=parametros[i].get('descripcion')
            var valor=$("#"+parametro).val();
            if(valor=='')
            {
                strhtml+="<li>No ha completado "+desc+"</li>"
            }
            if(tipo_dato=="int")
            {
                if(!es_entero(valor))
                    {strhtml+="<li>"+desc+" no tiene un valor correcto</li>"}
                if(es_entero(valor) && parametro=="mes_vencimiento" && !(parseInt(valor)>=1 && parseInt(valor)<13))
                {
                    strhtml+="<li>"+desc+" debe estar comprendido entre 1 y 12</li>"
                }

            }
            if(tipo_dato=="float")
            {
                if(!es_numero(valor))
                    strhtml+="<li>"+desc+" no tiene un valor correcto</li>"
            }
        }
        if(strhtml!="")
        {   strhtml="<ul>"+strhtml+"</ul>"
            that=this;
            win.dialog(strhtml,'¿Desea continuar de todas formas?',4, function(t){            
                                        t.saveparams(parametros);
                },that); 
        }else{
            this.saveparams(parametros);
        }
    },
    saveparams:function(parametros){
        $("#modal-parametros").modal("hide");        
        for (i in parametros)
        { var parametro=parametros[i].get('parametro')            
          var valor=$("#"+parametro).val();          
          parametros[i].set({valor:valor})
        }
        this.tipopago.set({parametros:parametros})
        var tipospagos=this.pago.get("tipospagos");
        for (i in tipospagos.models)
        {   //busco el objeto tipo pago  seleccionado en this.pago para asignarle el nuevo tipo con sus parametros
            if(this.pago.get("id_tipo_pago")==tipospagos.models[i].get("id_tipo_pago"))
            {
                tipospagos.models[i]=this.tipopago;
                break;
            }
        }
        this.pago.set({tipospagos:tipospagos});        
        oPagoadd=this.pago;
    }

})



function inicializacion_contexto()
{
eventos.off();
$("#fecha_desde").mask("99/99/9999");
                $("#fecha_desde").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

$("#fecha_hasta").mask("99/99/9999");
                $("#fecha_hasta").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });
$("#btn-volver").click(function(e){    
    event.stopPropagation();
     handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/")
})

$("#id_pago").keypress(function(e){
    return teclaentero(e)
})
$("#inp_monto_abona").keypress(function(e){
    return teclamoney(e)
})


oPagosView=new PagosView({el:$('#row-pagos')});                
oPagosparametros= new Pagosparametrosview({el:$('#modal-parametros'),base_url:'<?=base_url()?>'})
$("#btn-consultar").click(function(e){    
    event.stopPropagation();
     consultar();
})

$("#btn-exportar").click(function(e){    
    event.stopPropagation();
     exportar();
})



display('btnconsultarpagos');
olista=new ElementosView({el:$('#row-det')});
}//inicializacion contexto

function detallefacturado(){    
    handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/comprobantes/<?php echo  $id_cliente ?>")
}

function consultar()
{
 /*$("#row-det").hide(); 
 $("#row-pagos").show();      */

 display('consultaspagos');
var patrones={fecha_desde:$("#fecha_desde").val(),fecha_hasta:$("#fecha_hasta").val(),id_pago:$("#id_pago").val(),anulados:$("#anulados").is(":checked")} 
/*se dibuja la consulta de los pagos*/
 oPagosView.render(patrones);
}
function exportar(){
    var patrones={fecha_desde:$("#fecha_desde").val(),fecha_hasta:$("#fecha_hasta").val(),id_pago:$("#id_pago").val(),anulados:($("#anulados").is(":checked"))?1:0,id_cliente:<?=$id_cliente?>}
    $.ajax({url:'<?=base_url()?>index.php/operaciones/pagos/exportar',
                    type: "post",
                    dataType: "json",
                    data: patrones,                    
                    beforeSend: function(){
                       spinnerStart($('#panel-body-consultar-pagos'));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body-consultar-pagos'));
                    },
                    error:function(){
                      spinnerEnd($('#panel-body-consultar-pagos'));  
                    }
                    })
                    .done(function(response){                        
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;                                                

                        if(numError == 0)
                        {                            
                          window.open("<?php echo base_url() ?>"+descError,'_blank');
                        }else{
                                win.alert(descError," La exportación falló ",4) 
                        }
                    });
}//exportar


function crear_pago(){
  event.stopPropagation();
  /*$("#row-pago-add").show();*/
  display('btncargapago');
  var tp=new Tipopagos();
  oPagoadd=new Pago({id_tipo_pago:1,monto_abona:0,observacion:'',tipospagos:tp,eliminable:false,disabled:false ,id_cliente:<?=$id_cliente?>});
  $("#inp_id_tipo_pago").val(1);
  $("#inp_monto_abona").val("");
  $("#inp_observacion").val("");

 
}

function imputar(){
  event.stopPropagation();
var saldo=parseFloat($("#saldo").html());
if(saldo<=0){
    win.alert("No hay saldo disponible para realizar esta acción"," Atención",4)
    return
}
      /*win.dialog('A continuación usted va a imputar pagos a comprobantes adeudados con el saldo a favor que disponga el cliente. ¿Desea cotinuar?',' Atención',3, function(mr){
                    send_inputar()
                },null);*/ 

        swal({
          title: "Atención",
          text: 'A continuación usted va a imputar pagos a comprobantes adeudados con el saldo a favor que disponga el cliente. ¿Desea cotinuar?',
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
            }, function () {
           send_inputar()
            });


}
function send_inputar(){
//spinnerStart($("#panel-body-saldos"))
$.ajax({url:'<?=base_url()?>index.php/operaciones/pagos/imputar',
                    type: "post",
                    dataType: "json",
                    data: {id_cliente:<?=$id_cliente?>},                    
                    beforeSend: function(){
                       //spinnerStart($('#panel-body-saldos'));
                    },
                    complete: function(){
                        //spinnerEnd($('#panel-body-saldos'));
                    },
                    error:function(e){
                      swal("Error al imputar","Hubo un error en el envio. consulte con el administrador "+e.responseText, "error")

                    }
                    })
                    .done(function(response){
                        //spinnerEnd($('#panel-body-saldos'));

                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;                        
                        

                        if(numError == 0)
                        {
                            var id_proceso=parseInt(response.data.id_proceso);
                            if(id_proceso>0)
                            {var patrones=Array();
                                $("#row-pagos").show();
                                patrones['id_proceso']=id_proceso                                  
                                /*win.alert("El proceso se realizó correctamente. A continuación el detalle de los comprobantes  afectados"," Proceso exitoso",1) */
                                var importe_total=response.data.importe_total
                                var deuda=response.data.deuda
                                var saldoafavor=response.data.saldoafavor;
                                $("#totalfacturado").html(format_number(importe_total,"#.00"))
                                $("#totaladeudado").html(format_number(deuda,"#.00"))
                                $("#saldo").html(format_number(saldoafavor,"#.00"))
                                swal("Excelente!","El proceso se realizó correctamente. A continuación el detalle de los comprobantes  afectados", "success")
                                oPagosView.render(patrones);
                            }else{
                                swal("Atencion!","El proceso de imputación a deudas no se realizó. Puede que no haya saldo pendiente o comprobantes con saldo", "info")

                                /*win.alert("El proceso no se realizo. Puede que no haya saldo pendiente o comprobantes con saldo "," Atención",3) */
                            }
                          
                        }else{
                                //win.alert("El proceso no se realizo."+descError," El proceso falló",4) 
                                swal("Atencion!","El proceso de imputación a deudas no se realizó. "+descError, "error")
                        }
                    });

}


function verificar_params(){
         var id_tipo_pago=$("#inp_id_tipo_pago").val();
        //si lo anterior tiene parametros, es porque hay que completar mas info, sino, nada
        var tipopago=(oPagoadd.get("tipospagos")).findWhere({id_tipo_pago:id_tipo_pago.toString()})
         params=tipopago.get("parametros");
         if(params.length>0)
         { $("#lbpago").show();
            $("#btn-info-pago").show();
            oPagosparametros.render(oPagoadd,tipopago,globales['bancos'],globales['tarjetas'])
            $("#modal-parametros").modal("show");
         }else{
            $("#btn-info-pago").hide();
            $("#lbpago").hide();
         }

}

function mostrar_params(){
    var id_tipo_pago=$("#inp_id_tipo_pago").val();
        //si lo anterior tiene parametros, es porque hay que completar mas info, sino, nada
        var tipopago=(oPagoadd.get("tipospagos")).findWhere({id_tipo_pago:id_tipo_pago.toString()})
    $("#btn-info-pago").show();
    oPagosparametros.render(oPagoadd,tipopago,globales['bancos'],globales['tarjetas'])
    $("#modal-parametros").modal("show");
}
function agregar_pago(){
event.stopPropagation();
var monto_abona=$("#inp_monto_abona").val();
if(!es_numero(monto_abona))
{
    //win.alert("El monto ingresado, es invalido"," Atención",4)
    swal("Atención","El monto ingresado es invalido","error")
    return
}
monto_abona=parseFloat(monto_abona);
if(monto_abona<=0){
 //win.alert("El monto no puede ser menor a cero"," Atención",4)
 swal("Atención","El monto no puede ser menor a cero","error")
    return   
}

    swal({
          title: "Atención",
          text: 'Este pago impactará directamente sobre la cuenta corriente del cliente. ¿Desea cotinuar?',
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
            }, function () {
           send_pago()
            });

      


}

function send_pago(){

//spinnerStart($("#panel-body-cargar-pago"))
var id_tipo_pago=$("#inp_id_tipo_pago").val();
oPagoadd.url= '<?=base_url();?>index.php/operaciones/pagos/generar/'
oPagoadd.set({"monto_abona":$("#inp_monto_abona").val(),"observacion":$("#inp_observacion").val(),"id_tipo_pago":$("#inp_id_tipo_pago").val()})
$("#save-pago").button("loading")
oPagoadd.save(null,{
               type:'POST', 
                    success:function(e,params){                            
                        $("#save-pago").button("reset")
                                if(params.numerror!=0)
                                {   swal("Error al generar pago","Detalle: "+params.descerror+". Consulte con el administrador", "error")
                                    
                                    
                                }
                                else
                                {   
                                    
                                var importe_total=params.data.importe_total
                                var deuda=params.data.deuda
                                var saldoafavor=params.data.saldoafavor;
								var pagos=params.data.id_pago;
                                
                                 if(id_tipo_pago==8){
                                    generarpreferenciaMP(pagos[0])
                                }
                                $("#totalfacturado").html(format_number(importe_total,"#.00"))
                                $("#totaladeudado").html(format_number(deuda,"#.00"))
                                $("#saldo").html(format_number(saldoafavor,"#.00"))
                                $("#row-pago-add").hide();  
                                $("#inp_id_tipo_pago").val(1);
                                $("#inp_monto_abona").val("");
                                $("#inp_observacion").val("");
                                swal({
                                    title: "EL PAGO SE REALIZÓ CON EXITO",
                                    text: '¿Desea imputar este saldo a favor?',
                                      type: "info",
                                      showCancelButton: true,
                                      closeOnConfirm: false,
                                      showLoaderOnConfirm: true
                                    }, function () {
                                   send_inputar()
                                    });
                                             
                                 
                                }
                            },
                    error: function(e,params) {                        
                        $("#save-pago").button("reset")
                                    win.alert(params.responseText,"Error",4)
                                    
                    },
                    wait:true
                    })//save


}

function detalledeudas(){
 /*$("#row-det").show();
 $("#panel-body-pagos").hide(); */
 display('compadeudados')
 /*if ($("#panel-body-consultar-pagos").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body-consultar-pagos").slideToggle();            
}*/
//dibuja y consulta los detalles de los comprobantes adeudaoes
 olista.render();

}

/*elemento que se encarga de recolectar los comprobantes adeudados para la vista que muestra detalles de la deuda*/
var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>',   
    idAttribute:'id_comp',
    defaults:{
        id_comp:0,
        id_talonario:0,
        nro_comp:0,
        nro_talonario:0,
        id_cliente:0,
        strnombrecompleto:'',
        domicilio:'',
        telefono:'',
        importe_total:0,
        importe_neto:0,
        importe_iva:0,
        importe_exento:0,
        importe_descuento:0,
        fe_creacion:'',
        fe_comp:'',
        fe_vencimiento:'',
        cuit:'',
        estado:'',
        usuario_estado:'',
        usuario:'',
        afip:'',
        sucursal:'',
        condicion:'',
        tipo_comp:'',
        tipo_comp_afip:''
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        var that=this;
        that.reset();
        var id_cliente=$("#id_cliente").val();
        var cond="id_cliente=<?php echo $id_cliente ?> and deuda>0 and (id_tipo_comp in(1,2,5) or id_tipo_comp is null) and estado in('E','F')"
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_comp",this);
        }        
        var cmp=Array("id_comp","id_talonario","IFNULL(nro_comp,0) as nro_comp","IFNULL(nro_talonario,0) as nro_talonario","cae","id_cliente","strnombrecompleto","domicilio","telefono","nro_docu","tipo_docu","documento","tipo_persona_desc","descripcion_loc","descripcion_pro","importe_total","importe_neto","importe_base","importe_iva","importe_exento","importe_descuento","DATE_FORMAT(fe_creacion,'%d/%m/%Y %r') as fe_creacion","DATE_FORMAT(fe_comp,'%d/%m/%Y %r') as fe_comp","DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento","cuit","estado","descripcion","estadoclass","usuario_estado","usuario","afip","id_sucursal","sucursal","id_cond_iva","condicion","id_tipo_comp","tipo_comp","tipo_comp_afip","importe_pago","path_comp","id_comp_anula","deuda");
        ofwlocal.getAsync("vercomprobantes",cmp,cond,"fe_creacion asc",function(rs){ 
            
            that.cargar(rs,that) 
        } )
    },    
    cargar:function(rs,that)
    {       
     for (c in rs)
     {
      that.add(rs[c])
      }
        if(typeof that.options.eventos !="undefined")
        {
         that.options.eventos.trigger("endload_comp",that);
        }
    },
    model:Elemento
});

/*Vista utilizada para mostrar los resultados del boton "ver detalles" de los comprobantes adeudadoes*/
var ElementosView=Backbone.View.extend({   
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    
        }
        eventos.on("initload_comp",this.loading,this)
        eventos.on("endload_comp",this.endload,this)
        oColecciones.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-saldos'));  
      //spinnerStart($('#panel-body-comprobantes'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },
    events:{
            "click a[id*='view-link-']":'ver'
    },
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));         
         //spinnerEnd($('#panel-body-comprobantes'));
         spinnerEnd($('#panel-body-saldos'));  
         $('[data-click="panel-remove"]').off();
         $('[data-click="panel-remove"]').click(function(){
            display('btnconsultarpagos');
            return true;
         })
    },
    ver:function(e)
    {   
        var i=e.target.id.indexOf('view-link-')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('view-link-','')        
        }else{
            id_model=e.target.parentNode.id.replace('view-link-','')        
        }
        
        if(id_model!="")
        {  var modelo=oColecciones.findWhere({'id_comp':id_model})
            this.mostrar(modelo)
        }
    },
    mostrar:function(modelo)
    {   //muestro los datos de los comprobantes adeudados
        var oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modelo.get('id_comp'),nombre:'id_comp',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        var str_talonario=format_number(modelo.get('nro_talonario'),'0000')
        var str_nro_comp=format_number(modelo.get('nro_comp'),'00000000')
        var comprobante=(modelo.get('afip')!=1)?('orden de pedido N°:'+modelo.get('id_comp')):(modelo.get('tipo_comp')+' N° '+str_talonario+' '+str_nro_comp+'  ('+modelo.get('id_comp')+')')
        cCampo=new Campo({valor:comprobante,nombre:'comprobante',tipo:'readonly',etiqueta:'Comprobante',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);
        var datoscliente=modelo.get('strnombrecompleto')+' '+modelo.get('documento')+' '+modelo.get('nro_docu')+' Cuit: '+modelo.get('cuit')
        cCampo=new Campo({valor:datoscliente,nombre:'datoscliente',tipo:'readonly',etiqueta:'Cliente',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('condicion'),nombre:'condicion',tipo:'text',etiqueta:'Condición',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('importe_total'),nombre:'importe_total',tipo:'money',etiqueta:'Importe total',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('importe_neto'),nombre:'importe_neto',tipo:'money',etiqueta:'Importe neto',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('importe_iva'),nombre:'importe_iva',tipo:'money',etiqueta:'Importe iva',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('importe_exento'),nombre:'importe_exento',tipo:'money',etiqueta:'Importe exento',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('importe_descuento'),nombre:'importe_descuento',tipo:'money',etiqueta:'Importe descuento',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('fe_creacion'),nombre:'fe_creacion',tipo:'text',etiqueta:'Fecha creación',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('usuario'),nombre:'usuario',tipo:'text',etiqueta:'Usuario',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('fe_comp'),nombre:'fe_comp',tipo:'text',etiqueta:'Fecha comprobante',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('cae'),nombre:'cae',tipo:'text',etiqueta:'C.A.E',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('fe_vencimiento'),nombre:'fe_vencimiento',tipo:'text',etiqueta:'Fecha vencimiento',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('descripcion'),nombre:'descripcion',tipo:'text',etiqueta:'Estado',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('usuario_estado'),nombre:'usuario_estado',tipo:'text',etiqueta:'Usuario estado',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        var afip=(modelo.get("afip")=="1")?"SI":"NO"
        cCampo=new Campo({valor:afip,nombre:'afip',tipo:'text',etiqueta:'Afip',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('sucursal'),nombre:'sucursal',tipo:'text',etiqueta:'Sucursal',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        if(modelo.get('estado')=='A'){
            cCampo=new Campo({valor:'ID COMP:'+ modelo.get('id_comp_anula'),nombre:'id_comp_anula',tipo:'text',etiqueta:'Anulado por ',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        }

        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        viendopago=false;
        var that=this;
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'V',base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
            ocampoView.options.volver=function(){ 
                /*es para atajar el evento de volver cuando se 
                        apreto el boton de volver de la vista pagos*/
                    if(viendopago) 
                        return
            
                        if (!$("#panel-body-comprobantes").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body-comprobantes").slideToggle();            
                        }
                        $("#row-body").hide();
                        $("#row-body").html("");
                        $("#row-pagos").hide();    
                        $("#row-pagos").html("");
                        ocampoView=null;
                        /*$("#row-body").hide();    
                        $("#row-body").html("");
                        $("#row-det").hide();    
                        
                        $("#row-pagos").hide();    
                        $("#row-pagos").html("");
                        if (!$("#panel-body-consultar-pagos").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body-consultar-pagos").slideToggle();            
                        }*/
           }
         
         ocampoView.options.onafterrender=function(){
            event.stopPropagation()            
            var mo=ocampoView.options.modelo
            var id_tipo_comp=mo.get("id_tipo_comp")
            var afip=mo.get("afip")
            var estado=mo.get("estado")
            var  arrTipocomp=["1","2","5"]//solo facturas A,B,C
            var  arrEstados=['D','E','F','P'] //solo estos estados se puede editar            
            
            if(mo.get('path_comp')!="null" && mo.get('path_comp')!=""){
                $("#botonera2").html("<button type='button' id='mostrarComprobante' class='btn btn-sm btn-info'><i class='fa fa-file-text'></i> Comprobante</button>"); 
            $("#mostrarComprobante").click(function(){                
                window.open('<?php echo base_url();?>'+mo.get('path_comp'),'_blank');
            })
            }
            
            }//afterrenderd
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.modelo=modelo
        }
         var targetLi = $("#panel-body-comprobantes").closest('li');
        if ($("#panel-body-comprobantes").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-comprobantes").slideToggle();            
        }        
        ocampoView.render('V')
        
    }
})//modelos view

var secciones={}
secciones['btnresumen']={}
secciones['btnresumen']['row']='';
secciones['btnresumen']['panel']='panel-body-saldos';
secciones['btncargapago']={}
secciones['btncargapago']['row']='row-pago-add';
secciones['btncargapago']['panel']='panel-body-cargar-pago';
secciones['btnconsultarpagos']={};
secciones['btnconsultarpagos']['row']='row-consulta-pagos';
secciones['btnconsultarpagos']['panel']='panel-body-consultar-pagos';
secciones['compadeudados']={};
secciones['compadeudados']['row']='row-det';
secciones['compadeudados']['panel']='panel-body-comprobantes';
secciones['compdet']={};
secciones['compdet']['row']='row-body';
secciones['compdet']['panel']='';
secciones['consultaspagos']={};
secciones['consultaspagos']['row']='row-pagos';
secciones['consultaspagos']['panel']='';
function display(seccion){
    
    switch (seccion){
        case 'btncargapago':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['compdet']['row']).hide();
        $("#"+secciones['consultaspagos']['row']).hide();
        $("#"+secciones['compadeudados']['row']).hide();
        break;
        case 'btnconsultarpagos':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['btncargapago']['row']).hide();
        $("#"+secciones['compdet']['row']).hide();
        $("#"+secciones['btncargapago']['row']).hide();
        $("#"+secciones['compadeudados']['row']).hide();
        break;
        case 'consultaspagos':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['btnconsultarpagos']['row']).show();
        $("#"+secciones['btncargapago']['row']).hide();
        $("#"+secciones['compdet']['row']).hide();
        $("#"+secciones['btncargapago']['row']).hide();
        $("#"+secciones['compadeudados']['row']).hide();
        break;
        case 'compadeudados':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['btnconsultarpagos']['row']).hide();
        $("#"+secciones['btncargapago']['row']).hide();
        $("#"+secciones['compdet']['row']).show();
        $("#"+secciones['consultaspagos']['row']).hide();        
        break;
        default:
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


function generarpreferenciaMP(id_pago){


$.ajax({dataType: "json",type: 'POST',url:base_url+'operaciones/pagos/pagarMP/',data: {id_pago:id_pago},
            beforeSend: function(){
             spinnerStart($("#panel-body-cargar-pago"))
            },
            success: function(jsonResponse)
            {
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror; 
                $("#formmp").html("")               
                if(numError == 0)
                {                    

                  //agrego datos al form para mostrar boton de mp;
                  var rand=Math.floor((Math.random() * 1000)+1);
                  var urlscript="https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js?v="+rand
                  var script= document.createElement('script');
                  formmp=document.getElementById("formmp"); 
                  formmp.appendChild(script)
                  $("#formmp script").attr("id","tagscript")
                  $("#formmp script").attr("data-public-key",jsonResponse.data['data-public-key'])
                  $("#formmp script").attr("data-preference-id",jsonResponse.data['data-preference-id'])
                  script.src=urlscript;

                 $(window).on("message", function(e) {

                    var origen=e.originalEvent.origin;
                    if(origen.indexOf("mercadopago")>=0){
                      e.preventDefault();                    
                      e.stopPropagation();
                    if(typeof e.originalEvent.data.type !="undefined"){
                        if(e.originalEvent.data.type=="submit"){                            
                            console.log("Pagó...")                           
                            $("#formmp").html("")
                           verificarAcreditacion(id_pago)
                             $(window).off("message");
                         return
                        }
                        if(e.originalEvent.data.type=="close"){
                             swal("error","El pago por mercadopago no se concretó.","error")
                            return
                        }
                    
                    }//origen                    
                    return;    
                    }
                  }); //oyente de mensajes windows
                }else
                {
                    //win.alert("Los cambios no se realizaron."+descError," Hubo un problema",1)
                    swal("error","Los cambios no se realizaron."+descError,"error")
                }
                
            },            
            complete: function(){
            spinnerEnd($("#panel-body-cargar-pago"))
            }
           })

}


function verificarAcreditacion(id_pago){

     console.log("verificamos acreditacion...")
          $.ajax({dataType: "json",type: 'POST',url:base_url+'operaciones/pagos/pagoverify/',data: {id_pago:id_pago},
            beforeSend: function(){
              spinnerStart($("#panel-body-cargar-pago"))
            },
            success: function(jsonResponse)
            {
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;                
                if(numError == 0)
                {
                  var data=jsonResponse.data
                  if(data.montoacreditado>0){
                    swal({
                           title: "EL pago fue exitoso",
                           text: "se han acreditado "+data.montoacreditado+" pesos",
                           type: "success",
                           showCancelButton: false,
                           confirmButtonColor: "rgb(71, 186, 193)",
                           confirmButtonText: "Aceptar",
                           closeOnConfirm: true
                        },
                        function () {
                        send_inputar()
                        });
                  }else{
                    swal({
                           title: "Atención",
                           text: "Por el momento no se ha acreditado el pago",
                           type: "info",
                           showCancelButton: false,
                           confirmButtonColor: "rgb(71, 186, 193)",
                           confirmButtonText: "Aceptar",
                           closeOnConfirm: true
                        })
                
                }
              }else
              {
                swal("error","Hubo problemas al verificar."+descError,"error")
              }
            },            
            complete: function(){
            spinnerEnd($("#panel-body-cargar-pago"))
            }
           }).fail( function( jqXHR, textStatus, errorThrown ) {
            console.log(textStatus)
            alert('Error!!');
            });

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->