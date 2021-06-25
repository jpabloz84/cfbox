<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$selfurl=str_replace("panel", $_SERVER['PATH_INFO'], $_SERVER['HTTP_REFERER']);
$selfurl=str_replace(".php//", ".php/", $selfurl);


?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/address/address.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/typeaheadjs/lib/typeahead.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-datetimepicker/css/datetimepicker.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/select2/select2.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-wysihtml5/src/bootstrap-wysihtml5.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/javascript" src="<?php echo BASE_FW; ?>js/underscore-min.js"></script>
<script type="text/javascript" src="<?php echo BASE_FW; ?>js/backbone-min.js"></script>

<script type="text/template" id="tpl-table-list"> 
<p>
<button type="button" class="btn btn-primary" id="newelement">NUEVO REGISTRO</button> 
<span class="m-r-5">Para editar, haga click en los link's</span> 
</p>
<table class="table table-striped table-bordered nowrap" style="font-size:10px;" width="100%" id="data-table" >
                        <thead>
                            <tr>
                                <th>#</th>                                        
                                <th>NOMBRE</th>                                        
                                <th>TIPO COMP.</th>                                        
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(condicion) {  %>
    <tr>
        <td><%=condicion.get('id_cond_iva') %> </td>
        <td><a href="#/edit/<%=condicion.get('id_cond_iva')%>" id="elemento-<%=condicion.get('id_cond_iva')%>" data-type="text" data-pk="1" data-title="Ingrese el dato"><%=condicion.get('condicion') %></a></td>
        <td><a href="#/editabreviacion/<%=condicion.get('id_cond_iva')%>" id="abreviacion-<%=condicion.get('id_cond_iva')%>" data-type="text" data-pk="1" data-title="Ingrese letra tipo comprobante"><%=condicion.get('comp_tipo') %></a></td>
        <td><button type="button" class="btn btn-danger" id="remove-<%=condicion.get('id_cond_iva')%>">Eliminar</button>
        </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<script type="text/template" id="tpl-new">
    <form class="form-inline" action="/" method="POST">
        <div class="form-group m-r-12">
            <input type="text" class="form-control" id="condicion" name="condicion" placeholder="Ingrese condicion">
        </div>        
        <div class="form-group m-r-12">
            <input type="text" class="form-control" id="comp_tipo" name="comp_tipo" placeholder="Ingrese letra de tipo de comprobante">
        </div>        
        <button type="submit" class="btn btn-sm btn-primary m-r-5">Crear</button>                      
        <button type="button" class="btn btn-sm btn-danger m-r-5" id="cancelaAdd">Cancelar</button>                      
    </form>
</script>


<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-12 -->
        <div class="col-md-12" id="data-table-wrapper">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>                        
                        </div>
                        <h4 class="panel-title">Condiciones ante IVA</h4> 
             </div>             
            <div class="panel-body" id="panel-body">                    
            </div>
           </div>
        </div>
        <!-- end col-12 -->
    </div>
   <!-- end row -->

</div>
<!-- end #content -->
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];


App.restartGlobalFunction();
var base_url='<?=base_url()?>index.php/'

