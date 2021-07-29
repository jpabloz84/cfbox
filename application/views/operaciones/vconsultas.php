<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$modo=(isset($modo))?$modo:"";
$origen=(isset($origen))?$origen:"";
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" class="display compact hover table table-striped nowrap" width="100%" style="color:#707478">
                        <thead>
                            <tr>
                                <th>NOMBRES</th>                                        
                                <th>CUIT/CUIL</th>
                                <th>DOCUMENTO</th>                                        
                                <th></th>                                        
                                <th></th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) { 
        var cf=(elemento.get("cf"))?elemento.get("cf"):""
     %>
    <tr>        
        <td><%=elemento.get('strnombrecompleto') %> (<%=elemento.get('id_cliente') %>)</td>        
        <td><%=elemento.get('cuit') %></td>        
        <td><%=elemento.get('documento') %> - <%=elemento.get('nro_docu') %></td>
        <%
            if(cf=="1" && modo=="NP"){%>
            <td></td>
            <td style='text-align:right' >
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary" idcliente="<%=elemento.get('id_cliente')%>" id="nuevo-pedido-cf">
                Realizar pedido&nbsp;<i class="fa fa-paperclip"></i></button>
            </div>
            </td>
            <%}else{%>
            <td style='text-align:right'>
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary" name='comprobantes' id="comp-<%=elemento.get('id_cliente')%>">
                comprobantes&nbsp;<i class="fa fa-paperclip"></i></button>
            </div>
            </td>
            <td style='text-align:right'>
                <div class="btn-group">
                <button type="button" class="btn btn-xs btn-primary" name='seleccionar' id="seleccionar-<%=elemento.get('id_cliente')%>">
                    cuenta&nbsp;<i class="fa fa-arrow-right"></i></button></div>
            </td>
            <%}
        %>
        
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<input type="hidden" id="modo" value="<?=$modo?>">
<input type="hidden" id="origen" value="<?=$origen?>">
<input type="hidden" name="base_url" id="base_url" value="<?=base_url()?>">
<input type="hidden" name="id_empresa" id="id_empresa" value="<?=$visitante->get_id_empresa()?>">
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel1">  
          <div class="panel panel-success">
           <div class="panel-heading">
            <div class="panel-heading-btn">
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            </div>
                <h4 class="panel-title">1) BUSQUE Y ELIJA UN CLIENTE</h4>
           </div>             
                <div class="panel-body bg-green text-white" id="panel-body-inputs">
                    <form>
                        
                          <div class="form-row">
                            <div class="form-group col-md-4">
                                 <label for="patronnombres">Apellido y/o nombres</label>                            
                                 <input type="text" class="form-control" id="patronnombres" placeholder="ingrese nombre" />
                            </div>
                             <div class="form-group col-md-4">
                                 <label for="patronemail">Email</label>                            
                                 <input type="text" class="form-control" id="patronemail" placeholder="Email" />
                            </div>
                           <!-- <div class="form-group col-md-2">
                                <label for="patron_cuit">Cuit</label> 
                                <input type="text" class="form-control" id="patron_cuit" placeholder="Ingrese numero" />
                            </div>-->
                            <div class="form-group col-md-2">
                                <label for="patron_nro_docu">N° documento</label>
                                <input type="text" class="form-control" id="patron_nro_docu" placeholder="ingrese numero" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <button type="button" class="btn btn-primary btn-block" onclick="return buscar(event)">
                                            <i class="fa fa-search"></i>
                                        BUSCAR</button>
                            </div>                        
                        </div>
                        
                    </form>
                </div>
           </div>
        </div>
        <!-- end col-12 -->
    <!-- tabla --> 
    </div>
    <!-- begin row -->
    
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel2">  
          <div class="panel panel-success">
           <div class="panel-heading">
            <div class="panel-heading-btn">                 
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            </div>
                <h4 class="panel-title">2) SELECCIONE UNA ACCIÓN</h4>
           </div>             
                <div class="panel-body bg-green text-white"  id="panel-body-clientes">
                 
                </div>
           </div>
        </div>
        <!-- end col-12 -->
    <!-- tabla --> 
    </div>

</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];

App.restartGlobalFunction();
App.setPageTitle('Operar | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;

var ocampoView=null;    
var oClientes=null;
var oCampos=null;
var oClientesView=null;
var oCategorias=null;
var oElementView=null;



$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/<?=$origen;?>");
                            
                         });
                    });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);
var Cliente=Backbone.Model.extend({idAttribute:'id_cliente'});//elementomodel

var Clientes=Backbone.Collection.extend({         
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        
        var that=this;
        that.reset();        

        var cond=" habilitado=1 and id_empresa="+$("#id_empresa").val()
        if(typeof patrones['id_cliente']!="undefined" )
        {   if(+(patrones['id_cliente'])>0)
            {
            cond+=' and id_cliente='+patrones['id_cliente'];
            }
        }

        if(typeof patrones['cf']!="undefined" )
        {   if(parseInt( patrones['cf'])==1)
            {
            cond+=' and cf=1';
            }
        }

        if(patrones['email']!="")
        {
            cond+=' and email like "%'+patrones['email']+'%"'
        }
        if(patrones['apenom']!="")
        {
            cond+=' and strnombrecompleto like "'+patrones['apenom']+'%"'
        }
        if(patrones['nro_docu']!="")
        {
            cond+=' and nro_docu='+patrones['nro_docu']
        }
        if(typeof this.options.eventos != "undefined")
        {
            this.options.eventos.trigger("initload",that);
        }


        ofwlocal.getAsync("verclientes",Array("id_cliente,condicion,strnombrecompleto,tipo_persona,domicilio,telefono,cuit,nro_docu,documento,sexo,img_personal,email,observaciones,descripcion_loc,descripcion_pro,descripcion_loc_nac,DATE_FORMAT(fe_nacimiento,'%d/%m/%Y') as fe_nacimiento,descripcion_pro_nac,cf"),cond,"apellido asc,nombres asc",function(rs){ that.cargar(rs,that) } )
    },    
    cargar:function(rs,that)
    {       
         for (c in rs)
         {
          that.add(rs[c])
          }
        
        if(typeof that.options.eventos !="undefined")
        {         
         that.options.eventos.trigger("endload",that);
        }
    },
    model:Cliente
});


