function bdates_today_iso(){
    return new Date().toISOString().split("T")[0];
}

function bdates_firstDay_currentMonth(){
    const now = new Date()
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
    return firstDay;
}

function bdates_date_to_iso(date){
    return date.toISOString().split("T")[0];
}

function bdates_sql_friendly(date){
        var d = date.getDate();
        var m = date.getMonth() + 1; //Month from 0 to 11
        var y = date.getFullYear();
        return '' + y + '-' + (m<=9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
}

function bdates_get_range(start, end) {
    const start_date = new Date(start)
    const end_date = new Date(end)
    let c = start_date
    const range = []
    while(c <= end_date){
        range.push(new Date(c))
        c.setDate(c.getDate()+1)
    }
    return range
}

function get_seconds(time_str){
    if(typeof time_str === 'string'){
        parts = time_str.split(':')
        parts = parts.map(x=>parseInt(x))
        return parts[2]+parts[1]*60+parts[0]*3600
    }
    else{
        return 0;
    }
}

function seconds_to_strTime(seconds){
    horas = Math.floor(seconds/3600)
    resto = seconds % 3600
    minutos = Math.floor(resto/60)
    segundos = resto % 60
    fillers = [
        horas<10 ? '0' : '',
        minutos<10 ? '0' : '',
        segundos<10 ? '0' : ''
    ]
    cadena = [[fillers[0]+horas], [fillers[1]+minutos], [fillers[2]+segundos]].join(':')
    return cadena
}