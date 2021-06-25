//dado un formulario, serializa en forma de arreglo , los inputs
$.fn.serializeObject=function()
{
var o={};
var a=this.serializeArray();
$.each(a,function(){
 if(o[this.name]!== undefined){
    if(!o[this.name].push){
        o[this.name]=[o[this.name]];
    }
    o[this.name].push(this.value || '');
    }else
    {
        o[this.name]=this.value || '';
    } 
});
return o;
};


var cbox=function(id_control,options)
{   
    this.columnas=(options.columnas!=undefined)?options.columnas:null;
    this.vista=(options.vista!=undefined)?options.vista:null;
    this.orden=(options.orden!=undefined)?options.orden:"";
    this.condicion=(options.condicion!=undefined)?options.condicion:"";
    this.opciones=(options.opciones!=undefined)?options.opciones:Array(); //debe ser un array asociativo de campos id, descripcion
    this.id=id_control;
    this.selectedValue='';
    this.tagDisplay="cb"+id_control;
    this.guardarCache=(options.guardarCache!=undefined)?options.guardarCache:true;
    //la tag a dibujar debe llamarte cb_id_control
    
    //var cmp=options.columnas//Array("id_consultorio","consultorio")    
   this.cboxinit=function()
    {selectedValue=this.selectedValue
        if(this.opciones.length>0)
        {
            this.cargaropciones(this.opciones)
        }else
        {
            if(this.guardarCache)                    
            {ofw.getAsync(this.vista,this.columnas,this.condicion,this.orden,this.cargaropciones)}
            else
            {ofwlocal.getAsync(this.vista,this.columnas,this.condicion,this.orden,this.cargaropciones)}
        }
    
    }
    this.cboxload=function()
    {   
        $("#"+this.tagDisplay).html("<select id='"+this.id+"' name='"+this.id+"' class='form-control'></select>")        
    }
    var selectedValue=''
    this.set_value=function(id){
        
        this.cboxload()        
        if(this.opciones.length>0)
        {
            this.cargaropciones(this.opciones,id)
        }else
        {
            if(this.guardarCache)                    
            {      
                ofw.getAsync(this.vista,this.columnas,this.condicion,this.orden,this.cargaropciones)
            }
            else
            {
                ofwlocal.getAsync(this.vista,this.columnas,this.condicion,this.orden,this.cargaropciones)
            }
        }
    }
    var cbid=this.id

    this.cargaropciones=function(arr)
    {  
        
     $("#"+cbid).append("<option value=''></option>")

        for (i in arr)
        { if(selectedValue==arr[i].id)
           { $("#"+cbid).append("<option value='"+arr[i].id+"' selected='selected'>"+arr[i].descripcion+"</option>")}
            else
            {$("#"+cbid).append("<option value='"+arr[i].id+"'>"+arr[i].descripcion+"</option>")}
        }
        $("#"+cbid).combobox();
    }
    
    this.get_value=function()
    {
        return $("#"+this.id).val()
    }    
        this.cboxload();
        this.cboxinit();
}

function ObjxmlTostring(objxml)
{
	var xmlString=''
	if (window.ActiveXObject){ 
    xmlString = objxml.xml; 
  } else {
    var oSerializer = new XMLSerializer(); 
    xmlString = oSerializer.serializeToString(objxml[0]);
  } 
  return xmlString
}


function tomoney(n) {
  let t=n.toString();
  let regex=/(\d*.\d{0,2})/;
  return parseFloat(t.match(regex)[0]);
}
function number_format (number, decimals, dec_point, thousands_sep) {

var n = number, prec = decimals;

var toFixedFix = function (n,prec) {
    var k = Math.pow(10,prec);
    return (Math.round(n*k)/k).toString();
};

n = !isFinite(+n) ? 0 : +n;
prec = !isFinite(+prec) ? 0 : Math.abs(prec);
var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

var abs = toFixedFix(Math.abs(n), prec);
var _, i;

if (abs >= 1000) {
    _ = abs.split(/\D/);
    i = _[0].length % 3 || 3;

    _[0] = s.slice(0,i + (n < 0)) +
          _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
    s = _.join(dec);
} else {
    s = s.replace('.', dec);
}

var decPos = s.indexOf(dec);
if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
    s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
}
else if (prec >= 1 && decPos === -1) {
    s += dec+new Array(prec).join(0)+'0';
}
return s;
}