var win= new fwmodal();
 App.setPageTitle('Condiciones ante IVA | Coffee APP');
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/js/bootstrap-editable.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/js/bootstrap-editable.min.js').done(function() {
                    $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/address/address.js').done(function() {
                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/typeaheadjs/lib/typeahead.js').done(function() {
                            $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/typeaheadjs/typeaheadjs.js').done(function() {
                                $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-wysihtml5/wysihtml5.js').done(function() {
                                    $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-wysihtml5/lib/js/wysihtml5-0.3.0.js').done(function() {
                                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-wysihtml5/src/bootstrap-wysihtml5.js').done(function() {
                                            $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-datepicker/js/bootstrap-datepicker.js').done(function() {
                                                $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js').done(function() {
                                                    $.getScript('<?=BASE_FW?>assets/plugins/bootstrap3-editable/inputs-ext/select2/select2.min.js').done(function() {
                                                        $.getScript('<?=BASE_FW?>assets/plugins/mockjax/jquery.mockjax.js').done(function() {
                                                            $.getScript('<?=BASE_FW?>assets/plugins/moment/moment.min.js').done(function() {         
                                                                init();
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
});


var handleEditableFieldConstruct = function() {
    var c ='popup' //'inline'  //window.location.href.match(/c=inline/i) ? 'inline' : 'popup';
    if (c == 'inline') {
        $('[data-editable]').removeClass('active');
        $('[data-editable="inline"]').addClass('active');
    }
    $.fn.editable.defaults.mode = c === 'inline' ? 'inline' : 'popup';
    $.fn.editable.defaults.inputclass = 'form-control input-sm';
    $.fn.editable.defaults.url = '<?=base_url()?>index.php/condiciones/update';
    

$("[id^='elemento-']").each(function(e)
{
   $(this).editable({
        validate: function(value) {
            if($.trim(value) === '') { 
                return 'Debe ingresar un valor';
            }
        }
    });
});
   
$("[id^='abreviacion-']").each(function(e)
{ 
    
   $(this).editable({
        validate: function(value) {
                    
            if($.trim(value) === '') { 
                return 'Debe ingresar una letra de facturacion (A,B,C)';
            }

            /*if($.trim(value)!=='')
            {
                var id_cond_iva=$(this).attr("id").replace("abreviacion-","");
                 rs=ofwlocal.get("cond_iva",Array("id_cond_iva"),"id_cond_iva<>"+id_cond_iva+" and comp_tipo='"+$.trim(value)+"'","")    
        
                if(rs.length>0)
                {
                 return "La abreviacion ya esta asignada a otra categoria"
                }
                
            }*/

        }
    });
});


$("[id^='elemento-']").on('save', function(e, params) {
    //assuming server response: '{success: true}'    
    var pk = $(this).data('editableContainer').options.pk;    
    if(params.response) {
        var res=JSON.parse(params.response)
        if(res.numerror!=0)
        {
        win.alert("Hubo un problema: "+ res.descerror,"Error",4)
        }
                
    } else {
        win.alert("Hubo un problema","Error",4)
    } 
});
    
};


var condicionesls=null;
var condicionAdd=null;
var editcategoria=null;
var router=null;
var olista =null;
var oColecciones=null;
var Condiciones=Backbone.Collection.extend({
    url:'<?=base_url()?>index.php/condiciones/listar'
});

var Condicion=Backbone.Model.extend({
urlRoot:'<?=base_url()?>index.php/condiciones/'
});//Categoriamodel
var CondicionesList=Backbone.View.extend(
{ el:$('#panel-body'),
    render:function()
    {
        var that=this;
        oColecciones=new Condiciones();
        oColecciones.fetch({
            success:function(elemcondiciones)
            {olista=elemcondiciones.models 
                
                //var tpl=_.template($('#tpl-table-list').html(),{ls:olista}); --no anda
                var tpl=_.template($('#tpl-table-list').html());                
                that.$el.html(tpl({ls:olista}));
                handleEditableFieldConstruct();
            }//success
        })
    },//render
    events:{
            'click #newelement':'nuevo',
            'click .btn.btn-danger':'eliminar'
    },
    nuevo:function()
    {
    condicionAdd=new addElement();        
    condicionAdd.render();
    },
    eliminar:function(e)
    {
        var id_model=(e.target.id).replace("remove-","");
        var mRemove=null;
        mRemove=oColecciones.get(id_model);        
        //console.log('cantidad antes de eliminar : ' + oColecciones.length);
        win.dialog('¿Usted está seguro que desea eliminar '+mRemove.get('nombre')+'?',' Atención',3,remove,mRemove
        );        
    }
});//condicioneslist


function remove(mRemove)
{
spinnerStart($('#panel-body'));
mRemove.url='<?=base_url()?>index.php/condiciones/listener/'+mRemove.get('id_cond_iva');
mRemove.destroy({success:function (e,params){                        
                        spinnerEnd($('#panel-body'));
                        if(params.numerror==0)
                        {
                        oColecciones.remove(mRemove);
                        condicionesls.render();    
                        }else{
                            win.alert("Error: "+params.descerror,"No se pudo eliminar",4);
                        }                        
                         //console.log('cantidad luego de eliminar : ' + oColecciones.length);
                }
                ,wait: true
            });

        
}
var  editElement=Backbone.View.extend({
        el:$('#panel-body'),
        render:function()
        {
        var tpl=_.template($('#edit-data').html());
        this.$el.html(tpl({}));
        }//render
    });//editcategoria

var addElement=Backbone.View.extend({
        el:$('#panel-body'),
        render:function()
        {
            
        var tpl=_.template($('#tpl-new').html());
        this.$el.html(tpl({}));
        },//render
        events:{
            'submit .form-inline':'createElement',
            'click #cancelaAdd':'cancelaAdd'
        },
        createElement:function(ev){
            var detailsInputs=$(ev.currentTarget).serializeObject();
            for (e in detailsInputs)
            {
                if(detailsInputs[e]=="")
                {
                    win.alert("Debe completar el campo","Advertencia",3)
                    return false
                }
            }
            spinnerStart($('#panel-body'));
            var condicion=new  Condicion();
            condicion.url='<?=base_url()?>index.php/condiciones/listener'            
            condicion.save(detailsInputs,{
                success:function(e,params){
                    if(params.numerror!=0)
                    {
                        win.alert("Detalle: "+params.descerror+". Consulte con el administrador","Error al crear",4)
                    }
                    spinnerEnd($('#panel-body'));
                     condicionesls.render();
                    }               
            })
            
            return false;
        },//add
        cancelaAdd:function()
        {
            condicionesls.render();
        }
    });//nuevoelemento


var Router=Backbone.Router.extend({
    routes:{
    '<?=$selfurl?>':'home','add':'agregar'
    },
    home:function(url)
    {
     //console.log("url "+url)   
    }    
    ,
    editarCategoria:function(id)
    {
        console.log("editando "+id)
    },
    nuevo:function(url)
    {
        
    },//nuevo
    agregar:function()
    {

    }//agregar
    });//routes

function init()
{

condicionesls=new CondicionesList();
router=new Router();
router.on('route:home',function(){
    condicionesls.render();    
})

router.on('route:editar',function(){    
    editElement.render();
})//editar elemento

Backbone.history.start();
}//inicializacion contexto

</script>
<!-- ================== END PAGE LEVEL JS ================== -->