<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content">     
    <input type="hidden" id="id_cliente" value="<?php echo $id_cliente;?>">
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">CONSULTAR COMPROBANTES DEL CLIENTE <?php echo $cliente['strnombrecompleto'] ?> - <?php echo $cliente['documento'] ?> <?php echo $cliente['nro_docu'] ?></h4>
            </div>             
            <div class="panel-body" id="panel-body">
                <form class="form-horizontal" action="/" method="POST" id='form-standard'>                   
                <div class="form-group">                    
                    <div class="col-md-1">
                        <button type='button' class='btn btn-warning btn btn-sm'  class="form-control" id="btn-volver">
                            <i class='fa fa-reply' ></i>
                            VOLVER</button>
                    </div>
                    <div class="col-md-1">
                        <label>ID comp
                            <input type="text" name="id_comp" id="id_comp" class="form-control" placeholder="" />
                        </label>
                    </div>
                    <div class="col-md-2">                            
                        <label>fecha desde
                            <input type="text" name="fecha_desde" id="fecha_desde" class="form-control" placeholder="ingrese fecha..." />
                        </label>
                    </div>
                    <div class="col-md-2">
                        <label>fecha hasta
                        <input type="text" name="fecha_hasta" id="fecha_hasta" class="form-control" placeholder="ingrese fecha..." />
                    </label>
                    </div>    
                    <div class="col-md-2">
                    <label>Incluir ordenes
                        <input type="checkbox" name="chkorden" id="chkorden" class="form-control" checked="checked" />
                    </label>
                    </div>                
                    <div class="col-md-2">
                    <label>Estado
                        <select class="form-control" id="estado">
                            <option value=''>TODOS</option>
                            <?php
                            foreach ($estados as  $estado) {
                                echo "<option value='".$estado['estado']."'>".$estado['descripcion']."</option> ";
                            }
                            ?>
                        </select>
                    </label>
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
    <div class="row" id="row-body">
    <!-- tabla --> 
    </div>
    <!-- begin row -->
    <div class="row" id="row-det">      
    </div>
    <!-- begin row -->
    <div class="row" id="row-pagos">      
    </div>
</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>COMPROBANTE</th>
                                <th>FECHA</th>                                
                                <th>IMPORTE</th>                                        
                                <th>ESTADO</th>
                                <th>PAGOS</th>                                        
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
        <td><a href="javascript:;" class="btn btn-white btn-xs" id="view-link-<%=elemento.get('id_comp')%>"><i class="fa fa-eye"></i></a></td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<script type="text/template" id="tpl-details-list"> 
<%
var str_talonario=format_number(comprobante.get('nro_talonario'),'0000')
var str_nro_comp=format_number(comprobante.get('nro_comp'),'00000000')
var strcomprobante=(comprobante.get('afip')!=1)?('orden de pedido N°:'+comprobante.get('id_comp')):(comprobante.get('tipo_comp')+' N° '+str_talonario+' '+str_nro_comp+'  ('+comprobante.get('id_comp')+')') 
%>     
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                       
                </div>
                <h4 class="panel-title">DETALLES DEL COMPROBANTE <%=strcomprobante%></h4>
    </div>             
    <div class="panel-body" id="panel-body-det">                
        <table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>producto</th>
                                <th>$ base x unidad</th>                                
                                <th>$ iva x unidad</th>                                        
                                <th>$ venta x unidad</th>                                        
                                <th>unidades</th>
                                <th>$ iva</th>
                                <th>$ neto</th>
                                <th>descuento</th>
                                <th>total</th>
                            </tr>
                        </thead>
                        <tbody>                                                               
                        <% _.each(ls, function(elemento) { 
                         %>
                        <tr>        
                            <td><%=elemento.get('nro_item')%> - <%=elemento.get('producto')%>: <%=elemento.get('detalle')%></td>
                            <td>$ <%=elemento.get('precio_base')%></td>
                            <td>$ <%=elemento.get('precio_iva')%></td>
                            <td>$ <%=elemento.get('precio_venta')%></td>
                            <td><%=elemento.get('cantidad')%></td>        
                            <td>$ <%=elemento.get('importe_item_iva')%></td>        
                            <td>$ <%=elemento.get('importe_item_neto')%></td>        
                            <td>$ <%=elemento.get('importe_descuento')%></td>        
                            <td>$ <%=elemento.get('importe_item')%></td>                                    
                        </tr>
                        <% }); %>
                        </tbody>
        </table>
    </div>
   </div>