//valida si la cadena ingresada es real

function esCadenaReal(cadena)
{
	var illegalChars = /^[1-9]+[\.]{0,1}\d*$/;
	var patron = /^[1-9]+[\.]$/;
	if (illegalChars.test(cadena) && cadena.search(patron) )
	return true;
	return false;

}

function esNumeroReal(strNumero){
  var valido=false;
  
  try {
  
  regexp = /^-?\d+(?:\.\d+)?$/;
  valido=regexp.test(strNumero);
  }
  catch(error) {
  }
  return valido;
}


//formato yyyy-MM-dd
function esFechaF1(expresion)
{
	/*
Fecha con formato yyyy-MM-dd
(19|20)\d\d([- /.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])
	*/
	patron=/(19|20)\d\d([- /.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])/
	return patron.test(expresion);
}

//formato dd-MM-yyyy
function esFechaF2(expresion)
{
str=replaceAll( expresion,"/","-" );
partes=str.split("-");
anio=partes[0];
mes=partes[1];
dia=partes[2];

cadena=dia+"-"+mes+"-"+anio;

	return esFechaF1(cadena);
}
//formato HH:mm
function esHoraMin(expresion){
  patron=/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/
  return patron.test(expresion);
}

function replaceAll( text, busca, reemplaza ){
  while (text.toString().indexOf(busca) != -1)
      text = text.toString().replace(busca,reemplaza);
  return text;
}

//dado formato de fecha DD/MM/YYYY devuelve el dato convertido a objeto date
function strtodate(text){
  if(text==""){
    return "";
  }
  //var st = "26.04.2013";
  /*var partes=text.split("/")
var pattern = /(\d{2})\/(\d{2})\/(\d{4})/;
debugger
var dia=(partes[0]<10)?"0"+partes[0].toString():partes[0].toString()
var mes=(partes[1]<10)?"0"+partes[1].toString():partes[1].toString()
var anio=partes[3].toString()*/

var str=strdateformat(text)
var dt = new Date(str+'T00:00:00');

return dt;
}

function strdateformat(text){
  if(text==""){
    return "";
  }
  //var st = "26.04.2013";
  var partes=text.split("/")
var pattern = /(\d{2})\/(\d{2})\/(\d{4})/;
partes[0]=parseInt(partes[0])
partes[1]=parseInt(partes[1])
var dia=(partes[0]<10)?"0"+partes[0].toString():partes[0].toString()
var mes=(partes[1]<10)?"0"+partes[1].toString():partes[1].toString()
var anio=partes[2].toString()
return anio+'-'+mes+'-'+dia
}

//convierte date javascript a formato DD/MM/YYYY
function datetostr(f){
  var dia=(f.getDate()<10)?"0"+(f.getDate().toString()):f.getDate();
  var mes=(f.getMonth()+1<10)?"0"+((f.getMonth()+1).toString()):f.getMonth()+1;
  var anio=f.getFullYear();
fecha = dia + "/" + mes + "/" + anio;
return fecha;
}

//dado una fecha en formato date js, devuelve el dia en la semana para mysql
//en javascript 0 es lunes, 6 domingo , en mysql 1 es domingo, 7 sabado
function dayOfWeeKsql(dateIn){
  var ar={0:2,1:3,2:4,3:5,4:6,5:7,6:1}
var d=dateIn.getDay();
  var r=ar[d]
  return r
}

//valida si el caracter ingresado es nro entero (no real)
function esChrEntero(ch)
{
	patron = /\d|-/;
	return patron.test(ch);
}
//valida si la cadena forma un entero
function esCadenaEntero(cadena)
{

patron=/^(\+|-)?\d+$/;
return patron.test(cadena);

}


