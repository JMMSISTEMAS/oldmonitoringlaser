// Establece las opciones de un select borrando las que tuviera
function set_select_options(id, options, values){
    opt = document.getElementById(id)
    while (opt.firstChild){
        opt.removeChild(opt.lastChild)
    }
    options.forEach((x, i)=>{
      new_option_elem = document.createElement('option')
      new_option_elem.value = values[i]
      new_option_elem.innerHTML = x
      opt.appendChild(new_option_elem)
    })    
}

// Añade nuevas manteniendo las antiguas
function insert_select_options(id, options, values){
    opt = document.getElementById(id)
    options.forEach((x, i)=>{
      new_option_elem = document.createElement('option')
      new_option_elem.value = values[i]
      new_option_elem.innerHTML = x
      opt.appendChild(new_option_elem)
    })    
}

function roundToTwo(num) {
  return +(Math.round(num + "e+2")  + "e-2");
}