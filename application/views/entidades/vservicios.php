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
                                <th>SERVICIO</th>
                                <th>PRECIO VENTA</th>
                                <th>HABILITADO</th>                                        
                                <th>CADETERIA</th>
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {  %>
    <tr class="gradeA">        
        <td><%=elemento.get('servicio') %></td>
        <td>$ <%=elemento.get('precio_venta') %></td>
        <td><% if(elemento.get('habilitado')==1){%>SI<%}else {%>NO<% } %></td>
        <td><% if(elemento.get('cadeteria')==1){%>SI<%}else {%>NO<% } %></td>
        <td><button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_servicio')%>">VER</button>            
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
                    <a href="javascript:realizaralta();" class="btn btn-xs btn-success">crear <i class="fa fa-plus-square"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">SERVICIOS</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-2 col-xs-4">
                                    <label>Id</label>
                                    <div class="controls">                                                
                                    <input type="text" class="form-control" id="patronid" placeholder="Identificador"  style="text-align:right"/>
                                    </div>
                                 </div>
                                 <div class="col-md-5 col-xs-8">
                                    <label>Servicio</label>
                                    <div class="controls">                                                
                                    <input type="text" class="form-control" id="patrondescripcion" placeholder="Descripción" />
                                    </div>
                                 </div>
                                 <div class="col-md-2 col-xs-6">
                                    <label>Cadeteria</label>
                                    <div class="controls">                                                
                                    <input type="checkbox" class="form-control" id="patroncadeteria" />
                                    </div>
                                 </div>
                                 <div class="col-md-3  col-xs-6">
                                     <div class="btn-group btn-group-justified">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary m-r-5" id="bntBuscar" onclick="buscar(event)">Consultar <i class="fa fa-search"></i></button>
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
App.setPageTitle('Consulta de servicios | CoffeBox APP');
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
                        //TableManageResponsive.init();
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/servicios/");
                         })
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);
var Campo=Backbone.Model.extend({    
    defaults:{        
        identificador:false,//es para indicar que este campo se usa como identificador del modelo
        esdescriptivo:false, //se usa para mensajes de alerta y hacer alusion a este campo
        valor:'',
        nombre:'',
        tipo:'text', //text/checkbox/datetime/int/money/select/multiselect
        etiqueta:'',
        obligatorio:false,
        coleccion:null, //solo para casos tipo select y deben ser coleeciones con atributos id/descripcion
    }
})//model campo

var Campos=Backbone.Collection.extend({model:Campo});