var ClientesView=Backbone.View.extend({   
    initialize:function(options)
    {
        this.options=options || {};
        this.rendercount=0;
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
    },
    render:function(patrones)
    {
        var that=this;
        
        event.stopPropagation()
        if(oClientes ==null)
        {
        oClientes=new Clientes({eventos:eventos});    
        }
                
        oClientes.loadAsync(patrones)


        return this;
        
    },//render    
    loading:function(ocol)
    {         
      spinnerStart($('#panel-body-clientes'));  
    },
    endload:function(ocol)
    { event.stopPropagation();
      this.cargar(ocol)
      
    },
    events:{
            "click button[name='seleccionar']":'cuentacorriente',
            "click button[name='comprobantes']":'comprobantes',
            "click button[id='nuevo-pedido-cf']":'realizar_pedido',
    },
    cargar:function(oClientes)
    {   
        
        olist=oClientes.models 
        //si no es la primera vez que dibuja, que lo cierre, siempre y cuando tenga registros
        if(olist.length>0 && this.rendercount>0) 
        { 
            if ($("#panel-body-inputs").is(":visible")) { //si esta abierto, que lo cierre
                    $("#panel-body-inputs").slideToggle();            
            }

        }
        this.rendercount++;
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist,modo:$("#modo").val()}));
         $('#data-table').DataTable({responsive: true,searching:false,lengthChange:false,pageLength:10,
            "columns": [
            { "orderable": true },
            { "orderable": true },
            { "orderable": true},
            { "orderable": false},
            { "orderable": false}
            ]
         }); 
         spinnerEnd($('#panel-body-clientes'));
    },
    cuentacorriente:function(e)
    {    
        var id_button=(e.target.id.indexOf("seleccionar-")>=0)?e.target.id: e.target.parentNode.id
        if(permitir(prt_clientes,16))
         {
            var id_model=id_button.replace("seleccionar-","");
            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/cuentacc/"+id_model);
         }else{
            swal("Error","No tiene permiso para realizar esta acción","error")   
         }

    },
    comprobantes:function(e)
    {
        var id_button=(e.target.id.indexOf("comp-")>=0)?e.target.id:e.target.parentNode.id
        if(permitir(prt_comprobantes,1))
         {
            var id_model=id_button.replace("comp-","");
            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/comprobantes/"+id_model);
         }else{
            swal("Error","No tiene permiso para realizar esta acción","error")
         }

    },
    realizar_pedido:function(e)
    {   var id_cliente=$("#nuevo-pedido-cf").attr("idcliente")
        if(permitir(prt_operaciones,2))
         {
            
            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/nuevopedido/"+id_cliente);
         }else{
             swal("Error","No tiene permiso para realizar esta acción","error")
         }
    }
});//ElementosView
function inicializacion_contexto()
{

window.localStorage.setItem("origin",$("#base_url").val()+"index.php/"+$("#origen").val())
    /*elimino todos los eventos porque al volver, se  multiplican los eventos*/
eventos.off(); 
oClientesView=new ClientesView({el:$('#panel-body-clientes')}); 
 $("#patron_cuit").keypress(function(e){
    if ( e.which == 13 ) {
        buscar(e);
        return false;
    }else{
        return teclaentero(e)
    }
    });     
 $("#patron_nro_docu").keypress(function(e){
    if ( e.which == 13 ) {
        buscar(e);
        return false;
    }else{
        return teclaentero(e)
    }
    }); 
 $("#patronnombres").keypress(function(e){
    if ( e.which == 13 ) {
        buscar(e);
        return false;
    }else{
        return true
    }
    }); 
 var id_cliente_preseleccionado=""
 if(typeof window.localStorage.getItem("id_cliente_selected")!="undefined" && window.localStorage.getItem("id_cliente_selected")!=null){
    if(window.localStorage.getItem("id_cliente_selected")!=""){
        id_cliente_preseleccionado=window.localStorage.getItem("id_cliente_selected")
    }
 }


var modo=$("#modo").val()
if(id_cliente_preseleccionado!="" && modo!="NP"){
oClientesView.render({id_cliente:id_cliente_preseleccionado,nro_docu:'',apenom:'',email:''});    
}

//nuevo pedido
if(modo=="NP"){
oClientesView.render({cf:1,nro_docu:'',apenom:'',email:''});    
}

else{
oClientesView.render({cf:1,nro_docu:'',apenom:'',email:''});  
}
  
}//inicializacion contexto

function buscar(evt)
{
    
event.stopPropagation()
window.localStorage.setItem("id_cliente_selected","")
var modo=$("#modo").val()
var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),email:$("#patronemail").val()}


oClientesView.render(patrones);

}

function realizaralta()
{   
     if(permitir(prt_clientes,2))
     {
        handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/consultas/");    
     }else {
        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
     }
   
}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->