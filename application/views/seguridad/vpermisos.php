<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

		
<!-- begin #content -->
<div id="content" class="content">
    <!-- begin row -->
    <div id="data-table_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
    <div class="row">
        <div class="col-sm-12">
            <div class="dataTables_length" id="data-table_length">
                <label>Perfiles disponibles 
                    <select name="data-table_length" aria-controls="data-table" class="form-control input-sm" style="width:200px" id="rol">
                        <?php
                        $entro=false;
                        foreach ($roles as $rol) {
                        	$id_rol=$rol['id_rol'];
                            $rol=$rol['rol'];
                            
                            if(!$entro){
                                echo "<option value='$id_rol' selected='selected'>$rol</option>";
                            }
                            else
                            { echo "<option value='$id_rol'>$rol</option>";
                            }

                            $entro=true;
                        }                         
                        ?>
                        
                    </select> </label>
                    
            </div>
            <div class="dataTables_length" >
                <a href="javascript:;" class="btn btn-sm btn-inverse" data-click="save-practice"><i class="fa fa-check"></i> Guardar</a>
            </div>
        </div>
    </div>        
    <div class="row">
        <!-- begin col-5 -->
        <div class="col-md-5">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                    </div>
                    <h4 class="panel-title">Modulos</h4>
                </div>
                <div class="panel-body">
                    <table id="data-table-modulo" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>&nbsp</th>
                                <th>Modulo</th>                                
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-5 -->
        <!-- begin col-5 -->
        <div class="col-md-5">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                        
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                    </div>
                    <h4 class="panel-title">Permisos</h4>
                </div>
                <div class="panel-body">
                    <table id="data-table-permiso" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nro. Permiso</th>
                                <th>Bit</th>
                                <th>Permiso</th>
                                <th>&nbsp</th>                                
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-5 -->
    </div>
    <!-- end row -->
</div>
</div>
<!-- end #content -->
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
    App.restartGlobalFunction();
    App.setPageTitle('Coffee | Permisos');
//segun el rol, obtengo los permisos
var arrPermisos={}    
	$.getScript('<?php echo BASE_FW; ?>assets/plugins/DataTables/media/js/jquery.dataTables.js').done(function() {
        $.getScript('<?php echo BASE_FW; ?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?php echo BASE_FW; ?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {                
                 var id_rol= $("#rol").val()
                 refresh_rol_data(id_rol)         
                 $("#rol").change(function() {
                        id_rol=$("#rol").val()
                    refresh_rol_data(id_rol)
                });
                 $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/seguridad/permisos/");
                })
            });
        });
    });

    

    


function dibujar_row_data(id_rol)
{
    var arrFilas=arrPermisos[id_rol]
    var tblrow_mod=""
    var nro_permiso_modulo=""
    $("#page-loader").modal("show")            
    //mostrarBloqueo("#data-table_wrapper")
    for (k=0;k<arrFilas.length;k++)
    {strChecked=''
        var arr1=arrFilas[k]
        var strChecked=''
        if(nro_permiso_modulo!=arr1['nro_permiso_modulo'])
        {
            //checkeo el primermodulo por defecto
            if(nro_permiso_modulo=='')
            {
                strChecked="checked='checked'"
                dibujar_permisos(id_rol,arr1['nro_permiso_modulo'])
            }          
         nro_permiso_modulo=arr1['nro_permiso_modulo']   
         tblrow_mod+="<tr><td><input type='radio' id='radioMod"+nro_permiso_modulo+"' name='radioMod' onclick='dibujar_permisos("+id_rol+","+nro_permiso_modulo+")' "+strChecked+"/></td><td>"+arr1['permiso_modulo']+"</td></tr>"  
        }
        
    }
    
    $("#data-table-modulo >tbody").html(tblrow_mod)
    $("#page-loader").modal("hide")            
    //ocultarBloqueo("#data-table_wrapper")
    //$("#page-loader").hide()
}

