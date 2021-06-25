<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>CERTIFICADO</th>                                
                                <th>VENCIMIENTO</th>
                                <th>TESTING</th>
                                <th>HABILITADO</th>                                        
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {  %>
    <tr class="gradeA">        
        <td><%=elemento.get('id_certificado')%> - <%=elemento.get('strnombre') %>  
        <td><%=elemento.get('fe_vencimiento') %></td>
        <td><% if(elemento.get('testing')==1){%>SI<%}else {%>NO<% } %></td>
        <td><% if(elemento.get('habilitado')==1){%>SI<%}else {%>NO<% } %></td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_certificado')%>">VER</button>            
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
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">CONSULTA DE CERTIFICADOS DIGITALES</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-3">
                                    <label>
                                        FECHA EMITIDO DESDE
                                    <input type="text" class="form-control" id="patronfe_desde" placeholder="fecha desde"  style="text-align:right"/>
                                    </label>
                                 </div>
                                 <div class="col-md-3">
                                    <label>
                                        FECHA EMITIDO HASTA
                                    <input type="text" class="form-control" id="patronfe_hasta" placeholder="fecha hasta"  style="text-align:right"/>
                                </label>
                                 </div>
                                 <div class="col-md-3" style="text-align:center">
                                     <label>USO TESTING
                                    <input type="checkbox" class="form-control" id="patrontesting" />
                                    </label>
                                 </div>
                                 <div class="col-md-3" style="text-align:center">
                                     <label>HABILITADOS
                                    <input type="checkbox" class="form-control" id="patronhabilitado" />
                                    </label>
                                 </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success" id="btnRealizarAlta" onclick="realizaralta()">Crear Certificado</button>
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
App.setPageTitle('Certificados digitales | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;

var ocampoView=null;    
var oColecciones=null;
var oCampos=null;
var olista=null;

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
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/certificados/");
                             })
                          });
                        });
                    });
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);

var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/certificados/listener',   
    idAttribute:'id_certificado',
    defaults:{
        id_certificado:0,
        fe_emision:'',
        fe_vencimiento:'',
        habilitado:true,
        testing:false,
        path:'',
        path_key:'',
        cuit:'',
        clave:'',
        nombre:'',
        sujeto:'',
        editor:'',
        strnombre:''
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();        
        var cond=""
        var cond="1=1 "
        
        if(patrones['fe_desde']!="")
        {var strdate=todate(patrones['fe_desde'],103);
            cond+=' and fe_emision>="'+strdate+'"';
        }
        if(patrones['fe_hasta']!="")
        {
            var strdate=todate(patrones['fe_hasta'],103);
            cond+=" and fe_emision< ADDDATE('"+strdate+"',1)";
        }
        if(patrones['habilitado']!=0)
        {
            cond+=' and habilitado='+patrones['habilitado'];
        }
        if(patrones['testing']!=0)
        {
            cond+=' and testing='+patrones['testing'];
        }
        
                
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }


        ofwlocal.getAsync("vercertificados",Array("id_certificado,DATE_FORMAT(fe_emision,'%d/%m/%Y %r') as fe_emision,DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento,path,path_key,testing,habilitado,cuit,clave,nombre,sujeto,editor,strnombre"),cond,"fe_emision desc",function(rs){ that.cargar(rs,that) });       
        
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
        this.$el.html(tpl({ls:olist}));
         $('#data-table').DataTable({responsive: true}); 
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {
        var id_model=(e.target.id).replace("view-","");
        var res=oColecciones.where({"id_certificado":id_model})
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
        cCampo=new Campo({valor:modelo.get('id_certificado'),nombre:'id_certificado',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('fe_emision'),nombre:'fe_emision',tipo:'readonly',etiqueta:'Fecha de emisión',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('fe_vencimiento'),nombre:'fe_vencimiento',tipo:'readonly',etiqueta:'Fecha de vencimiento',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('nombre'),nombre:'nombre',tipo:'readonly',etiqueta:'Nombre',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('sujeto'),nombre:'sujeto',tipo:'readonly',etiqueta:'Sujeto',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('editor'),nombre:'editor',tipo:'readonly',etiqueta:'Editor',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:'<?=base_url()?>'+modelo.get('path'),nombre:'path',tipo:'file',etiqueta:'Certificado digital',obligatorio:true,accept:'.crt'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:'<?=base_url()?>'+modelo.get('path_key'),nombre:'path_key',tipo:'file',etiqueta:'Archivo de clave privada',obligatorio:true,accept:'.key'});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('clave'),nombre:'clave',tipo:'int',etiqueta:'Clave del certificado'});
        oCampos.add(cCampo); 

        cCampo=new Campo({valor:modelo.get('habilitado'),nombre:'habilitado',tipo:'checkbox',etiqueta:'Habilitado para operar'});
        oCampos.add(cCampo); 

        cCampo=new Campo({valor:modelo.get('testing'),nombre:'testing',tipo:'checkbox',etiqueta:'¿Uso testing?'});
        oCampos.add(cCampo); 

        cCampo=new Campo({valor:modelo.get('cuit'),nombre:'cuit',tipo:'int',etiqueta:'Cuit negocio', obligatorio:true});
        oCampos.add(cCampo); 
        

        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'AEV',base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
                      
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
        
        //ocampoView.$el=$("#row-body")
        ocampoView.render(modo)
        if(modo=='A' || modo=='E')
        {
            ocampoView.options.validar=function(e)
            { var strhtml=''
                    /*if(e['fe_emision']=="")
                    {
                        strhtml+="<li>Debe ingresar una fecha de emisión del certificado</li>"
                    }
                    if(e['fe_vencimiento']=="" )
                    {
                        strhtml+="<li>Debe ingresar una fecha de vencimiento del certificado</li>"
                    }*/

                    if(e['cuit']=="" )
                    {
                        strhtml+="<li>El cuit es obligatorio</li>"
                    }
                    if(modo!='E')
                    {
                        if(e['path']=="")
                        {
                            strhtml+="<li>Debe ingresar el certificado digital</li>"
                        }
                        if(e['path_key']=="")
                        {
                            strhtml+="<li>Debe ingresar el archivo de clave privada</li>"
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
            var patrones={fe_desde:$("#patronfe_desde").val(),fe_hasta:$("#patronfe_hasta").val(),testing:($("#patrontesting").is(":checked"))?1:0,habilitado:($("#patronhabilitado").is(":checked"))?1:0}    
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
            var patrones={fe_desde:$("#patronfe_desde").val(),fe_hasta:$("#patronfe_hasta").val(),testing:($("#patrontesting").is(":checked"))?1:0,habilitado:($("#patronhabilitado").is(":checked"))?1:0}    

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
olista=new ElementosView({el:$('#tpl-table-query')});
$("#patronfe_desde").mask("99/99/9999");
                $("#patronfe_desde").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

$("#patronfe_hasta").mask("99/99/9999");
                $("#patronfe_hasta").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });                

}//inicializacion contexto

function buscar(evt)
{
evt.preventDefault();
var patrones={fe_desde:$("#patronfe_desde").val(),fe_hasta:$("#patronfe_hasta").val(),testing:($("#patrontesting").is(":checked"))?1:0,habilitado:($("#patronhabilitado").is(":checked"))?1:0}    
olista.render(patrones);
}

function realizaralta()
{   
    var newModel=new Elemento()    
    olista.mostrar(newModel,'A')

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->