var campoView=Backbone.View.extend({
        el:$('#row-body'),
        campos:null,
        initialize:function(options){            
            this.options=options || {};
        },
        render:function(modo)
        {            
        var that=this;        
        this.$el.show();            
        $.get(this.options.base_url+'/tpl/form.html', function (data) {            
            tpl = _.template(data, {});//Option to pass any dynamic values to template
            var camposInputs=that.options.campos.models
            //permite es 'AVDE' -alta, vista, eliminar,editar
            htmlrender=tpl({campos:camposInputs,modo:modo,permite:that.options.permite})
            that.$el.html(htmlrender);//adding this.camposthe template content to the main template.
            
            for(c in camposInputs)
            {
                cp=camposInputs[c];
                if(cp.get('tipo')=='int')
                {
                  $("#"+cp.get('nombre')).keypress(function(e){return teclaentero(e)});  
                }
                if(cp.get('tipo')=='money')
                {
                  $("#"+cp.get('nombre')).keypress(function(e){return teclamoney(e)});  
                }
                if(cp.get('tipo')=='datetime') 
                {                  
                    /*
                necesita:masked-input.min.js/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js/bootstrap-datepicker.min.js
                    */
                $("#"+cp.get('nombre')).mask("99/99/9999");
                $("#"+cp.get('nombre')).datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });
                }//datetime

               if(cp.get('tipo')=='select'){
                  $("#"+cp.get('nombre')).combobox();
               }
               if(cp.get('tipo')=='multiselect'){ //select2/dist/js/select2.min.js
                  $("#"+cp.get('nombre')).select2({ placeholder: "Seleccione..." });
               }
            }//for
        }, 'html');    
        },//render
        events:{
            'submit #form-view':'guardar',            
            "click #eliminarElemento":'eliminar',
            "click #editarElemento":'mostrarEditar',
            "click #volverElemento":'volver'            
        },
        mostrarEditar:function()
        {
            /*var mEdit=null;
            cpID=this.campos.findwhere({identificador:true});
            mEdit=this.modelos.findwhere({cpID.nombre:cpID.valor})*/
            /*var id_model=$('#id_servicio').val();                        
             mEdit=oColecciones.get(id_model);*/
             
            if(typeof this.options.modelo!="undefined")
            {
                this.render("E");
            }
            else
            {
              console.log('no se encontro modelo');  
            }
            //
            
        },
        getdescripcion:function(cmpcoleccion,modelo){
            
            var cmp=cmpcoleccion.findwhere({esdescriptivo:true});
            var res=''
            if(cmp != null)
            {
               res=modelo.get(cmp.nombre) 
            }
            return res;
        },
        getidentificador:function(cmpcoleccion,modelo){
            
            var cmp=cmpcoleccion.findwhere({identificador:true});
            var res=''
            if(cmp != null)
            {
               res=modelo.get(cmp.nombre) 
            }
            return res;
        },
        eliminar:function()
        {            
             var cp=this.options.campos.findWhere({esdescriptivo:true});
             var cpID=this.options.campos.findWhere({identificador:true});
             var col=cpID.get('nombre');
             mRemove=this.options.modelo;

            if(typeof mRemove!="undefined" && mRemove!=null)
            {   urldestroy=mRemove.url+'/'+cpID.get('valor');
                that =this
                win.dialog('¿Usted está seguro que desea eliminar '+cp.get('nombre')+': '+cp.get('valor')+'?',' Atención',4, function(mr){
                    
                    mr.url=urldestroy
                    that.remove(mr)
                } ,mRemove);        
            }
            else
            {
              console.log('no se ingreso modelo');  
            }
            //
            
        },
       remove:function(mRemove)
        {
            
        this.before();
        that=this;
        mRemove.destroy({success:function (e,params){
                            if(params.numerror==0)
                            {                            
                            that.success()
                            }else{
                                win.alert("Error: "+params.descerror,"No se pudo eliminar",4);
                                that.error()
                            }

                    }
                    ,wait: true
                });        
        },//remove
        before:function(){
            if(typeof this.options.before=="function")
            {
             this.options.before();             
            }
        },//accion a ejecutar antes de enviar datos al servidor
        success:function(){
            if(typeof this.options.success=="function")
            {
             this.options.success();             
            }
        },//accion a ejecutar luego de que la accion se haya ejecutado bien
        error:function(){
            if(typeof this.options.error=="function")
            {
             this.options.error();             
            }
        },//accion a ejecutar luego de que la accion se haya ejecutado mal
        volver:function()
        {
            if(typeof this.options.volver=="function")
            {
             this.options.volver();             
            }

        },
        validar:function(inputs)
        { var datosvalidos=false;
            if(typeof this.options.validar=="function")
            {
            datosvalidos=this.options.validar(inputs);             
            }else{
                datosvalidos=true;
            } 
            return datosvalidos           
        },
        guardar:function(ev){
            
            var detailsInputs=$(ev.currentTarget).serializeObject();            
            var strhtml=""
            var metodo=(detailsInputs['modo']=='A')?'POST':'PUT'
            //recorro aquellos campos que sean checkbox para setear correctamente el valor (0 o 1)
             var camposbits=this.options.campos.where({tipo:'checkbox'})
            
             for (e in camposbits)
             {
                var tagname=camposbits[e].get('nombre');
                detailsInputs[tagname]=($("#"+tagname).is(":checked"))?1:0
             }
            
            var that=this;
            if(this.validar(detailsInputs))
            {  
                /*saco los elementos de los inputs que no me sirven para el modelo*/                
               delete detailsInputs['modo'];
               delete detailsInputs['identificador']
               
                this.before();
                if(this.options.modelo!=null)
                {   this.options.modelo.set(detailsInputs)
                    this.options.modelo.save(detailsInputs,{
                    type: metodo, //esto debo setearlo manualmente porque el modelo tiene como idatribute personalizado ()
                    success:function(e,params){
                            
                                if(params.numerror!=0)
                                {
                                    win.alert("Detalle: "+params.descerror+". Consulte con el administrador","Error al crear",4)
                                    if(that.error!=null){
                                        that.error();
                                    }
                                }
                                else
                                {that.options.modelo.set(params.data)
                                    if(that.success!=null){
                                        that.success();
                                    }
                                    
                                }
                            }
                    })//save
                    
                }
    
            }//validar
                        
            return false;
        }
});//addelement