//valida si el caracter ingresado es nro entero (no real)
function esChrReal(ch)
{
	patron = /\d|\.|-/;
	return patron.test(ch);
}

//valida si el caracter ingresado es valido para formar una fecha
function esChrFecha(ch)
{
	patron = /\d|\/|-/;
	return patron.test(ch);
}

function es_numero(valor){ //verifica q sea numero con coma (valor flotante)
	if(/^\d+\.?\d*$/.test(valor)){
	return true;	
	}else{
	return false;
	}

}// es numero

function es_entero(valor){ //verifica q sea numero entero
  if(valor.trim()=="")
    return false;
  
	if(/^[0-9]*$/.test(valor)){
	return true;	
	}else{
	return false;
	}

}// es numero


function es_email(valor)
{
var filter=/^[A-Za-z][A-Za-z0-9_.-]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
if (valor.length == 0 ) return true;
if (filter.test(valor))
return true;
else
return false;
}

//nousar en key up
function teclamoney(e)
{

  if (e.which != 8 && e.which != 0 && e.which != 46 && e.which != 110 && (e.which < 48 || e.which > 57)) {
                 return false;
  }
return true

}

function keyupmoney(e)
{

  if ((e.key>=1 && e.key<9)|| e.key==".") {
                 return true;
  }
return false

}

function keyupentero(e)
{

  if ((e.key>=1 && e.key<9)) {
                 return true;
  }
return false

}
function teclaentero(e)
{

  if (e.which < 48 || e.which > 57) {
                 return false;
  }
return true

}


function todate(strdate,codigo){
    str="";
    if(codigo==103)
    {
      var p=strdate.split("/");
      if(p.length!=3)
      {
        p=strdate.split("-");
      }
      if(p.length!=3)
      {
        p=strdate.split("\\");
      }

      if(p.length==3)
      {
      str=p[2]+"-"+p[1]+"-"+p[0];
      }
    }
return str;
}

function adddays(odate,days){
  return new Date(odate.getTime()+(86400000*days))

}




function spinnerPanel()
{

var target = $(".panel").closest('.panel');
        if (!$(target).hasClass('panel-loading')) {
            var targetBody = $(target).find('.panel-body');
            var spinnerHtml = '<div class="panel-loader"><span class="spinner-small"></span></div>';
            $(target).addClass('panel-loading');
            $(targetBody).prepend(spinnerHtml);
            setTimeout(function() {
                $(target).removeClass('panel-loading');
                $(target).find('.panel-loader').remove();
            }, 2000);

        }
}

function spinnerStart(panelTarget)
{
        var target = panelTarget.closest('.panel');
        if (!$(target).hasClass('panel-loading')) {
            var targetBody = $(target).find('.panel-body');
            var spinnerHtml = '<div class="panel-loader"><span class="spinner-small"></span></div>';
            $(target).addClass('panel-loading');
            $(targetBody).prepend(spinnerHtml);
        }
        return

}

function spinnerEnd(panelTarget)
{
$(panelTarget).removeClass('panel-loading');
$(panelTarget).find('.panel-loader').remove();
return
}

var arrObjfw={}

