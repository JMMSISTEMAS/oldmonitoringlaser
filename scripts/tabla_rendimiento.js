function format_time(time_str){
    return time_str.split('.')[0]
}


function pretty_date(ugly_date){
    return ugly_date.split('-').reverse().join('/')
}

function PHPdata_JSdata(PHPdata, inicio, fin){
    fechas = bdates_get_range(inicio, fin)
    fechas = fechas.map(x=>bdates_sql_friendly(x))
    
    datos = fechas.map(function(fecha, i){
        datos2 = PHPdata.filter(function(registro){
            return registro['dia'].split(' ')[0] == fecha
        })
        return datos2
    })

    tams = datos.map(x=>x.length)
    nombre_maquinas = datos[Math.max(...tams)].map(x=>x.nombre)
    turnos = datos.map(x=>{if(x.length>0){return x[0]['turno']} else {return 0}})

    datos2 = []
    datos.forEach((dato, i)=>{
        datos2.push([])
        nombre_maquinas.forEach((nombre, j)=>{
            if(datos[i].map(x=>x.nombre).filter(x=>x===nombre).length>0){
                idx = datos[i].map(x=>x.nombre).indexOf(nombre)
                aux = datos[i][idx]
                obj = {}
                obj[nombre+' cortando'] = format_time(format_time(aux['cortando']))
                obj[nombre+' parada pausa'] = format_time(aux['pausa'])
                obj[nombre+' parada error'] = format_time(aux['error'])
                obj[nombre+' parada mant.'] = format_time(aux['mantenimiento'])
                obj[nombre+' chapas cortadas'] = aux['chapas']
                datos2[i].push(obj)
            }
            else{
                obj = {}
                obj[nombre+' cortando'] = '00:00:00'
                obj[nombre+' parada pausa'] = '00:00:00'
                obj[nombre+' parada error'] = '00:00:00'
                obj[nombre+' parada mant.'] = '00:00:00'
                obj[nombre+' chapas cortadas'] = 0
                datos2[i].push(obj)
            }
        })
    })

    //Añadimos los atributos derivados
    datos2.forEach((fila, i)=>{
        fila.forEach((registro, j)=>{
            //registro['Fecha'] =  pretty_date(fechas[i])
            //registro['Equipo total horas'] = '16:00:00'
            nombre = nombre_maquinas[j]
            primitivos = [
                registro[nombre+' cortando'],
                registro[nombre+' parada pausa'],
                registro[nombre+' parada error'],
                registro[nombre+' parada mant.']
            ]
            primitivos_formateados = primitivos.map(
                x=>format_time(x)
            )
            primitivos_segundos = primitivos_formateados.map(
                x=> get_seconds(x)
            )
            segundos_encendida = primitivos_segundos.reduce((a,b)=>a+b)
            segundos_apagada = get_seconds('08:00:00')-segundos_encendida
            segundos_parada = primitivos_segundos.slice(1).reduce((a,b)=>a+b)
            derivados = [segundos_encendida, segundos_apagada, segundos_parada]
            derivados_formateados = derivados.map(x=>seconds_to_strTime(x))
            registro[nombre+ ' encendida'] = derivados_formateados[0]
            registro[nombre+ ' apagada'] = derivados_formateados[1]
            registro[nombre+ ' parada total'] = derivados_formateados[2]
        })
    })

    campos_maquina = [
        ' encendida',
        ' apagada',
        ' cortando',
        ' parada pausa',
        ' parada error',
        ' parada mant.',
        ' parada total',
        ' chapas cortadas'
    ]

    //Añadimos fecha y turno y unimos todas las maquinas de dicho día
    filas = datos2.map((dato,i)=>{
        obj = {
            'Fecha': pretty_date(fechas[i]),
            'Turno': turnos[i]
        }

        datos_unidos = Object.assign({}, ...dato)
        nombre_maquinas.forEach((nombre, i)=>{
            campos_maquina.forEach((campo, j)=>{
                nombre_campo = nombre+campo
                obj[nombre_campo] = datos_unidos[nombre_campo]            
            })
        })
        
        function todas_horas_cero(){
            return nombre_maquinas
                .map(x=>x+' cortando')
                .map(x=>datos_unidos[x])
                .every(x=>x==='00:00:00')
        }

        if(todas_horas_cero()){
            obj['Equipo total horas'] = '00:00:00'
        }
        else{
            obj['Equipo total horas'] = '24:00:00'
        }

        
        return obj
    })

    //Hacemos la sumatoria del resumen
    sumatoria = []
    campos_no_tiempo = [' chapas cortadas']
    nombre_maquinas.forEach((nombre, i)=>{
        campos_maquina.forEach((campo, j)=>{
            nombre_campo = nombre+campo
            if(campos_no_tiempo.includes(campo)){
                suma = filas.map(x=>x[nombre_campo]).reduce((a,b)=>a+b)
            }
            else{
                suma = seconds_to_strTime(filas.map(x=>get_seconds(x[nombre_campo])).reduce((a,b)=>a+b))
            }
            sumatoria.push(suma)
        })
    })
    //Al final metemos las sumatorias que no dependan del número de máquinas
    suma = seconds_to_strTime(filas.map(x=>get_seconds(x['Equipo total horas'])).reduce((a,b)=>a+b))
    sumatoria.push(suma)

    //Creamos el segundo resumen de datos
    const resumen = [[]]
    const resumen_por_maquina = ['TIEMPO CORTANDO', 'PORCENTAJE CORTANDO', 'CHAPAS CORTADAS']
    let t_todas_cortando_segundos = 0
    let t_todas_total_segundos = 0
    let n_chapas_todas = 0
    nombre_maquinas.forEach((nombre_maquina, i)=>{
        datos = [nombre_maquina]
        index_maquina = 8*i
        t_cortando = sumatoria[index_maquina+2]
        t_cortando_segundos = get_seconds(t_cortando)
        t_total = sumatoria[index_maquina]
        t_total_segundos = get_seconds(t_total)
        t_todas_cortando_segundos += t_cortando_segundos
        t_todas_total_segundos += t_total_segundos
        p_cortando = t_cortando_segundos / t_total_segundos * 100
        n_chapas = sumatoria[index_maquina+7]
        n_chapas_todas += n_chapas
        resumen_por_maquina.forEach((nombre_campo, j)=>{
            if(nombre_campo === 'TIEMPO CORTANDO'){
                datos.push(t_cortando)
            }
            else if(nombre_campo === 'PORCENTAJE CORTANDO'){
                datos.push(roundToTwo(p_cortando))
            }
            else if(nombre_campo === 'CHAPAS CORTADAS'){
                datos.push(n_chapas)
            }
        })
        resumen[0].push(datos)
    })
    //Ahora sacamos el resumen de todas las máquinas
    const n_dias = filas.length
    const t_todas_cortando = seconds_to_strTime(t_todas_cortando_segundos)
    const p_todas = roundToTwo(t_todas_cortando_segundos / t_todas_total_segundos * 100)
    const chapas_dia = Math.round(n_chapas_todas/n_dias)
    const COSTE_HORA = 25.00
    const coste_por_hora = roundToTwo((COSTE_HORA*get_seconds(sumatoria.at(-1))/3600)/(t_todas_cortando_segundos/3600))
    const productivad = p_todas + chapas_dia
    resumen.push([t_todas_cortando, p_todas,n_chapas_todas,chapas_dia], COSTE_HORA, coste_por_hora, productivad)

    //Finalmente todos los días que para todas sus máquinas tengan 0 horas encendidas los dejamos en blanco
    filas.forEach(registro=>{
        horas = []
        nombre_maquinas.forEach((nombre, i)=>{
            horas.push(registro[nombre+' encendida'])
        })
        todo_cero = horas.every(hora=>{
            return hora === '00:00:00'
        })
        if(todo_cero){
            nombre_maquinas.forEach((nombre, i)=>{
                registro[nombre+' cortando'] = ''
                registro[nombre+' parada pausa'] = ''
                registro[nombre+' parada error'] = ''
                registro[nombre+' parada mant.'] = ''
                registro[nombre+' parada total'] = ''
                registro[nombre+' chapas cortadas'] = 0
            }) 
        }
    })
    return [filas, sumatoria, resumen]
}

