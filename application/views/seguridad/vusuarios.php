<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- ================== END PAGE LEVEL STYLE ================== -->
<style type="text/css">
    .popover{
    width: auto !important;
    max-width: 100% !important;
 }
</style>
<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-wrapper">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>                        
                        </div>
                        <h4 class="panel-title">Gestión de Usuarios</h4>                    </div>             
            <div class="panel-body" id="panel-body">
                            <table class="table table-striped table-bordered nowrap" style="font-size:10px;" width="100%" id="data-table-usuario" >
                                <thead>
                                    <tr>
                                        <th>USUARIO</th>                                        
                                        <th>NOMBRES</th>
                                        <th>ROL</th>
                                        <th>VER</th>                                        
                                    </tr>
                                </thead>
                                <tbody>                                    
                                </tbody>
                            </table>
            </div>
           </div>
        </div>
        <!-- end col-12 -->
<!-- observaciones --> 

    </div>
    <!-- end row -->
<div class="row">  
<!-- begin panel -->
            <div class="panel panel-inverse" data-sortable-id="ui-general-5">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <?php if($visitante->permitir('prt_usuarios',2)){?>
                             <a href="javascript:alta();" class="btn btn-xs btn-primary" >alta &nbsp;<i class="fa fa-user"></i></a>
                        <?php }?>
                        <?php if($visitante->permitir('prt_usuarios',4)){?>
                             <a href="javascript:modificar();" class="btn btn-xs btn-primary" >modificar &nbsp;<i class="fa fa-edit"></i></a>
                        <?php }?>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    </div>
                    <h4 class="panel-title" id="usuario_desc">SELECCIONE UN USUARIO</h4>
                </div>
                <div class="panel-body">                                        
                    <div class="row">
                        <div class="col-md-3">
                            <label>Usuario (origen: <span id="txt_origen"></span>)</label>                 
                            <p id="txt_usuario"></p>
                        </div>
                        <div class="col-md-3">
                            <label>Apellido y Nombres</label>                            
                            <p id="txt_apenom"></p>
                        </div>
                        <div class="col-md-3">
                            <label>Rol</label>                            
                            <p id="txt_rol"></p>
                        </div>
                        <div class="col-md-3">
                            <label>Estado</label>                            
                            <p id="txt_estado"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Telefono</label>                            
                            <p id="txt_telefono"></p>
                        </div>
                        <div class="col-md-3">
                            <label>Domicilio</label>
                            <p id="txt_domicilio"></p>
                        </div>
                        <div class="col-md-3">
                            <label>Email</label>                            
                            <p id="txt_email"></p>
                        </div>
                        <div class="col-md-3">
                            <label>Documento</label>                            
                            <p id="txt_documento"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Clave</label>                            
                            <div class="controls">
                            <input  data-toggle="password" data-placement="after" type="password" name="txt_clave" id="txt_clave"  class="form-control"/>
                            </div>
                        </div>                  
                        <div class="col-md-6">
                           <div class="media media-sm">
                                 
                                    <img src="<?=BASE_FW?>assets/img/default-large.jpg" alt="Imagen de perfil" class="media-object" id="txt_imgprofile"  data-toggle="popover" data-img="<?=BASE_FW?>assets/img/default-large.jpg"  title="imagen de perfil" />

                            </div>                           
                        </div>
                        <div class="col-md-2">   
                         <?php if($visitante->permitir('prt_usuarios',8)){?>                         
                            <button  id="btn-eliminar" type="button" class="btn btn-xs btn-danger m-r-5" onclick="Eliminar()" data-loading-text="<i class='fa fa-spinner fa-spin '></i> eliminando...">Eliminar
                                <i class="fa fa-trash-o"></i>
                            </button>
                             <?php }?>
                            <input type="hidden" id="id_usuario" value="">
                        </div>                        
                    </div>
                </div>
            </div>
            <!-- end panel -->
</div>            
<!-- end #row -->
  
<div class="modal" id="modal-without-animation">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="cancel-puntaje-cruz">×</button>
                                    <h5 class="modal-title" id="ver-usuario-titulo"></h5>
                                </div>
                                <div class="modal-body" >
                                    <form class="form-horizontal form-bordered" name="demo-form">
                                        <div class="form-group">
                                            <label class="col-md-12 control-label" style="text-align:center" id="ver-usuario-mensaje"></label>
                                        </div>
                                    </form>
                                    
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal" id="cancel-puntaje">OK</a>                                    
                                </div>
                            </div>
                        </div>
                    </div>
</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>


App.restartGlobalFunction();
App.setPageTitle('Consulta de usuarios | Coffee APP');
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;
    
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-show-password/bootstrap-show-password.js').done(function(){
                        TableManageResponsive.init();
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            gettableinfo()
                         })
                    })
                });
            });
        });
    });


function inicializacion_contexto()
{


gettableinfo();
reinicializar_variables()

 $('[data-toggle="popover"]').popover({          
  //trigger: 'focus',
  trigger: 'hover',
  html: true,          
  content: function () {   
    
    var src=$(this).attr("src") 
                                   
  if(src!=""){
        return '<img class="img-fluid" src="'+$(this).attr("src") + '" />';
  }else{
    return '<p>sin imagen para mostrar</p>';
  }
  },
  title: 'Imagen de perfil'
})

}//inicializacion contexto
function setdetails(id_usuario)
{

    var part=$("#desc_"+id_usuario).val()
    var text="ID USUARIO "+id_usuario+": "+part
    $("#usuario_desc").html(text.toUpperCase())
    $("#id_usuario").val(id_usuario)
    mostrar()
}


