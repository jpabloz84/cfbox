<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$id_empresa=$visitante->get_id_empresa();
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<link href="<?=base_url()?>js/croppie/croppie.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->

<script type="text/template" id="tpl-table-list"> 
<table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>ARTICULO</th>                                        
                                <th>CATEGORIA</th>                                        
                                <th>STOCK</th>
                                <th>PRECIO VENTA</th>
                                <th>HABILITADO</th>                                        
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {  
    var stock=0
        if(elemento.get('stock') ==null){
        stock=0;
        }else{
            stock=elemento.get('stock')
            if(elemento.get('tipo_dato')=='int'){
            stock=parseInt(elemento.get('stock'))      
            }
        }
    
    %>
    <tr >        
        <td><%=elemento.get('articulo') %> (ID <%=elemento.get('id_articulo') %>)</td>
        <td><%=elemento.get('categoria') %></td>
        <td><%=stock %></td>
        <td>$ <%=elemento.get('precio_venta') %></td>
        <td><% if(elemento.get('habilitado')==1){%>SI<%}else {%>NO<% } %></td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_articulo')%>">VER</button>            
        </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<!-- ================== END PAGE LEVEL STYLE ================== -->

<!-- begin #content -->
<div id="content" class="content"> 
    <input type="hidden" id="id_empresa" value="<?=$id_empresa?>" />
    <input type="hidden" id="base_url" value="<?=base_url();?>" />
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel1">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                 <a href="javascript:realizaralta();" class="btn btn-xs btn-success">crear <i class="fa fa-plus-square"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">ARTICULOS</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">                                
                                <div class="form-group">                                
                                <div class="col-md-3  col-xs-6">
                                    <input type="text" class="form-control" id="patrondescripcion" placeholder="Descripción" />
                                 </div>
                                 <div class="col-md-3  col-xs-6">
                                    <select class="form-control" id="id_categoria">
                                        <option value=''></option>
                                        <?php foreach ($categorias as  $cat) {
                                            $id_categoria=$cat['id_categoria'];
                                            $categoria=$cat['categoria'];
                                          echo "<option value='$id_categoria'>$categoria</option>";
                                        }
                                        ?>
                                    </select>
                                 </div>
                                 <div class="col-md-2">
                                    <input type="text" class="form-control" id="patroncodbarras" placeholder="Codigo de barras" />
                                 </div>
                                 <div class="col-md-2 col-xs-5">
                                    <input type="text" class="form-control" id="patronid" placeholder="Identificador"  style="text-align:right"/>
                                 </div>
                                 <div class="col-md-2 col-xs-7">
                                            <div class="btn-toolbar">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-md p-r-1 p-l-1" id="bntBuscar" onclick="buscar(event)">Consultar <i class="fa fa-search"></i></button>
                                                    <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle">
                                                        <span class="caret"></span>
                                                    </a>
                                                    <ul class="dropdown-menu pull-right">
                                                        <li><a href="javascript:exportar(event);" >Exportar a excel</a></li>
                                                    </ul>                                                    
                                                </div>
                                            </div>
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
App.setPageTitle('Consulta de articulos | CoffeBox APP');
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
var oAlicuotas=null;


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        //TableManageResponsive.init();
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/articulos/");
                         })
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);