function fw(urlbase) {
  this.fwcache=Array()
  this.onbefore=function(){};
  this.oncomplete=function(){};
  this.urlbase = urlbase;
  this.guardarCache=true;
  this.UID=generateUUID();
  //debugger
  arrObjfw[this.UID] =this;

  function generateUUID() {
    var d = new Date().getTime();
    var uuid = 'xxxxxxxxxxxx4xxxyxxxxxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
    return uuid;
   }


  function buscarcache(uid,condicion,orden,tbl,campos)
  {var test1=false;
   var test2=false;
   var idx=-1
   /*if(arrObjfw[uid] !=undefined)
   {*/
    for (i in arrObjfw[uid].fwcache)   
   {    test1=false;
        test2=false;
         if(arrObjfw[uid].fwcache[i].condicion==condicion && arrObjfw[uid].fwcache[i].orden==orden && arrObjfw[uid].fwcache[i].tbl==tbl)
        {   test1=true;
            if(arrObjfw[uid].fwcache[i].campos.length>0)
            {
                var existecampo=false
                for(e in arrObjfw[uid].fwcache[i].campos)
                { existecampo=false
                    for (j in campos)
                    {
                     if(arrObjfw[uid].fwcache[i].campos[e]==campos[j])
                     {
                        existecampo=true;
                        break;
                     }
                    }
                    if(!existecampo)
                    {test2=false
                     break;
                    }               
                }
                if(existecampo)    
                {test2=true}
            }else
            {
                test2=true;
            }
            
        }
    if(test1 && test2)
       {
        idx=i
        break
       }
    }
   /*}*/
   

   return idx;
    
  }
  //obtiene consulta desde el server sincrono, tbl=tabla, campos=columnas, condicion=where
  this.get=function(tbl,campos,condicion='',orden='')
 {
    var arrreturn=null
    if(tbl.trim()=='' || campos.length==0)
    {
        return arrreturn
    }

    if(this.guardarCache)
     {
         var idx=buscarcache(this.UID,condicion,orden,tbl,campos)  
         if(idx != -1)             
         {
            return this.fwcache[idx].data
         }               
     }

     that=this;
    var xmldata="<body><data tbl='"+tbl+"'>"    
    for(c in campos)
    {
    xmldata+="<col><![CDATA["+campos[c]+"]]></col>"
    }
    xmldata+="<condicion><![CDATA["+condicion+"]]></condicion>"
    xmldata+="<orden><![CDATA["+orden+"]]></orden>"
    xmldata+="</data></body>"
     var uid=this.UID
    $.ajax({dataType: "json",type: 'POST',async: false,url:this.urlbase+'service/get',data: {xmldata:xmldata} ,                                     
            success: function(response)
            {
                
                var numerror=response.numerror;
                var descerror=response.descerror;                
                if(numerror == 0)                
                {                
                
                arrreturn=response.data;        
                    if(arrObjfw[uid].guardarCache)
                    {var idx= -1;
                      idx=buscarcache(uid,condicion,orden,tbl,campos)  
                            //si no existe en cache, lo agrego
                             if(idx == -1)             
                             {
                                var cacheData={'tbl':tbl,'campos':campos,'orden':orden, 'condicion':condicion,'data':arrreturn}
                                arrObjfw[uid].fwcache.push(cacheData)
                             }                        
                    }
                }
                
            },
            beforeSend: function(){              
              if( typeof that.onbefore=="function"){
                that.onbefore();
              }
            },
            complete: function(){              
              if( typeof that.oncomplete=="function"){
                that.oncomplete();
              }
            }
        })
    
    return arrreturn;
  }//get sincrono

//obtiene consulta desde el server, tbl=tabla, campos=columnas, condicion=where, fdx=funcion a ejecutar una vez terminado
  this.getAsync=function(tbl,campos,condicion='',orden='',fxOnComplete)
 {
    var arrreturn=null
    if(tbl.trim()=='' || campos.length==0 || fxOnComplete==undefined)
    {
        return arrreturn
    }
    
    if(this.guardarCache)
     {var idx=buscarcache(this.UID,condicion,orden,tbl,campos)  
         if(idx != -1)             
         { fxOnComplete(this.fwcache[idx].data)
            return 
         }        
     }
    that=this; 
    var xmldata="<body><data tbl='"+tbl+"'>"    
    for(c in campos)
    {
    xmldata+="<col>"+campos[c]+"</col>"
    }
    xmldata+="<condicion><![CDATA["+condicion+"]]></condicion>"
    xmldata+="<orden><![CDATA["+orden+"]]></orden>"
    xmldata+="</data></body>"
    var uid=this.UID
    $.ajax({dataType: "json",type: 'POST',async: true,url:this.urlbase+'service/get',data: {xmldata:xmldata} ,                                     
            success: function(response)
            {
                
                var numerror=response.numerror;
                var descerror=response.descerror;                
                if(numerror == 0)
                {
                    if(arrObjfw[uid].guardarCache)
                    {var idx= -1;
                      idx=buscarcache(uid,condicion,orden,tbl,campos)  
                            //si no existe en cache, lo agrego
                             if(idx == -1)             
                             {
                                var cacheData={'tbl':tbl,'campos':campos,'orden':orden, 'condicion':condicion,'data':response.data}
                                arrObjfw[uid].fwcache.push(cacheData)
                             }                        
                    }                 
                            
                    if(fxOnComplete !=null)
                    {
                    fxOnComplete(response.data)    
                    }
                
                }
                
            },
             beforeSend: function(){              
              if( typeof that.onload=="function"){
                that.onload();
              }
            },
            complete: function(){              
              if( typeof that.oncomplete=="function"){
                that.oncomplete();
              }
            }
        })

  }//get asincrono

}

