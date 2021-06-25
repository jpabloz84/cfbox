<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>TALONARIO</th>
                                <th>SUCURSAL</th>
                                <th>HABILITADO</th>                                        
                                <th>CERTIFICADO</th>
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) { 
     
    var tipo_comp=comprobantes.findWhere({id:elemento.get('id_tipo_comp')})
    var sucursal=sucursales.findWhere({id:elemento.get('id_sucursal')})
    var cert=(elemento.get('strnombre')!=null)?elemento.get('strnombre'):"";
    var nombrecert="(ID "+elemento.get("id_certificado")+")"
    if(nombrecert!=""){
    nombrecert+=" "+cert.split("[serialNumber]")[0]+"...";
    }
    %>
    <tr >        
        <td><%=format_number(elemento.get('nro_talonario'),'0000')%> - <%=tipo_comp.get('descripcion') %> (ID <%=elemento.get('id_talonario')%>)</td>
        <td><%=sucursal.get('descripcion') %></td>
        <td><% if(elemento.get('habilitado')==1){%><span class="label label-success">SI</span><%}else {%><span class="label label-danger">NO</span><% } %></td>
        <td><% if(elemento.get('testing')==1){%><span class="label label-warning"><%=nombrecert%> (en modo test!)</span><%}else {%><span class="label label-success"><%=nombrecert%></span><% } %></td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_talonario')%>">VER</button>            
        </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel1">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">CONSULTA DE TALONARIOS</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-6">
                                     <label>TALONARIO
                                    <input type="text" class="form-control" id="patronnro" placeholder="Numero"  style="text-align:right"/>
                                    </label>
                                 </div>
                                 <div class="col-md-6" style="text-align:center">                                    
                                     <label>HABILITADOS
                                    <input type="checkbox" class="form-control" id="patronhabilitado" />
                                    </label>
                                 </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success" id="btnRealizarAlta" onclick="realizaralta()">Crear talonario</button>
                                </div>
                                <div class="col-md-6">                            
                                </div>
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-primary m-r-5" id="bntBuscar" onclick="buscar(event)">Buscar...</button>
                                </div>                            
                            </div>                            
                            <div class="form-group" id="tpl-table-query">
                            </div>                            
                            </form>
                            </div>
           </div>
        </div>
        <!-- end col-12 -->
    <!-- tabla --> 
    </div>
    <!-- begin row -->
    <div class="row" id="row-body">
    <!-- tabla --> 
    </div>

</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];

App.restartGlobalFunction();
App.setPageTitle('Consulta de talonarios | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;

var ocampoView=null;    
var oColecciones=null;
var oCampos=null;
var olista=null;
var oComprobantes=null;
var oSucusales=null;
var oCertificados=null;

$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        //TableManageResponsive.init();
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/talonarios/");
                         })
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);

var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/talonarios/listener',   
    idAttribute:'id_talonario',
    defaults:{
        id_talonario:0,
        nro_talonario:0,
        ultimo_nro_comp:0,
        habilitado:true,
        id_tipo_comp:0,
        id_sucursal:1,
        id_certificado:0
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();                        
        var cond="1=1 "        
        if(patrones['nro_talonario']>0)
        {
            cond+=' and nro_talonario='+patrones['nro_talonario'];
        }
        if(patrones['habilitado']!=0)
        {
            cond+=' and habilitado='+patrones['habilitado'];
        }
        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }        
        var cols= Array('*')
        ofwlocal.getAsync("vertalonarios",cols,cond,"nro_talonario asc,habilitado desc",function(rs){ that.cargar(rs,that) } )
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
    model:Elemento
});


