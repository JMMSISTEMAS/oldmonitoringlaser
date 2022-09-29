const filter = {
  'zona': 0,
  'maquina': -1,
}

const assoc_zona_nombre = {
  "Granada": 0,
  "Madrid": 1,
  "Levante": 2,
  "Noreste": 3
}

let user = {}

const ui = {
  zonas: {
    id: 'input_zona', 
    get elem(){
      return document.getElementById(this.id)
    }
  }, 
  maquinas: {
      id: 'input_maquina', 
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

let maquinas = []
let last_response = []

const zonas = Object.keys(assoc_zona_nombre)


function init_data(){
  //Hacemos que la fecha máxima elegible sea el día de hoy
  ui.fecha_inicio.elem.max = bdates_today_iso()
  ui.fecha_fin.elem.value = bdates_today_iso()
  ui.fecha_fin.elem.max = bdates_today_iso()

  //Petición para ver qué usuario está logueado
  const url1 = 'server/services/get_current_user.php'
  fetch(url1).then(function (response) {
    return response.text()  
  })
  .then(function (body) {
    const data = JSON.parse(body)
    last_response = data
    user = data
    console.log(data)
  })

  
  //Petición incial para saber las máquinas de la zona inicial
  const url2 = 'server/services/initial_query.php'
  fetch(url2)
  .then(function (response) {
    return response.text()  
  })
  .then(function (body) {
    const data = (JSON.parse(body)).data
    last_response = data
    maquinas = d_update_maquinas(data.map(x=>x.Terminal))
  })

  rui()
}

document.addEventListener('DOMContentLoaded', init_data, false);

function d_update_maquinas(nuevas_maquinas){
  nuevas_maquinas.unshift('Todas')
  return nuevas_maquinas
}

// Validación formularios

function form_is_valid(){
  let is_valid = true
  return is_valid
}

// Eventos
function ev_change_zona(){
  const zona = ui.zonas.elem.value
  const url = `server/services/get_maquina_by_zona.php?zona=${zona}`

  fetch(url)
    .then(function (response) {
      return response.text()  
    })
    .then(function (body) {
      const data = (JSON.parse(body)).data
      last_response = data
      maquinas = d_update_maquinas(data.map(x=>x.Terminal))
      rui_maquinas()
    })
}

function ev_click_filter(){
  if(form_is_valid){
    const zona = ui.zonas.elem.value
    const maquina = ui.maquinas.elem.value !== 'Todas' ? ui.maquinas.elem.value : -1
    const fechas = format_dates()
    const inicio = fechas[0]
    const final = fechas[1]

    let url = `server/services/make_query.php?zona=${zona}&inicio=${inicio}&fin=${final}`
    if(maquina!=-1){
      url += `&maquina=${maquina}`
    }
    
    fetch(url)
    .then(function (response) {
      return response.text()  
    })
    .then(function (body) {
      const data = (JSON.parse(body)).data
      last_response = data
      btables_generate_table('tabla_resultados', data)
      console.log(data)
    })
  }
}

// Funciones manipulación de datos
function format_dates(){
  // El día empieza a las 8 de la mañana
  let start = ui.fecha_inicio.elem.value
  start += ' 8:00:00.000'

  // El día acaba al siguiente día a las 7:59:59
  let end = ui.fecha_fin.elem.value
  let end_date = new Date(end)
  end_date.setDate(end_date.getDate() + 1)
  end = bdate_date_to_iso(end_date)
  end += ' 7:59:59.999'

  return [start, end]
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