function dibujar_permisos(id_rol,nro_permiso_modulo)
{ var tblrow_mod=''

  var arrFilas=arrPermisos[id_rol];
  for (i=0;i<arrFilas.length;i++)
  {
        var arr1=arrFilas[i]
        
        if(nro_permiso_modulo==arr1['nro_permiso_modulo'])
        {
        strChecked=(arr1['checked'])?"checked='checked'":""
        tblrow_mod+="<tr><td>"+arr1['nro_permiso']+"</td><td>"+arr1['bit']+"</td><td id='desc"+nro_permiso_modulo+"_"+arr1['nro_permiso']+"'>"+arr1['permiso_desc']+" </td><td><input type='checkbox' id='chkModid_"+id_rol+"_"+nro_permiso_modulo+"_"+arr1['nro_permiso']+"' name='chkMod'  "+strChecked+" /></td></tr>"           

        }
        
    }
    $("#data-table-permiso >tbody").html(tblrow_mod)
}


function refresh_rol_data(id_rol)
{ 
    if(typeof id_rol=="undefined")
    {
        return
    }

//carga los permisos del servidor siempre y cuando no este cacheado
if(typeof arrPermisos[id_rol]=="undefined")
{
    arrPermisos[id_rol]={}

    $.ajax({dataType: "xml",type: 'POST',url:'<?=base_url()?>index.php/seguridad/permisos/getpermisos/'+id_rol,
        success: function( xmlResponse )
        {
            var $xml = $(xmlResponse)             
            var numError=$xml.find('numError').text();
            var descError=$xml.find('descError').text();
            var sumbit=1    
            var strxml=$xml.find('Data').text()
            var nro_permiso_mod=''
            if(numError==0)
            { var arrRow=new Array()
                $xmlRows=$(strxml)
                var i=0
                 $xmlRows.find('row').each(function()
                 {
                    
                    var nro_permiso=$(this).find("nro_permiso").text()
                    var nro_permiso_modulo=$(this).find("nro_permiso_modulo").text()
                    var permiso_modulo=$(this).find("permiso_modulo").text()
                    var permiso_desc=$(this).find("permiso_desc").text()
                    var permiso=$(this).find("permiso").text()
                    var id_rol=$(this).find("id_rol").text()                    
                    sumbit+=sumbit
                    if(nro_permiso_modulo!=nro_permiso_mod)
                    {
                        nro_permiso_mod=nro_permiso_modulo
                        sumbit=1
                    }
                    
                    arrRow[i]={}
                    arrRow[i]['id_rol']=id_rol
                    arrRow[i]['nro_permiso']=nro_permiso
                    arrRow[i]['nro_permiso_modulo']=nro_permiso_modulo
                    arrRow[i]['permiso_modulo']=permiso_modulo
                    arrRow[i]['permiso_desc']=permiso_desc
                    arrRow[i]['permiso']=parseInt(permiso)
                    arrRow[i]['bit']=sumbit
                    arrRow[i]['checked']=((permiso & sumbit)>0)?true:false
                    i++
                    
                 })
                 arrPermisos[id_rol]=arrRow
                 dibujar_row_data(id_rol);                      
            }else
            {
                alert(descError)
            }
        }
       })
}else{
    dibujar_row_data(id_rol);
}

}
$(function () {
$("#data-table-permiso  td:nth-child(3)").live("dblclick",function(){
    
    var currentEle = $(this);
    var strId=this.id;
    var value = $(this).html();
    if(value.indexOf("input type")>=0)
    {
        return
    }
    var strinput="<input type='text' value='"+value+"' /> "
    $(this).html(strinput)
    $("#"+strId+" input").keypress(function(event){
    
          if ( event.which == 13 ) {

            var arr=(strId.replace("desc","") ).split("_")
            var nro_permiso_modulo= arr[0]
            var nro_permiso= arr[1]
            var id_rol=$("#rol").val()
            var arrPer=arrPermisos[id_rol]
            for (j=0;j<arrPer.length;j++)
            {
                if(arrPer[j]['nro_permiso']==nro_permiso && arrPer[j]['nro_permiso_modulo']==nro_permiso_modulo)
                {
                    arrPer[j]['permiso_desc']=$(this).val()
                    arrPermisos[id_rol]=arrPer
                    $("#"+strId).html($(this).val())
                    break;
                }   
            }
            }
       
    })
});
});


var confirmAction=false

