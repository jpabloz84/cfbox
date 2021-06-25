var base_url=$("#base_url").val()
var ofwt=new fwt(base_url+'index.php/seguridad/usuarios/listener');



var miCanvas = $('#pizarra');
// Eventos raton
miCanvas.on("mousedown",empezarDibujo)
miCanvas.on("mousemove",function(e){
    dibujarLinea(e)
})
miCanvas.on("mouseup",pararDibujar)
// Eventos pantallas táctiles
miCanvas.on("touchstart",empezarDibujo)
miCanvas.on("touchmove",function(e){
    dibujarLinea(e)
})

var lineas = [];
var correccionX = 0;
var correccionY = 0;
var pintarLinea = false;
$("#btnfirma").click(function(){
    $("#modal-firma").modal("show")
})

 function Limpiarfirma() {
          let ctx = miCanvas[0].getContext('2d');
          ctx.clearRect(0, 0, miCanvas[0].width, miCanvas[0].height);
          lineas = [];
          correccionX = 0;
          correccionY = 0;
          pintarLinea = false;
          posicion = miCanvas[0].getBoundingClientRect()
          correccionX = posicion.x;
          correccionY = posicion.y;
          miCanvas[0].width = $('#divCanvas').width();
          miCanvas[0].height = $('#divCanvas').height();
}
function empezarDibujo () {    
        pintarLinea = true;
        lineas.push([]);
}

 function dibujarLinea (event) {
    
        event.preventDefault();
        if (pintarLinea) {
            
            let ctx = miCanvas[0].getContext('2d')
            // Estilos de linea
            ctx.lineJoin = ctx.lineCap = 'round';
            ctx.lineWidth = 2;
            // Color de la linea
            ctx.strokeStyle = '#000' //'#fff';
            // Marca el nuevo punto
            let nuevaPosicionX = 0;
            let nuevaPosicionY = 0;
            var offset = $("#pizarra").offset();
            
            if (event.originalEvent.changedTouches == undefined) {
                // Versión ratón
             var  layerX = event.clientX - offset.left + $("body").scrollLeft();
             var  layerY = event.clientY - offset.top + $("body").scrollTop();
                
                nuevaPosicionX =layerX //event.layerX;
                nuevaPosicionY = layerY //event.layerY;
            } else {
                // Versión touch, pantalla tactil
                
                posx=event.originalEvent.touches[event.originalEvent.touches.length-1].clientX
                posy=event.originalEvent.touches[event.originalEvent.touches.length-1].clientY   
                nuevaPosicionX =posx - offset.left + $("body").scrollLeft();  //event.changedTouches[0].pageX - correccionX;
                nuevaPosicionY = posy -correccionY//- offset.top + $("body").scrollTop(); //event.changedTouches[0].pageY - correccionY;
            }
            // Guarda la linea
            lineas[lineas.length - 1].push({
                x: nuevaPosicionX,
                y: nuevaPosicionY
            });
            // Redibuja todas las lineas guardadas
            ctx.beginPath();
            lineas.forEach(function (segmento) {
               if(segmento.length > 0)
               {
                ctx.moveTo(segmento[0].x, segmento[0].y);
                segmento.forEach(function (punto, index) {
                    ctx.lineTo(punto.x, punto.y);
                });
               }
            });
            ctx.stroke();
        }
    }

/**
 * Funcion que deja de dibujar la linea
 */
function pararDibujar () {
    
    pintarLinea = false;
}

    var umbral =0.0002
function isCanvasTransparent(canvas) { 
    
    var ctx = canvas.getContext("2d");
    var imageData = ctx.getImageData(0, 0, canvas.offsetWidth, canvas.offsetHeight);
    var datalength = imageData.data.length
    var datalengthNoBlank=0
    for (var i = 0; i < datalength; i += 4) {
        if (imageData.data[i + 3] !== 0)
            datalengthNoBlank++;

        if ((datalengthNoBlank / datalength) > umbral) {
            return false
        }
    }
    return true

}


function Guardarfirma() {
            var imgbase64 = ""              
             imgbase64 = miCanvas[0].toDataURL("image/jpg");
            //imgbase64 = miCanvas[0].toDataURL("image/png").split("data:image/png;base64,")[1]
           // imgbase64=imgbase64.replace(/^data:image\/(png|jpg);base64,/, "")
            imgbase64 = imgbase64.trim()
            if (isCanvasTransparent(miCanvas[0]) || imgbase64 == "" || imgbase64== null) {
                swal("Atención","Intente nuevamente","error")
                Limpiarfirma()
                return
            }
           $("#firma").attr("src",imgbase64)
           $("#img_firma").val(imgbase64)
           
}
$('#modal-firma').on('shown.bs.modal', function () {
     Limpiarfirma()
    posicion = miCanvas[0].getBoundingClientRect()
    correccionX = posicion.x;
    correccionY = posicion.y;
    miCanvas[0].width = $('#divCanvas').width();
    miCanvas[0].height = $('#divCanvas').height();
    
    let ctx = miCanvas[0].getContext('2d');
    ctx.beginPath();
    lineas.forEach(function (segmento) {
       if(segmento.length > 0)
       {
        ctx.moveTo(segmento[0].x, segmento[0].y);
        segmento.forEach(function (punto, index) {
            ctx.lineTo(punto.x, punto.y);
        });
       }
     });
    ctx.stroke();
    dibujarNombres();
    
});      

$("#limpiar-firma").click(function(){
    Limpiarfirma()
    dibujarNombres();
})  

$("#confirm-firma").click(function(){
    Guardarfirma()
    $("#modal-firma").modal("hide")
})  


function dibujarNombres(){
	var ctx = miCanvas[0].getContext('2d');
	var nombre=	$("#nombre").val()
	var apellido=$("#apellido").val()
	ctx.font = "30px Arial";
	var left=(miCanvas[0].width/2)-150
	var top=miCanvas[0].height-20
	ctx.fillText(nombre.toUpperCase() +" "+apellido.toUpperCase(), left, top);
}