var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/entidades/articulos/listener',   
    idAttribute:'id_articulo',
    defaults:{
        id_articulo:0,
        articulo:'',
        habilitado:true,
        precio_base:0,
        precio_iva:0,
        precio_venta:0,
        id_categoria:0,
        id_fraccion:1,
        stock:0,
        codbarras:'',
        mueve_stock:true,
        generico:false,
        id_alicuota:1, //sin iva
        precio_iva:0
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        
        var that=this;
        that.reset();        
        var cond=" id_empresa="+$("#id_empresa").val()
        
        for(c in patrones)
        {   if(c=="id_articulo" && patrones[c]!=""){
            cond=c+"="+patrones[c];
            break;
            }else{
                if(patrones[c]!="")
                {   
                        if(cond!="")
                        {   if(c=='articulo'){
                            cond+=" and "+c+" like '"+patrones[c]+"%'";
                            }else{
                            cond+=" and "+c+"='"+patrones[c]+"'";    
                            }
                            
                        }
                }
            }

         }//for
            
        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }        
        ofwlocal.getAsync("verarticulos",Array("id_articulo,articulo,habilitado,precio_venta,precio_base,precio_iva,id_alicuota,id_categoria,categoria,id_fraccion,ifnull(stock,0) as stock,codbarras,mueve_stock,generico,tipo_dato,detalle,img"),cond,"articulo asc",function(rs){ that.cargar(rs,that) } )
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
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    
        }
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
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
         $('#data-table').DataTable({responsive: true,
            searching:false,pageLength:10,
            "columns": [
            { "orderable": true },
            { "orderable": true },
            { "orderable": true},
            { "orderable": true},
            { "orderable": true},
            { "orderable": false}
            ]
         }); 
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {
        var id_model=(e.target.id).replace("view-","");
        var res=oColecciones.where({"id_articulo":id_model})
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
        cCampo=new Campo({valor:modelo.get('id_articulo'),nombre:'id_articulo',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('articulo'),nombre:'articulo',tipo:'text',etiqueta:'Articulo',esdescriptivo:true,obligatorio:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('detalle'),nombre:'detalle',tipo:'longtext',etiqueta:'Detalle/Descripcion',obligatorio:false});
        oCampos.add(cCampo);               
        cCampo=new Campo({valor:parseInt(modelo.get('id_categoria')),nombre:'id_categoria',tipo:'select',etiqueta:'Categoria',coleccion:oCategorias,obligatorio:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:parseInt(modelo.get('id_fraccion')),nombre:'id_fraccion',tipo:'select',etiqueta:'Fraccion',coleccion:oFracciones});
        oCampos.add(cCampo);
        var stock=0;
        if(modelo.get('tipo_dato')=='int'){
            stock=parseInt(modelo.get('stock'))
        }

        cCampo=new Campo({valor:stock,nombre:'stock',tipo:modelo.get('tipo_dato'),etiqueta:'Stock'});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('precio_base'),nombre:'precio_base',tipo:'money',etiqueta:'Precio Sugerido',obligatorio:true});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('id_alicuota'),nombre:'id_alicuota',tipo:'select',etiqueta:'Alicuota',coleccion:oAlicuotas});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('precio_iva'),nombre:'precio_iva',tipo:'money',etiqueta:'iva',readonly:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('precio_venta'),nombre:'precio_venta',tipo:'money',etiqueta:'Precio final',readonly:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('habilitado'),nombre:'habilitado',tipo:'checkbox',etiqueta:'Habilitado para vender'});
        oCampos.add(cCampo); 
        cCampo=new Campo({valor:modelo.get('mueve_stock'),nombre:'mueve_stock',tipo:'checkbox',etiqueta:'Mueve stock'});
        oCampos.add(cCampo);
        /*cCampo=new Campo({valor:modelo.get('generico'),nombre:'generico',tipo:'checkbox',etiqueta:'¿Es generico?'});
        oCampos.add(cCampo);*/
        cCampo=new Campo({valor:modelo.get('codbarras'),nombre:'codbarras',tipo:'text',etiqueta:'Codigo de barras'});
        oCampos.add(cCampo);
        var pathimg=$("#base_url").val()+"assets/img/default.png"
        if(modelo.get('img')!=null){
            pathimg=$("#base_url").val()+modelo.get('img')
        }
        cCampo=new Campo({valor:pathimg,nombre:'img',tipo:'image',etiqueta:'Imagen',obligatorio:true,accept:'image/*'});
        oCampos.add(cCampo);

        


        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    
        }
        
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'EVD',base_url:'<?=base_url()?>',el:$("#row-body"),tplname:'form_multipart_v2.html'});
                      
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.modelo=modelo
        }

        ocampoView.options.verificar=function(modelo){
            var eliminar=true;                
            
                     var rs=ofwlocal.get('vercomp_det',Array('nro_item'),'nro_tipo='+modelo.get('id_articulo')+' and id_tipo=1','');
                        if(rs.length>0)
                        {   
                             /*strhtml+="<li>No se puede eliminar este articulo porque tiene relacion con al menos un comprobante del sistema (No se aconseja eliminarlo. Solo deshabilitarlo)</li>"
                             win.alert("<ul>"+strhtml+"</ul>","ATENCIÓN",4)*/
                             swal("ATENCIÓN","No se puede eliminar este articulo porque tiene relacion con al menos un comprobante del sistema (No se aconseja eliminarlo. Solo deshabilitarlo)","warning")
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

        ocampoView.options.onafterrender=function(){
            $("#precio_base").keyup(function(e){
                
                var p=0;
                var precio_base=$("#precio_base").val()
                if(es_numero(precio_base))
                {
                    p=parseFloat(precio_base)
                }
                var aliModel=oAlicuotas.findWhere({id:$("#id_alicuota").val()})
                if(typeof aliModel !="undefined")
                {
                var alicuota=parseFloat(aliModel.get("valor"));
                var precio_iva=p*alicuota;
                $("#precio_iva").val(tomoney(precio_iva))
                var precio_venta=precio_iva+p;
                $("#precio_venta").val(tomoney(precio_venta))
                }

            })
            $("#id_alicuota").change(function(){
                
                var p=0;
                var precio_base=$("#precio_base").val()
                if(es_numero(precio_base))
                {
                    p=parseFloat(precio_base)
                }
                var aliModel=oAlicuotas.findWhere({id:$("#id_alicuota").val()})
                if(typeof aliModel !="undefined")
                {
                var alicuota=parseFloat(aliModel.get("valor"));
                var precio_iva=p*alicuota;
                $("#precio_iva").val(tomoney(precio_iva))
                var precio_venta=precio_iva+p;
                $("#precio_venta").val(tomoney(precio_venta))
                }
                

            })
        }

        ocampoView.render(modo)
        if(modo=='A' || modo=='E')
        {
            ocampoView.options.validar=function(e)
            { var strhtml=''

                    if(e['articulo']=="")
                    {
                        strhtml+="<li>Debe ingresar un nombre para el articulo</li>"
                    }
                    if(e['precio_venta']=="" || !es_numero(e['precio_venta']))
                    {
                        strhtml+="<li>El valor ingresado para el precio de venta, es incorrecto o es vacio</li>"
                    }

                    if(e['precio_base']=="" || !es_numero(e['precio_base']))
                    {
                        strhtml+="<li>El valor  precio de base, es incorrecto o es vacio</li>"
                    }
                    if(e['id_categoria']=="" || e['id_categoria']==0)
                    {
                        strhtml+="<li>No seleccionó categoria</li>"
                    }
                    if(e['id_fraccion']=="" || e['id_fraccion']==0)
                    {
                        strhtml+="<li>No seleccionó una fracción de unidad a vender para este artículo</li>"
                    }

                    if(e['id_alicuota']=="" || e['id_alicuota']==0)
                    {
                        strhtml+="<li>No seleccionó una alicuota (si no tiene , seleccione sin alicuota)</li>"
                    }
                    if(e['codbarras']!="" && e['modo']=='A')
                    {
                        var rs=ofwlocal.get('verarticulos',Array('articulo','codbarras','categoria'),'(codbarras="'+e['codbarras']+'" and codbarras<>"") or (articulo="'+e['articulo']+'")','');
                        if(rs.length>0)
                        {   
                             strhtml+="<li>Parece que el articulo ya existe: '"+rs[0].articulo+"' (cod barras "+rs[0].codbarras+"- categoria: "+rs[0].categoria+")</li>"
                        }
                
                    }

                     //controlo que siempre hay un articulo generico por categoria   
                    if(e['generico']==1 && e['id_categoria']>0 && e['habilitado']==1)
                    {
                        var rs=ofwlocal.get('verarticulos',Array('id_articulo','articulo','categoria'),'id_articulo<>'+e['id_articulo']+' and id_categoria='+e['id_categoria']+' and generico=1 and habilitado=1','');
                        if(rs.length>0)
                        {   
                             strhtml+="<li>Ya existe un articulo generico para esta categoria (Solo debe haber un articulo generico habilitado por categoria): '"+rs[0].articulo+"' (ID "+rs[0].id_articulo+"- categoria: "+rs[0].categoria+")</li>"
                        }
                
                    }
                    
                    if(strhtml!="")
                    {
                        win.alert("<ul>"+strhtml+"</ul>","Advertencia: Debe corregír los siguientes datos para continuar",3)
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
            var patrones={id_articulo:$("#patronid").val(),codbarras:$("#patroncodbarras").val(),articulo:$("#patrondescripcion").val(),id_categoria:$("#id_categoria").val()}
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
            var patrones={id_articulo:$("#patronid").val(),codbarras:$("#patroncodbarras").val(),articulo:$("#patrondescripcion").val(),id_categoria:$("#id_categoria").val()}
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

 oCategorias=new Categorias();
 oFracciones=new Fracciones();
 oAlicuotas=new Alicuotas();
}//inicializacion contexto

function buscar()
{
    
var patrones={id_articulo:$("#patronid").val(),codbarras:$("#patroncodbarras").val(),articulo:$("#patrondescripcion").val(),id_categoria:$("#id_categoria").val()}
 olista.render(patrones);
}

function exportar(event){
    var patrones={id_articulo:$("#patronid").val(),codbarras:$("#patroncodbarras").val(),articulo:$("#patrondescripcion").val(),id_categoria:$("#id_categoria").val()}
    $.ajax({url:'<?=base_url()?>index.php/entidades/articulos/exportar',
                    type: "post",
                    dataType: "json",
                    data: patrones,                    
                    beforeSend: function(){
                       spinnerStart($('#panel-body'));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body'));
                    },
                    error:function(){
                      spinnerEnd($('#panel-body'));  
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

}

function realizaralta()
{   
    var newModel=new Elemento()
    olista.mostrar(newModel,'A')

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->