function round2dec(numero)
{
var flotante = parseFloat(numero);
var resultado = Math. round(flotante*100)/100;
return resultado;
}


var format_number=function(n,m){

    var ns=n.toString();
    var na=ns.split(".");
    var ma=m.split(".");
    if(ma.length>1 && na.length>1)    { 
        return addizq(na[0],ma[0])+"."+addder(na[1],ma[1])
    }
    if(ma.length>1 && na.length==1)
    {
    return addizq(na[0],ma[0])+"."+addder("",ma[1])
    }
    if(ma.length==1 && na.length>1)
    {
    return addizq(na[0],m)+"."+na[0]
    }
    if(ma.length==1 && na.length==1)
    {
    return addizq(n,m)
    }

function addder(n,m)
{
    if(m==undefined || n==undefined)
    {
            return
    }
    if(n=="" && m=="#")
    {
    return ""
    }
    if(n=="" && m!="#")
    {
    return m
    }

    if(m=="#")
    {
        return n[0]+addder(n.substring(1,n.length),"#")
    }
    if(m!="#" && m.length>1)
    {
    return n[0]+addder(n.substring(1,n.length),m.substring(1,m.length))
    }
    if(m!="#" && m.length==1)
    {
    return n.substring(0,1)
    }

}

function addizq(n,m)
{
    if(m==undefined || n==undefined)
    {
            return
    }
    if(n=="" && m=="#")
    {
    return ""
    }
    if(n=="" && m!="#")
    {
    return m
    }

    if(m=="#")
    {
        return addizq(n.substring(0,n.length-1),"#")+n[n.length-1]
    }
    if(m!="#" && m.length>1)
    {
    return addizq(n.substring(0,n.length-1),m.substring(0,m.length-1))+n[n.length-1]
    }
    if(m!="#" && m.length==1)
    {
    return n
    }

}

}


