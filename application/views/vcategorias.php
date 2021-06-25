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
                                <th>ABREVIACION</th>                                        
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(categoria) {  %>
    <tr>
        <td><%=categoria.get('id_categoria') %> </td>
        <td><a href="#/edit/<%=categoria.get('id_categoria')%>" id="elemento-<%=categoria.get('id_categoria')%>" data-type="text" data-pk="1" data-title="Ingrese el dato"><%=categoria.get('categoria') %></a></td>
        <td><a href="#/editabreviacion/<%=categoria.get('id_categoria')%>" id="abreviacion-<%=categoria.get('id_categoria')%>" data-type="text" data-pk="1" data-title="Ingrese el abreviacion"><%=categoria.get('abreviacion') %></a></td>
        <td><button type="button" class="btn btn-danger" id="remove-<%=categoria.get('id_categoria')%>">Eliminar</button>
        </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<script type="text/template" id="tpl-new">
    <form class="form-inline" action="/" method="POST">
        <div class="form-group m-r-12">
            <input type="text" class="form-control" id="categoria" name="categoria" placeholder="Ingrese categoria">
        </div>        
        <div class="form-group m-r-12">
            <input type="text" class="form-control" id="abreviacion" name="abreviacion" placeholder="Ingrese abreviacion">
        </div>        
        <button type="submit" class="btn btn-sm btn-primary m-r-5">Crear</button>                      
        <button type="button" class="btn btn-sm btn-danger m-r-5" id="cancelaAdd">Cancelar</button>                      
    </form>
</script>

<input type="hidden" id="id_empresa" value="<?=$id_empresa?>">
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
                        <h4 class="panel-title">Categorias de productos</h4> 
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
 App.setPageTitle('categorias | Coffee APP');
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
    $.fn.editable.defaults.url = '<?=base_url()?>index.php/categorias/update';
    

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
                return 'Debe ingresar una abreviacion';
            }

            if($.trim(value)!=='')
            {
                var id_categoria=$(this).attr("id").replace("abreviacion-","");
                 rs=ofwlocal.get("categorias",Array("id_categoria"),"id_categoria<>"+id_categoria+" and abreviacion='"+$.trim(value)+"' and id_empresa="+$("#id_empresa").val(),"")    
        
                if(rs.length>0)
                {
                 return "La abreviacion ya esta asignada a otra categoria"
                }
                
            }

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
        //win.alert("Hubo un problema: "+ res.descerror,"Error",4)
        swal("Error al guardar!",res.descerror,"error")
        }
                
    } else {
        //win.alert("Hubo un problema","Error",4)
        swal("Error!","Consulte con el administrador","error")
    } 
});
    
};


var categorials=null;
var categoriaAdd=null;
var editcategoria=null;
var router=null;
var olista =null;
var oColecciones=null;
var categorias=Backbone.Collection.extend({
    url:'<?=base_url()?>index.php/categorias/listar'
});

var Categoria=Backbone.Model.extend({
urlRoot:'<?=base_url()?>index.php/categorias/'
});//Categoriamodel
var CategoriaList=Backbone.View.extend(
{ el:$('#panel-body'),
    render:function()
    {
        var that=this;
        oColecciones=new categorias();
        oColecciones.fetch({
            success:function(elemcategorias)
            {olista=elemcategorias.models 
                
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
    categoriaAdd=new addElement();        
    categoriaAdd.render();
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
});//categorialist


function remove(mRemove)
{
spinnerStart($('#panel-body'));
mRemove.url='<?=base_url()?>index.php/categorias/listener/'+mRemove.get('id_categoria');
mRemove.destroy({success:function (e,params){                        
                        spinnerEnd($('#panel-body'));
                        if(params.numerror==0)
                        {
                        oColecciones.remove(mRemove);
                        categorials.render();    
                        }else{
                            //win.alert("Error: "+params.descerror,"No se pudo eliminar",4);
                             swal("Error al eliminar","Detalle: "+params.descerror+". Consulte con el administrador","error")
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
                    //win.alert("Debe completar el campo","Advertencia",3)
                    swal("Atención","Debe completar el campo","error")
                    return false
                }
            }
            spinnerStart($('#panel-body'));
            var categoria=new  Categoria();
            categoria.url='<?=base_url()?>index.php/categorias/listener'
            
            categoria.save(detailsInputs,{
                success:function(e,params){
                    if(params.numerror!=0)
                    {
                        //win.alert("Detalle: "+params.descerror+". Consulte con el administrador","Error al crear",4)
                        swal("Error al crear","Detalle: "+params.descerror+". Consulte con el administrador","error")
                    }
                    spinnerEnd($('#panel-body'));
                     categorials.render();
                    }               
            })
            
            return false;
        },//add
        cancelaAdd:function()
        {
            categorials.render();
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

categorials=new CategoriaList();
router=new Router();
router.on('route:home',function(){
    categorials.render();    
})

router.on('route:editar',function(){    
    editElement.render();
})//editar elemento

Backbone.history.start();
}//inicializacion contexto

</script>
<!-- ================== END PAGE LEVEL JS ================== -->