</div>
</script>
<script type="text/template" id="tpl-pagos-list">
<%
var str_talonario=''
if(comprobante.get('nro_talonario')!=null){
    str_talonario=format_number(comprobante.get('nro_talonario'),'0000')
}

var str_nro_comp=''
if(comprobante.get('nro_comp')!=null){
    str_nro_comp=format_number(comprobante.get('nro_comp'),'00000000')
}

var strcomprobante=''

if(comprobante.get('afip') != 1){
strcomprobante= 'orden de pedido N°:' + comprobante.get('id_comp')
}else{
strcomprobante=comprobante.get('tipo_comp')+' N° ' + str_talonario + ' ' + str_nro_comp + '  (' + comprobante.get('id_comp') + ')'
}
%>     
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                       
                </div>
                <h4 class="panel-title">PAGOS IMPUTADOS AL COMPROBANTE <%=strcomprobante%></h4>
    </div>             
    <div class="panel-body" id="panel-body-pagos">                
        <table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>pago</th>
                                <th>tipo pago</th>                                                         
                                <th>usuario</th>                                        
                                <th>sucursal</th>                                        
                                <th> - </th>
                                
                            </tr>
                        </thead>
                        <tbody>                                                               
                        <% _.each(ls, function(elemento) { 
                        var strtipo_pago=''
                        var strpago_estado=''
                            if(elemento.get('estado')!='C'){
                                strpago_estado=' - <a href="javascript:;" class="btn btn-danger btn-xs">' + elemento.get('monto') + '</a>'
                            }
                        var params=elemento.get("parametros");
                            if(typeof params !="undefined")
                            {
                                if(params.length>0)
                                {
        strtipo_pago="<a href='javascript:;' id='id_tipo_pago_" + elemento.get('id_pago') + "-" + elemento.get('id_tipo_pago') + "'>" + elemento.get('tipo_pago') + "</a>"
                                }else{
                                        strtipo_pago=elemento.get("tipo_pago")
                                }
                            }
                         %>
                        <tr>        
                            <td>(<%=elemento.get('id_comp_pago')%>) - $ <%=elemento.get('monto')%> (<%=elemento.get('fe_pago')%>) <%=strpago_estado%></td>
                            <td><%=strtipo_pago%></td>
                            <td><%=elemento.get('usuario')%></td>
                            <td><%=elemento.get('sucursal')%></td>
                            <td>
                        <a href="javascript:;" class="btn btn-white btn-xs" id="pago-link-<%=elemento.get('id_pago')%>"><i class="fa fa-search-plus"></i></a>
                            </td>
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
App.setPageTitle('Consulta de comprobantes | Coffee APP');
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
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/comprobantes/<?php echo $id_cliente;?>");
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
var oPagos=null;
var tipospagos=null;
var olista=null;
var olsDetalles=null;
var olsPagos=null;
var ocampoView=null;
var eventos = _.extend({}, Backbone.Events);
var tipopagos=null;
var Detalle=Backbone.Model.extend({});
var Detalles=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}
        this.productos=globales['tipo_items'];             
    },
    loadAsync:function(mComp){
        var that=this;
        that.reset();                
        var cond="1=1"            
        
        cond+=" and id_comp="+mComp.get("id_comp");
        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_det",this);
        }        
        var cmp=Array("id_comp","nro_item","id_tipo","nro_tipo","precio_base","precio_iva","iva","precio_venta","importe_item","importe_item_iva","importe_item_neto","detalle","cantidad","importe_descuento");
        ofwlocal.getAsync("vercomprobantes_det",cmp,cond,"nro_item asc",function(rs){             
            that.cargar(rs,that) 
        })
    },    
    cargar:function(rs,that)
    {       
     for (c in rs)
     {
        var producto=this.productos.findWhere({id:rs[c]['id_tipo']});
        rs[c]['producto']=producto.get('descripcion');
      that.add(rs[c])
     }
       if(typeof that.options.eventos !="undefined")
        {
        that.options.eventos.trigger("endload_det",that);
        }
    },
    model:Detalle
});

