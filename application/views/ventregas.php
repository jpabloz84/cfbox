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
                                <th>CLIENTE</th>                                        
                                <th>DOCUMENTO</th>                                        
                                <th>TELEFONO</th>
                                <th>DOMICILIO</th>           
                                <th>CARTON</th>           
                                <th>ESTADO</th>           
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) { 
    var strhab=(elemento.get('habilitado')==1)?"SI":"NO"
     %>
    <tr >        
        <td><%=elemento.get('strnombrecompleto') %></td>
        <td><%=elemento.get('documento') %> - <%=elemento.get('nro_docu') %> </td>
        <td><%=elemento.get('telefono') %></td>
        <td><%=elemento.get('domicilio') %> - <%=elemento.get('descripcion_loc') %> (<%=elemento.get('descripcion_pro') %>)</td>
        <td><%=elemento.get('id_carton') %> (bingo <%=elemento.get('id_bingo') %>) </td>
        <td><%=(elemento.get('entregado')==1)?"ENTREGADO "+elemento.get('fe_entregado'):" SIN ENTREGAR" %></td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_carton')%>-<%=elemento.get('nro_premio')%>">VER</button>            
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
    <input type="hidden" id="txtCuc_BarCode"  value="" />
    <input type="hidden" id="txtCuc_QRCode" value="" />
    
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel1">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:abrircamara();" class="btn btn-xs btn-success" >Escanear<i class="fa fa-camera"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">CONSULTA DE PREMIOS - CLIENTES</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">   
                                 <div class="col-md-4">
                                    <label for="patronnombres"> Nombre del cliente</label>
                                    <input type="text" class="form-control" id="patronnombres" placeholder="apellido y nombres"  />
                                 </div>
                                  <div class="col-md-2">
                                    <label for="patron_nro_docu"> Nro. docu.</label>
                                    <input type="text" class="form-control" id="patron_nro_docu" placeholder="numero de documento" />
                                 </div>                                                                
                                 <div class="col-md-2">
                                    <label for="patron_nro_carton"> Nro. carton</label>
                                    <input type="text" class="form-control" id="patron_nro_carton" placeholder="nro." />
                                 </div>
                                 <div class="col-md-2">
                                    <label for="patron_nro_carton">Sin entregar</label>
                                <input type="checkbox" class="form-control" id="patron_sin_entregar" checked="checked" />
                                 </div>
                                 <div class="col-md-2">
                                 <button type="button" class="btn btn-sm btn-primary btn-block"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> buscando..." id="bntBuscar">BUSCAR&nbsp;<i class="fa fa-search"></i>
                                </button>
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
    <!-- begin row -->
    <div class="row" id="tpl-tabla-parametros">
    <!-- tabla --> 
    </div>

</div>
<!-- end #content -->

<div id="modalValidarCuponCUC_BarCode" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <h4 class="modal-title">Escanear código de barras</h4>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <div id="resultado"></div>
                    <div class="form-group col-md-12">

                      <div id="mainbody_">
                        <div id="camera"></div>
                        </div>
                        <input class="form-control input-lg" type="text" readonly id="txtCuc_BarCode">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" id="btnSalir_Barcode">Salir</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade in" id="modalValidarCuponCUC_QRCode" >
   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">ESCANEAR QR</h4>
            </div>
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <div id="mainbody">

                        <div id="outdiv" style="text-align: center;">
                        </div>
                        <div id="result"></div>
                        <canvas id="qr-canvas" width="800" height="600"></canvas>
                        <!--<input class="form-control input-lg" type="text" id="txtCuc_QRCode">-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Salir</a>
                
            </div>
        </div>
    </div>
</div>

 

  <!-- Lector QR   -->
  <script src="<?=base_url()?>js/qrcode/llqrcode.js" type="text/javascript"></script>
  <script src="<?=base_url()?>js/qrcode/webqr.js" type="text/javascript"></script>
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];

App.restartGlobalFunction();
App.setPageTitle('Consulta de premios de clientes | Vivobingo APP');
var ofwlocal=new fwt('<?=base_url()?>index.php/entidades/entregas/listener')

var ocampoView=null;    
var oPremiosClientes=null;
var oCampos=null;
var olista=null;
var oElementView=null;




$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entregas/");
                         })
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);