function fwmodal()
{
    function getstyle(type)
    {
        var sty=''
            switch(type) {
              case 1:
              sty='success'
              break;
              case 2:
              sty='info'    
              break;
              case 3:
              sty='warning'    
              break;
              case 4:
              sty='danger'    
              break;
              default:
                sty='success'    
            }
            return sty;
    }
    
    this.alert=function(mensaje,titulo='Atención',type=0)
    { var htmlmensaje=''
      if(typeof $("#fwmodal-dialog").val()!="undefined")
      {
       $("#fwmodal-dialog").remove() 
      }
        if(type==0)
        {
            htmlmensaje='<p>'+mensaje+'</p>'
        }
        else
        {           

         htmlmensaje='<div class="alert alert-'+getstyle(type)+' m-b-0">'+
                      '                  <h4><i class="fa fa-info-circle"></i>'+titulo+'</h4>'+
                      '                  <p>'+mensaje+'</p>'+
                      '              </div>'
        }
        var targetModalHtml='<div class="modal fade" id="fwmodal-dialog">'+
                     '   <div class="modal-dialog">'+
                     '      <div class="modal-content">'+
                     '          <div class="modal-header">'+
                     '              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
                     '              <h4 class="modal-title">'+((type==0)?titulo:"")+'</h4>'+
                     '         </div>'+
                     '          <div class="modal-body">'+htmlmensaje+
                     '          </div>'+
                     '          <div class="modal-footer">'+
                     '              <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Cerrar</a>'+                     
                     '          </div>'+
                     '      </div>'+
                     '  </div>'+
                     '</div>'

    
        $('body').append(targetModalHtml);
        $('[id="fwmodal-dialog"]').modal('show');
        $(document).on('hidden.bs.modal', '[id="fwmodal-dialog"]', function(e) {
            $('[id="fwmodal-dialog"]').remove();
             setTimeout(function() { 
             //lo elimino manual mente porque a veces no lo elimina              
              if(typeof $('modal-backdrop')!="undefined"){
                $('modal-backdrop').remove();
              }

             }, 2000);
        });

    };//alert

    this.dialog=function(mensaje,titulo='Atención',type=0,fxOKbutton,params)
    { var htmlmensaje=''
      if(typeof $("#fwmodal-dialog").val()!="undefined")
      {
       $("#fwmodal-dialog").remove() 
      }
        if(type==0)
        {
            htmlmensaje='<p>'+mensaje+'</p>'
        }
        else
        {
         
         htmlmensaje='<div class="alert alert-'+getstyle(type)+' m-b-0">'+
                      '                  <h4><i class="fa fa-info-circle"></i>'+titulo+'</h4>'+
                      '                  <p>'+mensaje+'</p>'+
                      '              </div>'
        }
        var targetModalHtml='<div class="modal fade" id="fwmodal-dialog">'+
                     '   <div class="modal-dialog">'+
                     '      <div class="modal-content">'+
                     '          <div class="modal-header">'+
                     '              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
                     '              <h4 class="modal-title">'+((type==0)?titulo:"")+'</h4>'+
                     '         </div>'+
                     '          <div class="modal-body">'+htmlmensaje+
                     '          </div>'+
                     '          <div class="modal-footer">'+
                     '<a href="javascript:;" class="btn btn-sm btn-primary" id="btnOK">Continuar</a>'+
                     '              <a href="javascript:;" class="btn btn-sm btn-danger" data-dismiss="modal">Cerrar</a>'+                     
                     '          </div>'+
                     '      </div>'+
                     '  </div>'+
                     '</div>'

    
        $('body').append(targetModalHtml);
        $('[id="fwmodal-dialog"]').modal('show');
        $(document).on('hidden.bs.modal', '[id="fwmodal-dialog"]', function(e) {          
            $('[id="fwmodal-dialog"]').remove();
        });
        $('[id="btnOK"]').click(function(){
          
           $('[id="fwmodal-dialog"]').modal('hide');
           //$('[id="fwmodal-dialog"]').remove();
           
           if(typeof fxOKbutton == "function") 
           {
            if(typeof params !="undefined" && typeof params !="null")
              {
                fxOKbutton(params)
              }else{
                fxOKbutton();
              }            
           }

        })

    };//dialog

}//fwmodal


