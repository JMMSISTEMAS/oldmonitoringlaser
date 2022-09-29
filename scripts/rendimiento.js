//const usuario = document.getElementsByName('')
let last_response = {}

const ui = {
    zonas: {
      id: 'input_zona', 
      get elem(){
        return document.getElementById(this.id)
      }
    }, 
    grupo: {
        id: 'input_grupo', 
        get elem(){
          return document.getElementById(this.id)
        }
    }, 
    fecha_inicio:{
      id: 'input_start_date', 
      get elem(){
        return document.getElementById(this.id)
      }
    },
    fecha_fin:{
      id: 'input_end_date', 
      get elem(){
        return document.getElementById(this.id)
      }
    }
}

function init_data(){
    //Hacemos que la fecha máxima elegible sea el día de hoy
    initial_date()
}  

function form_is_valid(){
    let is_valid = true
    const fecha_inicio = new Date(ui.fecha_inicio.elem.value)
    const fecha_fin = new Date(ui.fecha_fin.elem.value)
    if(fecha_inicio > fecha_fin){
      alert('Fecha incorrecta')
      fechas = format_dates()
      ui.fecha_inicio.elem.value
      return false;
    }
    return is_valid
}

function click_filter(){
  if(form_is_valid()){
    const zona = ui.zonas.elem.value
    const grupo = ui.grupo.elem.value
    const fechas = format_dates()
    const inicio = fechas[0]
    const final = fechas[1]
    const url = `server/services/get_filter.php?zona=${zona}&grupo=${grupo}&inicio=${inicio}&fin=${final}`
    
    fetch(url)
      .then(function (response){
        return response.text()  
      })
      .then(function (body){
        const data = JSON.parse(body)
        last_response = data
        registros = PHPdata_JSdata(data, inicio, final)
        rendimiento_cabecera('cabecera_datos', group_data)
        tabla_rendimiento('tabla_resultados', registros[0])
        append_summary_row('tabla_resultados', registros[1])
        rendimiento_summary('resumen_datos', registros[2])
      })    
  }
}


// Funciones manipulación de datos
function format_dates(){
    // El día empieza a las 8 de la mañana
    let start = ui.fecha_inicio.elem.value
    //start += 'T00:00:00.000'
  
    // El día acaba al siguiente día a las 7:59:59
    let end = ui.fecha_fin.elem.value
    //let end_date = new Date(end)
    //end_date.setDate(end_date.getDate() + 1)
    //end = bdates_sql_friendly(end_date)
    //end += 'T00:00:00.000'
  
    return [start, end]
  }

// Obtienes las fechas iniciales para los calendarios de las consultas
// Por defecto se pondrá que la fecha de incio es el 1 del mes actual
// Y que la fecha de fin sea el día de hoy
// Establece las fechas en formato válido para la ui
function initial_date(){
  /*const first_day_month = bdates_sql_friendly(bdates_firstDay_currentMonth())
  const now = new Date()
  ui.fecha_inicio.elem.value = first_day_month
  ui.fecha_inicio.elem.max = bdates_sql_friendly(now)
  ui.fecha_fin.elem.value = bdates_sql_friendly(now)
  ui.fecha_fin.elem.max = bdates_sql_friendly(now)*/
}

  
// Actualiazación de la GUI
function rui(){
  rui_zonas()
  rui_maquinas()
}

function rui_zonas(){
  set_select_options(ui.zonas.id, zonas, Object.values(assoc_zona_nombre))
}

function rui_maquinas(){
  set_select_options(ui.maquinas.id, maquinas, maquinas)
}


//Para imprimir pdf
function print_pdf(){
  //printJS('tabla_resultados', 'html')
  printJS({
    printable : 'tabla_resultados',
    type: 'html',
    css: 'styles/printing_table.css',
    showModal: true,
    modalMessage: 'Preparando el fichero PDF...'
  })
}



document.addEventListener('DOMContentLoaded', init_data, false);  