function mostrar()
{
clearinfo()
var id_usuario=$("#id_usuario").val()
spinnerStart($('[data-sortable-id="ui-general-5"]'))

<?php  //si muestra o no clave
if($visitante->permitir('prt_usuarios',16)){?>
var cmp=Array('usuario','clave','rol','habilitado','id_persona','apellido','nombres','domicilio','telefono','car_tel','nro_tel','nro_docu','documento','sexo','img_personal','email','descripcion_loc','descripcion_pro','origen')
<?php }else{?>
    var cmp=Array('usuario'," 'xxxxxxxx' as clave",'rol','habilitado','id_persona','apellido','nombres','domicilio','telefono','car_tel','nro_tel','nro_docu','documento','sexo','img_personal','email','descripcion_loc','descripcion_pro','origen')
<?php }?>

ofwlocal.getAsync('verusuarios',cmp,"id_usuario="+id_usuario,'',cargarinfo);  


}//mostrar

function clearinfo()
{
    $("#txt_clave").val("")
    $("#txt_imgprofile").attr("src","<?=base_url()?>assets/img/default-large.jpg")
    $("#txt_documento").html("")
    $("#txt_email").html("")
    $("#txt_domicilio").html("")
    $("#txt_email").html("")
    $("#txt_telefono").html("")
    $("#txt_estado").html("")
    $("#txt_rol").html("")
    $("#txt_apenom").html("")
    $("#txt_usuario").html("")

}

function cargarinfo(res)
{

for(i in res)    
{
    $("#txt_clave").val(res[i].clave)
    if(res[i].img_personal!="" && res[i].img_personal!=null)
    {   var srcimg="<?=base_url()?>"+res[i].img_personal
        $("#txt_imgprofile").attr("src",srcimg)
        $("#txt_imgprofile").attr("data-img",srcimg)
    
    }    
    $("#txt_documento").html(res[i].documento+' '+res[i].nro_docu)
    $("#txt_email").html(res[i].email)
    $("#txt_domicilio").html(res[i].domicilio)
    $("#txt_email").html(res[i].email)
    $("#txt_telefono").html(res[i].telefono)
    $("#txt_estado").html((res[i].habilitado==1)?"habilitado":"No habilitado")
    $("#txt_rol").html(res[i].rol)
    $("#txt_apenom").html(res[i].apellido+', '+res[i].nombres)
    $("#txt_usuario").html(res[i].usuario)
    $("#txt_origen").html(res[i].origen)

  
}
spinnerEnd($("[data-sortable-id='ui-general-5']"))
}

<?php if($visitante->permitir('prt_usuarios',2)){?>
function alta()
{
handleLoadPage("#<?php echo base_url(); ?>index.php/seguridad/usuarios/ingresar_alta")    
}
<?php } ?>

<?php if($visitante->permitir('prt_usuarios',4)){?>
function modificar()
{
    if($("#id_usuario").val()!="" && $("#id_usuario").val()!=0)
    {     
    handleLoadPage("#<?php echo base_url(); ?>index.php/seguridad/usuarios/modificar/"+$("#id_usuario").val())            
    }
}

<?php } ?>


function reinicializar_variables()
{

return
}
function gettableinfo()
{
var myTable=$('#data-table-usuario').DataTable()
    myTable.destroy();
    myTable=$('#data-table-usuario').DataTable( {
        "processing": true,
        "serverSide": false,
        responsive: true,
        "ajax": "<?=base_url()?>index.php/seguridad/usuarios/obtener_usuarios/",
        "columns": [{ "data": "USUARIO" },{ "data": "NOMBRES" },{ "data": "ROL" },{ "data": "VER" }]
    } );//fin de ajax datatable

}

<?php if($visitante->permitir('prt_usuarios',8)){?>
function Eliminar(){
var txtusuario=$("#id_usuario").val()+" - "+$("#txt_usuario").html()
  swal({
                  title: "Atención",
                  text: "¿Desea eliminar el usuario "+txtusuario+"?",
                  type: "info",
                  closeOnConfirm: false,
                 showLoaderOnConfirm: true         
            }, function () {
                var id_usuario=$("#id_usuario").val()
                 $("#btn-eliminar").button("loading")
                      $.ajax({dataType: "json",type: 'POST',url:'<?php base_url()?>seguridad/usuarios/eliminar',data: { id_usuario: id_usuario },success: function(json)
                            {   
                                var numError=json.numerror;
                                var descError=json.descerror;                                
                                if(numError != 0)
                                {   
                                    var mensaje="Puede que no se haya eliminado: "+ descError
                                    console.log(mensaje)
                                    swal("Atención",mensaje,"error")
                                }else{
                                   swal("eliminado","","success")
                                   clearinfo()
                                   gettableinfo()
                                }
                                
                            },
                            beforeSend: function(){
                            
                                },
                            complete: function(){
                                    $("#btn-eliminar").button("reset")
                                }
                           })
            })




}
<?php } ?>

 
</script>
<!-- ================== END PAGE LEVEL JS ================== -->