var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/entidades/servicios/listener',   
    idAttribute:'id_servicio',
    defaults:{
        id_servicio:0,
        servicio:'',
        habilitado:true,
        cadeteria:0,
        precio_venta:0.00,

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
        for(c in patrones)
        {
            if(patrones[c]!="")
            {
                if(cond!="")
                {
                    cond+=" and "+c+"='"+patrones[c]+"'";
                }
            }
        }        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }        
        ofwlocal.getAsync("servicios",Array("*"),cond,"servicio asc",function(rs){ that.cargar(rs,that) } )
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
         $('#data-table').DataTable({responsive: true,searching:false,pageLength:10}); 
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {
        var id_model=(e.target.id).replace("view-","");
        var res=oColecciones.where({"id_servicio":id_model})
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
        cCampo=new Campo({valor:modelo.get('id_servicio'),nombre:'id_servicio',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('servicio'),nombre:'servicio',tipo:'text',etiqueta:'Servicio',esdescriptivo:true,obligatorio:true});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('precio_venta'),nombre:'precio_venta',tipo:'money',etiqueta:'Precio de venta',obligatorio:true});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('cadeteria'),nombre:'cadeteria',tipo:'checkbox',etiqueta:'¿Es un servicio de cadeteria?'});
        oCampos.add(cCampo); 

        cCampo=new Campo({valor:modelo.get('habilitado'),nombre:'habilitado',tipo:'checkbox',etiqueta:'Habilitado para vender'});
        oCampos.add(cCampo); 

        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    //poner siempre los eventos para no traer efectos no deseados
        }
        
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'EVD',base_url:'<?=base_url()?>',el:$("#row-body")});
                      
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
                    if(e['servicio']=="")
                    {
                        strhtml+="<li>Debe ingresar un nombre para el servicio</li>"
                    }
                    if(e['precio_venta']=="" || !es_numero(e['precio_venta']))
                    {
                        strhtml+="<li>El valor ingresado para el precio de venta, es incorrecto o es vacio</li>"
                    }
                    if(e['servicio']!="" && e['modo']=='A')
                    {
                        var rs=ofwlocal.get('servicios',Array('servicio'),'servicio="'+e['servicio']+'"','');
                        if(rs.length>0)
                        {   
                             strhtml+="<li>Parece que el servicio ya existe: '"+rs[0].servicio+"'</li>"
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
            var patrones={id_servicio:$("#patronid").val(),servicio:$("#patrondescripcion").val(),cadeteria:($("#patroncadeteria").is(":checked"))?1:0}    
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
            var patrones={id_servicio:$("#patronid").val(),servicio:$("#patrondescripcion").val(),cadeteria:($("#patroncadeteria").is(":checked"))?1:0}    
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
}//inicializacion contexto

function buscar(evt)
{
    evt.preventDefault();
var patrones={id_servicio:$("#patronid").val(),servicio:$("#patrondescripcion").val(),cadeteria:($("#patroncadeteria").is(":checked"))?1:0}    
olista.render(patrones);
}

function realizaralta()
{   
    var newModel=new Elemento()
    olista.mostrar(newModel,'A')

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->