var Premio_parametro=Backbone.Model.extend();
var Premios_parametros=Backbone.Collection.extend({models:Premio_parametro});
var oPremiosTipos=new Premiotipos()
var PremioCliente=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/entidades/entregas/guardar',   

    cargardetalle:function(){
        var id_bingo=this.get("id_bingo")
        var nro_premio=this.get("nro_premio")
        var rs=ofwlocal.get("selparametros",{id_bingo:id_bingo,nro_premio:nro_premio})
        var premios_parametros=new Premios_parametros()
        for(r in rs){
            premios_parametros.add(rs[r])
        }
        this.set({premios_parametros:premios_parametros})

    }   
});//PremioClientemodel
var PremiosClientes=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();        
       
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }
        //si id_bingo esta definido , es porq busco por codigo QR, sino por patrones de busqueda manual
        if(typeof patrones['id_bingo']!="undefined") {
        
        ofwlocal.getAsync("selpremios_clientes2",patrones,function(rs){ that.cargar(rs,that) } )
        }else{
            ofwlocal.getAsync("selpremios_clientes",patrones,function(rs){ that.cargar(rs,that) } )
        }

        
    },    
    cargar:function(rs,that)
    {       
     for (c in rs)
     {
      that.add(rs[c])
      }
      for(r in that.models){
       that.models[r].cargardetalle()
      }

    if(typeof that.options.eventos !="undefined")
    {
     that.options.eventos.trigger("endload",that);
    }
    },
    model:PremioCliente
});


var Premios_parametrosView=Backbone.View.extend({el:$("#tpl-tabla-parametros"),
    initialize:function(options){        
        this.options=options || {}             
    },
    dibujar:function(premios_parametros){

this.options.parametros=premios_parametros
var that=this
       $.get(this.options.base_url+'tpl/entregas_premios_parametros.html', function (data) {
        
        tpl = _.template(data, {});
        htmlrender=tpl({premios_parametros:premios_parametros,premios_tipos:oPremiosTipos,base_url:that.options.base_url})
        that.$el.html(htmlrender);

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
        $('#tpl-tabla-parametros').show()
        /*if(!$('[id="modal-premio-edit"]').is(':visible')){
        $('[id="modal-premio-edit"]').modal("show")    
        }*/
        
        })//get html



}//dibujar
})



var PremiosclientesView=Backbone.View.extend(
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
        
        if(oPremiosClientes ==null)
        {
        oPremiosClientes=new PremiosClientes({eventos:eventos});    
        }
        
        oPremiosClientes.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    { 
        
     $("#bntBuscar").button("loading");   
      spinnerStart($('#panel-body'));  
    },
    endload:function(ocol)
    {
      $("#bntBuscar").button("reset");
      this.cargar(ocol)
    },
    events:{
            "click button[name='ver']":'ver'
    },
    cargar:function(oPremiosClientes)
    {               
        olist=oPremiosClientes.models  
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));
         /*$('#data-table').DataTable({responsive: true,searching:false,pageLength:20,
            "columns": [
            { "orderable": true },
            { "orderable": true },                        
            { "orderable": true },                        
            { "orderable": true },                        
            { "orderable": true },                        
            { "orderable": true },                        
            { "orderable": false}]
         }); */
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {   
        e.preventDefault()
        var strview=(e.target.id.indexOf("view-")>=0)?(e.target.id).replace("view-",""):(e.target.ParentNode.id).replace("view-","");

        var p=strview.split("-")
        var id_carton=p[0]
        var nro_premio=p[1]
        var res=oPremiosClientes.where({"id_carton":id_carton,"nro_premio":nro_premio})
        var modelo=null;
        if(res.length==0)
        {
            return        
        }
        modelo=res[0]
        this.mostrar(modelo,'V')
        var opremios_parametrosview=new Premios_parametrosView({el:$("#tpl-tabla-parametros"),base_url:this.options.base_url});
        
        opremios_parametrosview.dibujar(modelo.get("premios_parametros"))

    },
    mostrar:function(modelo,modo)
    {   var str=''
        var cCampo=null
    
        oCampos=new Campos(); //PremiosCliente        cCampo=new Campo({valor:modelo.get('id_cliente'),nombre:'id_cliente',tipo:'hidden',identificador:true});
        //oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('id_carton'),nombre:'carton',tipo:'text',etiqueta:'Nro. carton',readonly:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('nro_premio'),nombre:'nro_premio',tipo:'text',etiqueta:'Nro. premio',readonly:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('strnombrecompleto'),nombre:'apenom',tipo:'text',etiqueta:'Apellido y Nombre',readonly:true});
        oCampos.add(cCampo);
        str=modelo.get('documento')+' - '+modelo.get('nro_docu')
        cCampo=new Campo({valor:str,nombre:'documentos',tipo:'text',etiqueta:'Numero de documento',readonly:true});
        oCampos.add(cCampo);
        str=''
        
        str=(modelo.get('sexo')=='M')?'HOMBRE':'MUJER'
        cCampo=new Campo({valor:str,nombre:'sexo',tipo:'text',etiqueta:'Sexo',readonly:true});
        oCampos.add(cCampo);
                
        
        cCampo=new Campo({valor:modelo.get('telefono'),nombre:'telefono',tipo:'text',etiqueta:'telefono',readonly:true});
        oCampos.add(cCampo);
       
        
        str=modelo.get('domicilio')+' ('+modelo.get('descripcion_loc')+'- '+modelo.get('descripcion_pro')+')'
        cCampo=new Campo({valor:str,nombre:'domicilio',tipo:'text',etiqueta:'Domicilio',readonly:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('usuario_mail'),nombre:'email',tipo:'text',etiqueta:'Email',readonly:true});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('entregado'),nombre:'entregado',tipo:'checkbox',etiqueta:'Entregado'});
        oCampos.add(cCampo);
        var str=(modelo.get('entregado')==1?modelo.get('fe_entregado')+" - Usuario "+modelo.get("usuario_entrega"):"no entregado aun")
         cCampo=new Campo({valor:str,nombre:'fe_entregado',tipo:'text',etiqueta:'Cuando se entrego?',readonly:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('item'),nombre:'item',tipo:'text',etiqueta:'Item de carton',readonly:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:'<?=base_url()?>'+modelo.get('img_profile'),nombre:'img_profile',tipo:'image',etiqueta:'Avatar',download:true,readonly:true});
        oCampos.add(cCampo);
        if(oPremiosClientes ==null)
        {
        oPremiosClientes=new PremiosClientes();    
        }
        
        if(ocampoView ==null)
        {   var strpermite='EV'

            if(permitir(prt_entregas,8))
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
               // $("#botonera3").html('<button type="button" class="btn btn-sm btn-primary" id="editarbtn">Editar</button>')
               /* $("#editarbtn").click(function(e){
                    if(permitir(prt_clientes,4))
                    {
                    handleLoadPage("#<?php echo base_url(); ?>index.php/entidades/entrefas/modificar/"+modelo.get('id_cliente'))   
                    }else{
                        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
                    }
                })*/
                
            }
        }


        ocampoView.options.verificar=function(modelo){
       }//para verificar antes de eliminar articulo

        ocampoView.options.volver=function(){                
                        if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body").slideToggle();            
                        }
                        $("#row-body").hide();    
                        $("#row-body").html("");
                        $("#tpl-tabla-parametros").hide();    
                        $("#tpl-tabla-parametros").html("");
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
            var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),nro_carton:$("#patron_nro_carton").val(),sin_entregar:$("#patron_sin_entregar").is(":checked")?1:0}
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
            var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),nro_carton:$("#patron_nro_carton").val(),sin_entregar:$("#patron_sin_entregar").is(":checked")?1:0}
            olista.render(patrones);
        }
        ocampoView.options.before=function()
        {
            spinnerStart($('#panel-body-view'));
        }//antes

        
         
}
    


});//PremiosclientesView
function inicializacion_contexto()
{
      
olista=new PremiosclientesView({el:$('#tpl-table-query'),base_url:"<?=base_url()?>"}); 
     
 $("#patron_nro_carton").keypress(function(e){
    if(e.which==13){
    consultar();
    }
    return teclaentero(e)
});     
 $("#patron_nro_docu").keypress(function(e){
    if(e.which==13){
    consultar();
    }
    return teclaentero(e)
 });     
}//inicializacion contexto