var  fwt=function(urloyente){
  this.fwcache=Array()  
  this.oyente = urloyente;
  //obtiene consulta desde el server sincrono, definicion que la espera, parametros (respetando orden) y si acepta cache o no
  this.get=function(definicion,parametros,cache=false)
 {  that=this;    
    var arrreturn=null;     
    definicion=definicion.trim();

    if(cache){
    arrreturn=this.buscarcache(definicion,parametros)
    if(arrreturn != null){
        return arrreturn;
    }
   }

    var data=JSON.stringify(parametros)
    $.ajax({dataType: "json",type: 'POST',async: false,url:this.oyente,data:{definicion:definicion,data:data} ,                                     
            success: function(response)
            {
                
                var numerror=response.numerror;
                var descerror=response.descerror;                
                if(numerror == 0)                
                {   rs=response.data['rs'];             
                    arrreturn=rs;
                    if(response.data['enc']==1){
                     arrreturn=JSON.parse(window.atob(rs));
                    }
                    
                    if(cache){                        
                        that.fwcache.push({definicion:definicion,parametros:parametros,rs:arrreturn})
                    }
                }
                
            },
            beforeSend: function(){},
            complete: function(){}
        })
    
    return arrreturn;
  }//get sincrono

//obtiene consulta desde el server asynsincrono, definicion que la espera, parametros (respetando orden) y si acepta cache o no
  this.getAsync=function(definicion,parametros,onfwsuccess,cache=false)
 {  
     var arrreturn=null;     
    definicion=definicion.trim();
    var arrreturn=null
    if(definicion=='' || onfwsuccess==undefined)
    {
        return arrreturn
    }
   var that=this

   if(cache){
    arrreturn=this.buscarcache(definicion,parametros)
    if(arrreturn != null){
        onfwsuccess(arrreturn)
        return;
    }
   }

   var data=JSON.stringify(parametros)
    $.ajax({dataType: "json",type: 'POST',async: true,url:this.oyente,data:{definicion:definicion,data:data} ,                                     
            success: function(response)
            {
                
                var numerror=response.numerror;
                var descerror=response.descerror;                
                if(numerror == 0)
                {
                    rs=response.data['rs'];             
                    arrreturn=rs;
                    if(response.data['enc']==1){
                     arrreturn=JSON.parse(window.atob(rs));
                    }
                    if(cache){                        
                        that.fwcache.push({definicion:definicion,parametros:parametros,rs:arrreturn})
                    }
                    if(onfwsuccess !=null)
                    {   
                    onfwsuccess(arrreturn)
                    return;    
                    }
                }
                
            },
            beforeSend: function(){
                that.onbefore();
            },
            complete: function(){
              that.oncomplete();  
            }
        })

  },//get asincrono
  this.onbefore=function(){},
  this.oncomplete=function(){},
  this.buscarcache=function(definicion,parametros={}){
    var encontrado=false;

    var rs=null;    
    for(i in this.fwcache){
        if(encontrado==true){
            break;
        }
        var ele=this.fwcache[i]
        if(ele.definicion==definicion){
            //pruebo si tienen la misma longitud
            if(parametros!=null && parametros.length==ele.parametros.length){   
                //recorro los parametros que contiene la definicion
                var elementosOK=true;
               for(p in ele.parametros) {
                 if(ele.parametros[p]!=parametros[p]){
                    elementosOK=false;
                    break;
                 }
               }

               if(elementosOK){
                encontrado=true;
                rs=ele.rs;
               }    
            }else{
                continue;
            }
            
        }else{
            continue; //busca en el resto de la cache si hay otra definicion igual
        }
    }
    return rs;
  }//buscarcache

}


//sea una fecha string dd-mm-yyyy h:i:s convierte a date de javascritp
function strtodatetime(str){
var str=str.replace(/\//g,"-") //si viene con / en vez de -
 var f=str.split(" ");
 var fechastr=""
 var horastr="00:00:00";
 //tiene fecha y hora, sino, solo fecha
 fechastr=f[0]
 if(f.length>1){    
    horastr=f[1]
 }
 f=fechastr.split("-")
 s=f[1]+"-"+f[0]+"-"+f[2]+" "+horastr
 return new Date(s);
}

//para unificar array: sea myArr=[1,2,3,1,2] , myArr.unique() -> 1,2,3
/*Array.prototype.unique=function(a){
  return function(){return this.filter(a)}}(function(a,b,c){return c.indexOf(a,b+1)<0
});*/

//detecta si este navegador es un celular o una pc
function isMobile(){
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
   return true
}else{
  return false
}
}


//sea una de base de datos yyyy-mm-dd h:i:s convierte a dd-mm-yyyy h:i:s
function datetimetostr(str){
var str=str.replace(/\//g,"-") //si viene con / en vez de -
 var f=str.split(" ");
 var fechastr=""
 var horastr="00:00:00";
 //tiene fecha y hora, sino, solo fecha
 fechastr=f[0]
 if(f.length>1){    
    horastr=f[1]
 }
 f=fechastr.split("-")
 return f[2]+"-"+f[1]+"-"+f[0]+" "+horastr
 
}