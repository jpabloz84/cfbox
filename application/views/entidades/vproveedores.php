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
<div class="table-responsive">
<table id="data-table" class="table table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>PROVEEDOR</th>                                        
                                <th>RAZON SOCIAL</th>
                                <th>logo</th>                                        
                                <th>DOCUMENTO</th>                                        
                                <th>TELEFONO</th>
                                <th>DOMICILIO</th>
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) { 
    var nro_documento=(elemento.get('tipo_persona')=="J")?elemento.get('cuit'):elemento.get('documento')+' '+elemento.get('nro_docu') 
     %>
    <tr class="gradeA">        
        <td>(<%=elemento.get('id_proveedor') %>) - <%=elemento.get('proveedor') %></td>
        <td><%=elemento.get('strnombrecompleto') %></td>
        <td class="hidden-sm">
          <a href="javascript:;">
            <img src="<%=base_url%><%=elemento.get('img_personal') %>" alt="" id="img-param-c87" class="img-fluid img-thumbnail" data-img="<%=base_url%><%=elemento.get('img_personal') %>" title="" data-toggle="popover" style="height:64px;width: auto" data-original-title="imagen">
          </a>
        </td>
        <td><%=nro_documento %> </td>
        <td><%=elemento.get('car_tel') %> <%=elemento.get('nro_tel') %></td>
        <td><%=elemento.get('calle') %> <%=elemento.get('nro') %> piso :<%=elemento.get('piso') %>   dpto :<%=elemento.get('dpto') %> 
            - <%=elemento.get('descripcion_loc') %> (<%=elemento.get('descripcion_pro') %>)</td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_proveedor')%>">VER</button>            
        </td>
        
    </tr>
    <% }); %>
    </tbody>
</table>
</div>
</script>
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <input type="hidden" id="id_empresa" value="<?=$visitante->get_id_empresa();?>">
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
                <h4 class="panel-title">CONSULTA DE PROVEEDORES</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                <div class="col-md-3">
                                    <label>Razon social
                                    <input type="text" class="form-control" id="patron_proveedor" placeholder="nombre fantasia" />
                                    </label>
                                 </div>
                                 <div class="col-md-2">
                                    <label>Nro. Docu
                                <input type="text" class="form-control" id="patron_nro_docu" placeholder="numero de documento" />
                                    </label>
                                 </div>
                                 <div class="col-md-2">
                                    <label>Cuit
                                <input type="text" class="form-control" id="patron_cuit" placeholder="cuit / cuil" />
                                    </label>
                                 </div>
                                 <div class="col-md-3">
                                    <label>Nombre
                                    <input type="text" class="form-control" id="patronnombres" placeholder="apellido y nombres"  />
                                    </label>
                                 </div>                                 
                                 <div class="col-md-2">
                                 <label>Es Auspiciante
                                    <input type="checkbox"  id="patronauspiciante" class="form-control" checked="checked" />
                                    </label>
                                 </div>              
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success" id="btnRealizarAlta" onclick="realizaralta()">CREAR PROVEEDOR</button>
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
App.setPageTitle('Consulta de proveedores | CoffeBox APP');
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


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/proveedores/");
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
    idAttribute:'id_proveedor'   
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();        
        var cond="id_empresa="+$("#id_empresa").val()
        if(patrones['proveedor']!="")
        {
            cond+=' and proveedor like "'+patrones['proveedor']+'%"'
        }
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

        if(patrones['auspiciante']==1)
        {
            cond+=' and auspiciante=1'
        }
                
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }

var cols=Array("id_proveedor","proveedor","observaciones","id_persona","apellido","nombres",  "documento","strnombrecompleto","calle","nro","piso","dpto","cp","car_tel","nro_tel","cp","nro_docu","tipo_docu","cuit","tipo_persona","tipo_persona_desc",  "sexo",  "email","id_loc_nac","descripcion_loc_nac","DATE_FORMAT(fe_nacimiento,'%d/%m/%Y') as fe_nacimiento","id_pro_nac","descripcion_pro_nac","id_loc","descripcion_loc","id_pro","descripcion_pro","auspiciante","img_personal");

        ofwlocal.getAsync("verproveedores",cols,cond,"proveedor asc,apellido asc,nombres asc",function(rs){ that.cargar(rs,that) } )
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
        this.$el.html(tpl({ls:olist,base_url:'<?=base_url()?>'}));
          $('[data-toggle="popover"]').popover({          
          trigger: 'hover',
          html: true,
          container:'body',
          content: function () {   

            var src=$(this).attr("src")                                
          if(src!="" && src.indexOf("/icon-")== -1){
                return '<img class="img-fluid" src="'+$(this).attr("src") + '" />';
          }else{
            return '<p>sin imagen para mostrar</p>';
          }
          },
          title: 'Toolbox'
        })
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {
        var id_model=(e.target.id).replace("view-","");
        var res=oColecciones.where({"id_proveedor":id_model})
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
        cCampo=new Campo({valor:modelo.get('id_proveedor'),nombre:'id_proveedor',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('proveedor'),nombre:'proveedor',tipo:'text',etiqueta:'Nombre Fantasia',esdescriptivo:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('strnombrecompleto'),nombre:'apenom',tipo:'text',etiqueta:'Apellido y Nombre',esdescriptivo:false});
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
        
        cCampo=new Campo({valor:modelo.get('car_tel'),nombre:'car_tel',tipo:'text',etiqueta:'Car. tel.'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('nro_tel'),nombre:'telefono',tipo:'text',etiqueta:'telefono'});
        oCampos.add(cCampo);
        str=''        
         str=(modelo.get('tipo_persona')=='F')?'CUIT':'CUIL'
        
        cCampo=new Campo({valor:modelo.get('cuit'),nombre:'cuit',tipo:'text',etiqueta:str});
        oCampos.add(cCampo); 
        str=modelo.get('calle')+modelo.get('nro')+" piso:"+modelo.get('piso')+" depto:"+modelo.get('dpto')+' ('+modelo.get('descripcion_loc')+'- '+modelo.get('descripcion_pro')+')'
        cCampo=new Campo({valor:str,nombre:'domicilio',tipo:'text',etiqueta:'Domicilio'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('email'),nombre:'email',tipo:'text',etiqueta:'Email'});
        oCampos.add(cCampo);
        str=''
        str=modelo.get('fe_nacimiento')+' '+modelo.get('descripcion_loc_nac')+' - '+modelo.get('descripcion_pro_nac')
        cCampo=new Campo({valor:str,nombre:'nacimiento',tipo:'text',etiqueta:'Nacimiento'});
        oCampos.add(cCampo);        
        cCampo=new Campo({valor:'<?=base_url()?>'+modelo.get('img_personal'),nombre:'img_personal',tipo:'image',etiqueta:'Logo/Imagen'});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('observaciones'),nombre:'observaciones',tipo:'text',etiqueta:'Observaciones'});
        oCampos.add(cCampo);
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        
        if(ocampoView ==null)
        {   var strpermite='EV'
            if(permitir(prt_abm,2))
            {
                strpermite='EV'
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
                    if(permitir(prt_abm,2))
                    {
                    handleLoadPage("#<?php echo base_url(); ?>index.php/entidades/proveedores/modificar/"+modelo.get('id_proveedor'))    
                    }else{
                        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
                    }    

                })
                
            }
        }


        ocampoView.options.verificar=function(modelo){
            var eliminar=true;                
            
                     var rs=ofwlocal.get('comp',Array('id_comp'),'id_proveedor='+modelo.get('id_proveedor'),'');
                        if(rs.length>0)
                        {   
                             strhtml+="<li>No se puede eliminar este proveedor porque tiene relacion con al menos un comprobante del sistema </li>"
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
            var patrones={proveedor:$("#").val(),nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),cuit:$("#patron_cuit").val()}
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
            var patrones={proveedor:$("#patron_proveedor").val(),nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),cuit:$("#patron_cuit").val(), auspiciante:($("#patronauspiciante").prop("checked"))?1:0}
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
var patrones={proveedor:$("#patron_proveedor").val(),nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),cuit:$("#patron_cuit").val(), auspiciante:($("#patronauspiciante").prop("checked"))?1:0}
 olista.render(patrones);
}

function realizaralta()
{   
     if(permitir(prt_abm,2))
     {
        handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/proveedores/ingresar_alta/");    
     }else {
        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
     }
   
}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->