$(document).on('click', '[data-click="save-practice"]', function() {            
                                    
        var targetModalHtml = ''+
    '<div class="modal fade" data-modal-id="reset-local-storage-confirmation">'+
    '    <div class="modal-dialog">'+
    '        <div class="modal-content">'+
    '            <div class="modal-header">'+
    '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
    '                <h4 class="modal-title"><i class="fa fa-refresh m-r-5"></i> ATENCION</h4>'+
    '            </div>'+
    '            <div class="modal-body">'+
    '                <div class="alert alert-info m-b-0">¿Desea realmente guardar estos cambios?</div>'+
    '            </div>'+
    '            <div class="modal-footer">'+        
    '                <a href="javascript:;" class="btn btn-sm btn-inverse" data-click="confirm-action"><i class="fa fa-check"></i> OK</a><a href="javascript:;" class="btn btn-sm btn-inverse" data-click="cancel-action"><i class="fa fa-check"></i> CANCELAR</a>'+
    '            </div>'+
    '        </div>'+
    '    </div>'+
    '</div>';
    
    $('body').append(targetModalHtml);
    $('[data-modal-id="reset-local-storage-confirmation"]').modal('show');

    $(document).on('hidden.bs.modal', '[data-modal-id="reset-local-storage-confirmation"]', function(e) {
        $('[data-modal-id="reset-local-storage-confirmation"]').remove();
    });

    $(document).on('click', '[data-click=confirm-action]', function(e) {
/*
        if(!permitir(prt_permisos,2))
        {
            alert("Usted no tiene permiso para realizar esta accion")
            return
        }*/

        if(!confirmAction)
        {confirmAction=true
                var strXml='<\?xml version="1.0" \?><body><resultados>'        
                for (var id_rol in arrPermisos)
                {var r=arrPermisos[id_rol];
                var nro_permiso_modulo=''

                var strXmlMod='<rol id_rol="'+id_rol+'">'
                    for(j=0;j<r.length;j++)
                    {
                        if(nro_permiso_modulo!=r[j]['nro_permiso_modulo'])
                        {   if(nro_permiso_modulo!='')
                            {
                            strXmlMod+="</permiso_modulo>"    
                            }

                            var sumbit=0
                            nro_permiso_modulo=r[j]['nro_permiso_modulo']
                            for (k=0;k<r.length;k++)
                            {
                                if(r[k]['nro_permiso_modulo']==nro_permiso_modulo && r[k]['checked'] )
                                {
                                    sumbit+=parseInt(r[k]['bit'])
                                }
                            }
                            strXmlMod+="<permiso_modulo suma='"+sumbit+"' nro_permiso_modulo='"+nro_permiso_modulo+"'>"
                        }
                        if(nro_permiso_modulo==r[j]['nro_permiso_modulo'])
                        {
                         strXmlMod+="<permiso nro_permiso='"+r[j]['nro_permiso']+"'>"+r[j]['permiso_desc']+"</permiso>"   
                        }
                        
                    }
                    strXmlMod+='</permiso_modulo>'
                strXmlMod+='</rol>'
                }
                strXml+=strXmlMod+'</resultados></body>'

        $.ajax({dataType: "xml",type: 'POST',url:'<?=base_url()?>index.php/seguridad/permisos/save',data: { strxml: strXml } ,                                     
            success: function( xmlResponse )
            {
                var $xml = $(xmlResponse)             
                var numError=$xml.find('numError').text();
                var descError=$xml.find('descError').text();
                if(numError != 0)
                {
                    alert(descError)
                }
                confirmAction=false
            },
            beforeSend: function(){
            spinnerStart($(".panel-body"))
                },
            complete: function(){
            spinnerEnd($(".panel-body"))
                }
           })
        
        }//if confirmactrion
        $('[data-modal-id="reset-local-storage-confirmation"]').modal('hide');
     
    });

    $(document).on('click', '[data-click=cancel-action]', function(e) {        

        $('[data-modal-id="reset-local-storage-confirmation"]').modal('hide');
     
    });

        
    });

$("#data-table-permiso input[type=checkbox]").live('click', function() {
    
        var strId=(this.id).replace("chkModid_","")
        var arr=strId.split("_")
        var id_rol=arr[0]
        var nro_permiso_modulo=arr[1]
        var nro_permiso=arr[2]
        var arrperm=arrPermisos[id_rol]
        for(i=0;i<arrperm.length;i++)
        {
            if(arrperm[i]['nro_permiso']==nro_permiso && arrperm[i]['nro_permiso_modulo']==nro_permiso_modulo)
            {
                arrperm[i]['checked']=this.checked
                break
            }
        }

    arrPermisos[id_rol]=arrperm
        
    });




</script>
<!-- ================== END PAGE LEVEL JS ================== -->