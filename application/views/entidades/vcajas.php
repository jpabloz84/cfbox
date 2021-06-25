<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=base_url()?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=base_url()?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=base_url()?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=base_url()?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>CAJA</th>                                        
                                <th>SUCURSAL</th>                                        
                                <th>TELEFONO</th>
                                <th>DOMICILIO</th>           
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {  %>
    <tr class="gradeA">        
        <td>(<%=elemento.get('id_cliente') %>) - <%=elemento.get('strnombrecompleto') %></td>
        <td><%=elemento.get('documento') %> - <%=elemento.get('nro_docu') %> </td>
        <td><%=elemento.get('telefono') %></td>
        <td><%=elemento.get('domicilio') %> - <%=elemento.get('descripcion_loc') %> (<%=elemento.get('descripcion_pro') %>)</td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_cliente')%>">VER</button>            
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
                <h4 class="panel-title">CONSULTA DE CLIENTES</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-4">
                                <input type="text" class="form-control" id="patron_nro_docu" placeholder="numero de documento" />
                                 </div>
                                 <div class="col-md-4">
                                <input type="text" class="form-control" id="patron_cuit" placeholder="cuit / cuil" />
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" class="form-control" id="patronnombres" placeholder="apellido y nombres"  />
                                 </div>                                 
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success" id="btnRealizarAlta" onclick="realizaralta()">CREAR CLIENTE</button>
                                </div>
                                <div class="col-md-6">                            
                                </div>
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-primary m-r-5" id="bntBuscar" onclick="buscar(event)">BUSCAR</button>
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
App.setPageTitle('Consulta de clientes | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;

var ocampoView=null;    
var oColecciones=null;
var oCampos=null;
var olista=null;
var oCategorias=null;
var oElementView=null;
var oFracciones=null;


$.getScript('<?=base_url()?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=base_url()?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=base_url()?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=base_url()?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=base_url()?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=base_url()?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/clientes/");
                         })
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);


var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/entidades/cliente/listener',   
    idAttribute:'id_cliente'   
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();        
        var cond="1=1 "
        if(patrones['apenom']!="")
        {
            cond+=' and strnombrecompleto like "'+patrones['apenom']+'%"'
        }
        if(patrones['nro_docu']!="")
        {
            cond+=' and nro_docu='+patrones['nro_docu']
        }
        if(patrones['cuit']!="")
        {
            cond+=' and cuit="'+patrones['cuit']+'"'
        }
                
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }


        ofwlocal.getAsync("verclientes",Array("id_cliente,condicion,strnombrecompleto,tipo_persona,domicilio,telefono,cuit,nro_docu,documento,sexo,img_personal,email,observaciones,descripcion_loc,descripcion_pro,descripcion_loc_nac,DATE_FORMAT(fe_nacimiento,'%d/%m/%Y') as fe_nacimiento,descripcion_pro_nac"),cond,"apellido asc,nombres asc",function(rs){ that.cargar(rs,that) } )
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
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
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
        var res=oColecciones.where({"id_cliente":id_model})
        var modelo=null;
        if(res.length==0)
        {
            return        
        }
        modelo=res[0]
        this.mostrar(modelo,'V')

    },
    mostrar:function(modelo,modo)
    {   var str=''
        oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modelo.get('id_cliente'),nombre:'id_cliente',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('strnombrecompleto'),nombre:'apenom',tipo:'text',etiqueta:'Apellido y Nombre',esdescriptivo:true});
        oCampos.add(cCampo);
        str=modelo.get('documento')+' - '+modelo.get('nro_docu')
        cCampo=new Campo({valor:str,nombre:'documentos',tipo:'text',etiqueta:'Numero de documento'});
        oCampos.add(cCampo);
        str=''
        if(modelo.get('tipo_persona')=='F')
        {
            str=(modelo.get('sexo')=='M')?'HOMBRE':'MUJER'
            cCampo=new Campo({valor:str,nombre:'sexo',tipo:'text',etiqueta:'Sexo'});
            oCampos.add(cCampo);
        }        
        
        cCampo=new Campo({valor:modelo.get('telefono'),nombre:'telefono',tipo:'text',etiqueta:'telefono'});
        oCampos.add(cCampo);
        str=''        
         str=(modelo.get('tipo_persona')=='F')?'CUIT':'CUIL'
        
        cCampo=new Campo({valor:modelo.get('cuit'),nombre:'cuit',tipo:'text',etiqueta:str});
        oCampos.add(cCampo); 
        str=modelo.get('domicilio')+' ('+modelo.get('descripcion_loc')+'- '+modelo.get('descripcion_pro')+')'
        cCampo=new Campo({valor:str,nombre:'domicilio',tipo:'text',etiqueta:'Domicilio'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('email'),nombre:'email',tipo:'text',etiqueta:'Email'});
        oCampos.add(cCampo);
        str=''
        str=modelo.get('fe_nacimiento')+' '+modelo.get('descripcion_loc_nac')+' - '+modelo.get('descripcion_pro_nac')
        cCampo=new Campo({valor:str,nombre:'nacimiento',tipo:'text',etiqueta:'Nacimiento'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('condicion'),nombre:'condicion',tipo:'text',etiqueta:'Condicion ante IVA'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('observaciones'),nombre:'observaciones',tipo:'text',etiqueta:'Observaciones'});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:'<?=base_url()?>'+modelo.get('img_personal'),nombre:'img_personal',tipo:'image',etiqueta:'Avatar'});
        oCampos.add(cCampo);
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        
        if(ocampoView ==null)
        {   var strpermite='EV'
            if(permitir(prt_clientes,8))
            {
                strpermite='EVD'
            }
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:strpermite,base_url:'<?=base_url()?>',el:$("#row-body")});
            
                      
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.modelo=modelo
        }

        if(modo=="V")
        {  ocampoView.options.onafterrender=function(){                
                $("#botonera3").html('<button type="button" class="btn btn-sm btn-primary" id="editarbtn">Editar</button>')
                $("#editarbtn").click(function(e){
                    if(permitir(prt_clientes,4))
                    {
                    handleLoadPage("#<?php echo base_url(); ?>index.php/entidades/clientes/modificar/"+modelo.get('id_cliente'))    
                    }else{
                        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
                    }    

                })
                
            }
        }


        ocampoView.options.verificar=function(modelo){
            var eliminar=true;                
            
                     var rs=ofwlocal.get('comp',Array('id_comp'),'id_cliente='+modelo.get('id_cliente'),'');
                        if(rs.length>0)
                        {   
                             strhtml+="<li>No se puede eliminar este cliente porque tiene relacion con al menos un comprobante del sistema </li>"
                             win.alert("<ul>"+strhtml+"</ul>","ATENCIÓN",4)
                             eliminar=false;
                        }   
                        return eliminar;

       }//para verificar antes de eliminar articulo

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
        
         
        ocampoView.options.error=function()
        {
            spinnerEnd($('#panel-body-view'));
            if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                    $("#panel-body").slideToggle();            
                    }
            $("#row-body").hide();    
            $("#row-body").html("");
            var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),cuit:$("#patron_cuit").val()}
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
            var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),cuit:$("#patron_cuit").val()}
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
 $("#patron_cuit").keypress(function(e){return teclaentero(e)});     
 $("#patron_nro_docu").keypress(function(e){return teclaentero(e)});     
}//inicializacion contexto

function buscar(evt)
{
evt.preventDefault();
var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),cuit:$("#patron_cuit").val()}
 olista.render(patrones);
}

function realizaralta()
{   
     if(permitir(prt_clientes,2))
     {
        handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/clientes/ingresar_alta/");    
     }else {
        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
     }
   
}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->