$("#bntBuscar").on("click",function(e){ 
    e.stopPropagation();
consultar();    
})


function consultar(){
 

var patrones={nro_docu:$("#patron_nro_docu").val(),apenom:$("#patronnombres").val(),nro_carton:$("#patron_nro_carton").val(),sin_entregar:$("#patron_sin_entregar").is(":checked")?1:0}
 olista.render(patrones);    
}

$("#patronnombres").keypress(function(e){
    if(e.which==13){
    consultar();
    }
})



function realizaralta()
{   
     if(permitir(prt_clientes,2))
     {
        handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/clientes/ingresar_alta/");    
     }else {
        win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
     }
   
}


function abrircamara(){  
load();
//la funcion read() dentro de webqr.js, lanza el evento change 
//al escanear el QR
$('#modalValidarCuponCUC_QRCode').modal('show');
}//abrir camara

 $("#txtCuc_QRCode").change(function(e){
            e.stopPropagation();
            e.preventDefault();                      
            
            var txtCuc_QRCode=$("#txtCuc_QRCode").val() 
            var id_bingo=parseInt(txtCuc_QRCode.substr(0,5))
            var id_carton=parseInt(txtCuc_QRCode.substr(5,7))
            var nro_premio=parseInt(txtCuc_QRCode.substr(12,5))

            var patrones={id_bingo:id_bingo,id_carton:id_carton,nro_premio:nro_premio}
            olista.render(patrones);  
            //5 digitos id_bingo, 7 carton, 5 nro_premio
            // ej:00007000000100002
            $("#modalValidarCuponCUC_QRCode").modal("hide")
})

$("#modalValidarCuponCUC_QRCode").on("hidden.bs.modal", function () {
    vidOff(); //apago la camara
});


</script>
<!-- ================== END PAGE LEVEL JS ================== -->