function tabla_rendimiento(ID, data){
    const table = document.getElementById(ID)
    while (table.lastElementChild) {
        table.removeChild(table.lastElementChild);
    }
    
    //setting up thead
    let cols_names = Array.from(new Set(data.map(Object.keys).flat()))
    const thead_elem = document.createElement('thead')
    const tr_elem = document.createElement('tr')
    let class_name = 'even'
    let last_name = cols_names[0].split(" ")[0]
    cols_names.forEach(col_name => {
        const th_elem = document.createElement('th')
        col_text_words = col_name.split(" ")
        col_name_header = col_text_words[0]
        col_name_tail = col_text_words.slice(1).join(" ")
        if(last_name !== col_name_header){
            class_name = class_name !==  'even' ? 'even' : 'odd'
        }
        last_name = col_name_header
        th_elem.className = class_name
        th_elem.innerHTML = `<span class='table_header_first_word'>${col_name_header}</span><br>${col_name_tail}`
        tr_elem.appendChild(th_elem)
    })
    thead_elem.appendChild(tr_elem)
    table.appendChild(thead_elem)
    
    //setting up tbody
    const tbody_elem = document.createElement('tbody')
    data.forEach(row_data=>{
        const tr_elem = document.createElement('tr')
        cols_names.forEach(field=>{
            const td_elem = document.createElement('td')
            td_elem.className = 'turno_celda'
            if(field === 'Turno'){
                const turno0 = document.createElement('div')
                turno0.innerHTML = 'Mañana'

                const turno1 = document.createElement('div')
                turno1.innerHTML = 'Tarde'

                const turno2 = document.createElement('div')
                turno2.innerHTML = 'Noche'
                const turnos = [turno0, turno1, turno2]
                const turno_index = row_data['Turno']-1
                if(turno_index>-1){
                    turnos[turno_index].className = 'turno_seleccionado'
                }

                turnos.forEach(x=>{
                    td_elem.appendChild(x)
                })
            }
            else{
                if(row_data.hasOwnProperty(field)){
                    if(typeof row_data[field] === 'object' &&
                    row_data[field] !== null){
                        td_elem.innerHTML = JSON.stringify(row_data[field])
                    }
                    else{
                        td_elem.innerHTML = row_data[field]
                    }
                }
                else{
                    if(field.split(" ")[1] === 'apagada'){
                        td_elem.innerHTML = '08:00:00'
                    }
                    else if(field.split(" ")[1] === 'chapas'){
                        td_elem.innerHTML = '0'
                    }
                    else{
                        td_elem.innerHTML = '00:00:00'
                    }
                    
                }
            }
            
            tr_elem.appendChild(td_elem)
        })
        tbody_elem.appendChild(tr_elem)
    })
    table.appendChild(tbody_elem)
}