var ElementosView=Backbone.View.extend(
{   el:$('#panel-body'),
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
        eventos.on("initload",this.loading,this);
        eventos.on("endload",this.endload,this);
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    
        }
        
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
            "click button[name='ver']":'ver'
    },
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist,sucursales:oSucusales,comprobantes:oComprobantes}));
         $('#data-table').DataTable({responsive: true,searching:false,lengthChange:false,pageLength:10,
            "columns": [
            { "orderable": true },
            { "orderable": true },
            { "orderable": true},            
            { "orderable": false},            
            { "orderable": false}
            ]
         }); 
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {
        var id_model=(e.target.id).replace("view-","");
        var res=oColecciones.where({"id_talonario":id_model})
        var modelo=null;
        if(res.length==0)
        {
            return        
        }
        modelo=res[0]
        this.mostrar(modelo,'V')    
    },
    mostrar:function(modelo,modo)
    {
        oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modelo.get('id_talonario'),nombre:'id_talonario',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('nro_talonario'),nombre:'nro_talonario',tipo:'int',etiqueta:'Numero de talonario',esdescriptivo:true,obligatorio:true});
        oCampos.add(cCampo);     
        cCampo=new Campo({valor:modelo.get('habilitado'),nombre:'habilitado',tipo:'checkbox',etiqueta:'Habilitado para operar'});
        oCampos.add(cCampo); 
        
        cCampo=new Campo({valor:modelo.get('id_tipo_comp'),nombre:'id_tipo_comp',tipo:'select',etiqueta:'Tipo de comprobante',coleccion:oComprobantes,obligatorio:true});
        oCampos.add(cCampo); 

        
        cCampo=new Campo({valor:modelo.get('id_sucursal'),nombre:'id_sucursal',tipo:'select',etiqueta:'Sucursal',coleccion:oSucusales,obligatorio:true});
        oCampos.add(cCampo); 

        
        cCampo=new Campo({valor:modelo.get('id_certificado'),nombre:'id_certificado',tipo:'select',etiqueta:'Certificado',coleccion:oCertificados,obligatorio:true});
        oCampos.add(cCampo); 

        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'EV',base_url:'<?=base_url()?>',el:$("#row-body")});
                      
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.modelo=modelo
        }

        ocampoView.options.volver=function(){                
                        if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body").slideToggle();            
                        }
                        $("#row-body").hide();    
                        $("#row-body").html("");
       }
 
         var targetLi = $("#panel-body").closest('li');
        if ($("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body").slideToggle();            
        }
        ocampoView.render(modo)
        if(modo=='A' || modo=='E')
        {
            ocampoView.options.validar=function(e)
            { var strhtml=''
                    if(e['nro_talonario']=="" || !e['nro_talonario']>0)
                    {
                        strhtml+="<li>Debe ingresar un numero talonario</li>"
                    }
                    

                    if(e['id_tipo_comp']=="" || !e['id_tipo_comp']>0)
                    {
                        strhtml+="<li>Debe seleccionar un tipo de comprobante</li>"
                    }

                    if(e['id_sucursal']=="" || !e['id_sucursal']>0)
                    {
                        strhtml+="<li>Debe seleccionar una sucursal</li>"
                    }

                    if(e['id_certificado']=="" || !e['id_certificado']>0)
                    {
                        strhtml+="<li>Debe seleccionar un Certificado a usar</li>"
                    }
                    if(e['nro_talonario']>0 && e['id_tipo_comp']>0 && e['modo']=='A')
                    {
                        var rs=ofwlocal.get('talonarios',Array('id_talonario'),'nro_talonario='+e['nro_talonario']+' and id_tipo_comp='+e['id_tipo_comp']+' and id_sucursal='+e['id_sucursal'],'');
                        if(rs.length>0)
                        {   
                             strhtml+="<li>Parece que el tipo de talonario ya existe para esta sucursal: ID '"+rs[0].id_talonario+"'</li>"
                        }
                
                    }
                    
                    if(strhtml!="")
                    {
                        win.alert("<ul>"+strhtml+"</ul>","Advertencia: Debe corregir los siguientes datos para continuar",3)
                        return false;
                    }else{
                        return true;
                    }
            }
         }//solo para altas y edicion
         
        ocampoView.options.error=function()
        {
            spinnerEnd($('#panel-body-view'));
            if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                    $("#panel-body").slideToggle();            
                    }
            $("#row-body").hide();    
            $("#row-body").html("");
            var patrones={nro_talonario:$("#patronnro").val(),habilitado:($("#patronhabilitado").is(":checked"))?1:0}    
            olista.render(patrones);
        }
        ocampoView.options.success=function()
        {
            spinnerEnd($('#panel-body-view'));  
            if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                    $("#panel-body").slideToggle();            
                    }
            $("#row-body").hide();    
            $("#row-body").html("");
            var patrones={nro_talonario:$("#patronnro").val(),habilitado:($("#patronhabilitado").is(":checked"))?1:0}
            olista.render(patrones);
        }
        ocampoView.options.before=function()
        {
            spinnerStart($('#panel-body-view'));
        }//antes
         
}
    


});//ElementosView
function inicializacion_contexto()
{
oComprobantes=new Tipocomprobantes();
oSucusales=new Sucursales();
oCertificados=new Certificados();    
olista=new ElementosView({el:$('#tpl-table-query')});    
}//inicializacion contexto

function buscar(evt)
{
evt.preventDefault();
var patrones={nro_talonario:$("#patronnro").val(),habilitado:($("#patronhabilitado").is(":checked"))?1:0}
olista.render(patrones);
}

function realizaralta()
{   
    var newModel=new Elemento()
    olista.mostrar(newModel,'A')

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->