var DetallesView=Backbone.View.extend(
{   el:$('#row-det'),    
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(comprobante)
    {   
        this.comprobante=comprobante
        var that=this;
        if(oDetalles ==null)
        {
        oDetalles=new Detalles({eventos:eventos});    
        }
        eventos.on("initload_det",this.loading,this)
        eventos.on("endload_det",this.endload,this)                
        oDetalles.loadAsync(comprobante)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-det'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },
    events:{},
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-details-list').html()); 
                       
        this.$el.html(tpl({ls:olist,comprobante:this.comprobante}));         
         spinnerEnd($('#panel-body-det'));
    }
})//detalles view

var Pago=Backbone.Model.extend({});
var Pagos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
        this.tipospagos=tipospagos
    },
    loadAsync:function(mComp){
        var that=this;
        that.reset();                
        var cond="1=1 and incide_caja=1"            
        
        cond+=" and id_comp="+mComp.get("id_comp");
        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_pagos",this);
        }        
        var cmp=Array("id_comp_pago","id_comp","monto","id_pago","pago","DATE_FORMAT(fe_pago,'%d/%m/%Y %r') as fe_pago","id_tipo_pago","tipo_pago","id_usuario","usuario","id_sucursal","sucursal","id_cliente","strnombrecompleto","estado","DATE_FORMAT(fe_estado_pago,'%d/%m/%Y %r') as fe_estado_pago,btnclass_pago,estado_pago_desc");
        ofwlocal.getAsync("vercomp_pagos",cmp,cond,"",function(rs){             
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

var PagosView=Backbone.View.extend(
{   el:$('#row-pagos'),    
    initialize:function(options)
    {
        this.options=options || {};
        this.onview=false;
    },
    render:function(comprobante)
    {   
        this.comprobante=comprobante
        var that=this;
        if(oPagos ==null)
        {
        oPagos=new Pagos({eventos:eventos});    
        }
        eventos.on("initload_pagos",this.loading,this)
        eventos.on("endload_pagos",this.endload,this)                
        oPagos.loadAsync(comprobante)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-pagos'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },

    events:{"click a[id*='pago-link-']":'ver',
    "click a[id*='id_tipo_pago_']":'verparams'},
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-pagos-list').html());                
        this.$el.html(tpl({ls:olist,comprobante:this.comprobante}));         
         spinnerEnd($('#panel-body-pagos'));
    },
    ver:function(e){        
        event.stopPropagation();
        if(this.onview){
            return;
        }
        this.onview=true;
        var i=e.target.id.indexOf('pago-link-')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('pago-link-','')        
        }else{
            id_model=e.target.parentNode.id.replace('pago-link-','')        
        }
        var cmp=Array("id_pago","pago","DATE_FORMAT(fe_pago,'%d/%m/%Y %r') as fe_pago","id_cliente","strnombrecompleto","observacion","id_tipo_pago","tipo_pago","id_usuario","usuario","id_caja_op","DATE_FORMAT(fe_inicio,'%d/%m/%Y %r') as fe_inicio","DATE_FORMAT(fe_fin,'%d/%m/%Y %r') as fe_fin","caja","id_sucursal","sucursal","estado_pago","DATE_FORMAT(fe_estado_pago,'%d/%m/%Y %r') as fe_estado_pago","usuario_estado_pago","estado_pago_desc","btnclass_pago")
        var rs=ofwlocal.get("vercomp_pagos",cmp,"id_pago="+id_model,"");
        if(rs.length>0)
        {
        var titulo= " DATOS DEL PAGO N° "+rs[0]['id_pago']
        cuerpo="<ul>"
        if(rs[0]['estado_pago']!="C"){
        cuerpo+="<li>"+rs[0]['estado_pago_desc']+"  "+rs[0]['pago']+" ("+rs[0]['usuario_estado_pago']+" - "+rs[0]['fe_estado_pago']+")</li>"
        }
        cuerpo+="<li>$ "+rs[0]['pago']+" ("+rs[0]['fe_pago']+")</li>"
        cuerpo+="<li>Tipo pago: "+rs[0]['tipo_pago']+"</li>"        
        cuerpo+="<li>Obs: "+rs[0]['observacion']+"</li>"
        cuerpo+="<li>Usuario: "+rs[0]['usuario']+"</li>"
        cuerpo+="<li>Caja N° "+rs[0]['id_caja_op']+" inicio "+rs[0]['fe_inicio']+" fin "+rs[0]['fe_fin']+"</li>"
        cuerpo+="<li>Sucursal: "+rs[0]['sucursal']+"</li>"
        cuerpo+="</ul>"
        win.alert(cuerpo,titulo,3)    

        }
    this.onview=false;
    },//ver
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

})//detalles view




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
        var cond="id_cliente="+id_cliente
        var id_comp=0;
        //priorizo la busqueda por ID
        if(patrones['id_comp']!="")
        {   if(es_numero(patrones['id_comp']))
            {
            id_comp=parseInt(patrones['id_comp'])
            cond+=" and id_comp="+patrones['id_comp'];    
            }
        }

        if(patrones['fecha_desde']!="" && !(id_comp>0))
        {var strdate=todate(patrones['fecha_desde'],103);
            cond+=' and fe_creacion>="'+strdate+'"';
        }
        if(patrones['fecha_hasta']!="" && !(id_comp>0))
        {
            var strdate=todate(patrones['fecha_hasta'],103);
            cond+=" and fe_creacion< ADDDATE('"+strdate+"',1)";
        }
        if(patrones['estado']!="" && !(id_comp>0))
        {
            
            cond+=" and estado='"+patrones['estado']+"'";
        }
        if(patrones['afip']==1 && !(id_comp>0))
        {
            
            cond+=" and afip=1";
        }

        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_comp",this);
        }        
        var cmp=Array("id_comp","id_talonario","IFNULL(nro_comp,0) as nro_comp","IFNULL(nro_talonario,0) as nro_talonario","cae","id_cliente","strnombrecompleto","domicilio","telefono","nro_docu","tipo_docu","documento","tipo_persona_desc","descripcion_loc","descripcion_pro","importe_total","importe_neto","importe_base","importe_iva","importe_exento","importe_descuento","DATE_FORMAT(fe_creacion,'%d/%m/%Y %r') as fe_creacion","DATE_FORMAT(fe_comp,'%d/%m/%Y %r') as fe_comp","DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento","cuit","estado","descripcion","estadoclass","usuario_estado","usuario","afip","id_sucursal","sucursal","id_cond_iva","condicion","id_tipo_comp","tipo_comp","tipo_comp_afip","importe_pago","path_comp","id_comp_anula");
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
var ElementosView=Backbone.View.extend(
{   el:$('#panel-body'),
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(patrones)
    {
        var that=this;
        
        
        
        
        eventos.on("initload_comp",this.loading,this)
        eventos.on("endload_comp",this.endload,this)
        oColecciones.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body'));  
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
         spinnerEnd($('#panel-body'));
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
    {
        oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:parseInt(modelo.get('id_comp')),nombre:'id_comp',tipo:'hidden',identificador:true});
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

        /*if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }*/
        
        var that=this;
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'V',base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
            ocampoView.options.volver=function(){                
                        if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body").slideToggle();            
                        }
                        $("#row-body").hide();    
                        $("#row-body").html("");
                        $("#row-det").hide();    
                        $("#row-det").html("");
                        $("#row-pagos").hide();    
                        $("#row-pagos").html("");
                        ocampoView=null;
           }
         
         ocampoView.options.onafterrender=function(){
            event.stopPropagation() 
                                   
            var mo=ocampoView.options.modelo
            var id_tipo_comp=parseInt((mo.get("id_tipo_comp")!=null)?mo.get("id_tipo_comp"):0)
            var afip=parseInt(mo.get("afip"))
            var estado=mo.get("estado")
            var  arrTipocomp=[1,2,5]//solo facturas A,B,C
            var  arrEstados=['D','E','F','P'] //solo estos estados se puede editar
            var bandera1=(afip==1 && arrTipocomp.indexOf(id_tipo_comp)>=0 && arrEstados.indexOf(estado)>=0);

            var bandera2=(afip!=1 && arrEstados.indexOf(estado)>=0);
            if(bandera1 || bandera2){
                $("#botonera3").html("<button type='button' id='editarComprobante' class='btn btn-sm btn-info'><i class='fa fa-edit'></i> Editar</button>"); 
            $("#editarComprobante").click(function(){                
                var id_comp=ocampoView.options.modelo.get("id_comp");
                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/editar/"+id_comp);
            })
            }
            
            if(mo.get('path_comp')!=null && mo.get('path_comp')!=""){
                $("#botonera2").html("<button type='button' id='mostrarComprobante' class='btn btn-sm btn-info'><i class='fa fa-file-text'></i> Comprobante</button>"); 
            $("#mostrarComprobante").click(function(){                
                window.open('<?php echo base_url();?>'+mo.get('path_comp'),'_blank');
            })
            }
            that.mostrardetalles(mo);
            }//afterrenderd
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.modelo=modelo
        }

        
 
         var targetLi = $("#panel-body").closest('li');
        if ($("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body").slideToggle();            
        }        
        ocampoView.render('V')
        
    },//mostrar
    mostrardetalles:function(oComp){
        $("#row-det").show();    
        $("#row-det").html("");
        $("#row-pagos").show();    
        $("#row-pagos").html("");
        olsDetalles=new DetallesView({el:$('#row-det')});                
        olsDetalles.render(oComp)
        olsPagos=new PagosView({el:$('#row-pagos')});                
        olsPagos.render(oComp)
    }

})//modelos view





function inicializacion_contexto()
{
var hoy=new Date();

//$("#fecha_desde").val(datetostr(hoy))
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

$("#id_comp").keypress(function(e){
    if(e.which==13)
    {
    consultar();
    }
    return teclaentero(e)
})
$("#fecha_desde").keypress(function(e){
    if(e.which==13)
    {
     consultar();   
    }
    
})
$("#fecha_hasta").keypress(function(e){
    if(e.which==13)
    {
     consultar();   
    }
    
})
tipospagos=new Tipopagos();
olista=new ElementosView({el:$('#table-wrapper')});


$("#btn-volver").click(function(e){    
    event.stopPropagation();
     handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/")
})

$("#btn-consultar").click(function(e){    
    event.stopPropagation();
     consultar();
})
oColecciones=new Coleccion({eventos:eventos});    
consultar()

}//inicializacion contexto

function consultar()
{
    
var patrones={fecha_desde:$("#fecha_desde").val(),fecha_hasta:$("#fecha_hasta").val(), afip:$("#chkorden").is(":checked")?0:1,estado:$("#estado").val(),id_comp:$("#id_comp").val()}
 olista.render(patrones);
}
</script>
<!-- ================== END PAGE LEVEL JS ================== -->