function append_summary_row(ID, summary_data){
    const table = document.getElementById(ID)
    const tr_elem = document.createElement('tr')
    tr_elem.className = 'summary_row'
    let td_elem = document.createElement('td')
    td_elem.innerHTML = 'TOTAL'
    tr_elem.appendChild(td_elem)
    td_elem = document.createElement('td')
    tr_elem.appendChild(td_elem)
    summary_data.forEach(x=>{
        td_elem = document.createElement('td')
        td_elem.innerHTML = x
        tr_elem.appendChild(td_elem)
    })
    table.appendChild(tr_elem)
}

function rendimiento_cabecera(ID, summary_data){
    const container = document.getElementById(ID)
    while (container.lastElementChild) {
        container.removeChild(container.lastElementChild);
    }

    const grupo_container = document.createElement('div')
    grupo_container.innerHTML = `
        GRUPO DE CORTE LASER: <span> ${summary_data['nombre']} </span>
    `.trim()
    container.appendChild(grupo_container)

    const trabajadores_container = document.createElement('div')
    const trabajadores_list = document.createElement('ul')
    summary_data['trabajadores'].forEach(x=>{
        const trabajador_item = document.createElement('li')
        trabajador_item.innerHTML = x
        trabajadores_list.appendChild(trabajador_item)
    })
    trabajadores_container.appendChild(trabajadores_list)
    container.appendChild(trabajadores_container)
    
    const fecha_container = document.createElement('div')
    fecha_container.innerHTML = summary_data['fecha']
    container.appendChild(fecha_container)
}

function rendimiento_summary(ID, summary_data){
    const container = document.getElementById(ID)
    while (container.lastElementChild) {
        container.removeChild(container.lastElementChild)
    }
    datos_maquinas = summary_data.shift()
    datos_maquinas.forEach(maquina=>{
        html_code = `
            <tr>
                <td>TIEMPO CORTANDO <span class='bolder'>${maquina[0]}</span></td>
                <td>${maquina[1]}</td>
                <td>HH:MM:SS</td>
            </tr>
            <tr>
                <td>PORCENTAJE CORTANDO <span class='bolder'>${maquina[0]}</span></td>
                <td>${maquina[2]}</td>
                <td>%</td>
                <td>${maquina[3]}</td>
                <td>CHAPAS CORTADAS <span class='bolder'>${maquina[0]}</span></td>
            </tr>
        `
        container.innerHTML += html_code
    })

    resumen_maquinas = summary_data.shift()
    str_suma_maquinas = datos_maquinas.map(x=>x[0]).join('+')
    html_code = `
        <tr>
            <td>TIEMPO CORTANDO <span class='bolder'>${str_suma_maquinas}</span></td>
            <td>${resumen_maquinas[0]}</td>
            <td>HH:MM:SS</td>
        </tr>
        <tr>
            <td>PORCENTAJE CORTANDO <span class='bolder'>${str_suma_maquinas}</span></td>
            <td>${resumen_maquinas[1]}</td>
            <td>%</td>
            <td>${resumen_maquinas[2]}</td>
            <td>CHAPAS CORTADAS <span class='bolder'>${str_suma_maquinas}</span></td>
            <td>${resumen_maquinas[3]}</td>
            <td>CHAPAS CORTADAS DE MEDIA/DIA</td>
        </tr>
        <tr>
            <td>COSTE LABORAL POR DÍA</td>
            <td>${summary_data[0]}</td>
            <td>€</td>
        </tr>
        <tr>
            <td>TOTAL COSTE LABORAL POR HORA</td>
            <td>${summary_data[1]}</td>
            <td>€/h</td>
        </tr>
        <tr>
            <td><span class='bolder bigger'>PRODUCTIVIDAD</span></td>
            <td>${summary_data[2]}</td>
            <td>%</td>
        </tr>
    `
    container.innerHTML += html_code

    /*summary_data.forEach(row=>{
        const tr_elem = document.createElement('tr')
        row.forEach(col=>{
            const td_elem = document.createElement('td')
            td_elem.innerHTML = col
            tr_elem.appendChild(td_elem)
        })
        container.appendChild(tr_elem)
    })*/
}