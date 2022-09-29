const datos_mes = [
    fila('01-09-22'),
    fila('02-09-22'),
    fila('03-09-22'),
    fila('04-09-22'),
    fila('05-09-22'),
    fila('06-09-22'),
    fila('07-09-22'),
    fila('08-09-22'),
    fila('09-09-22'),
    fila('10-09-22'),
    fila('11-09-22'),
    fila('12-09-22'),
    fila('13-09-22'),
    fila('14-09-22'),
    fila('15-09-22'),
    fila('16-09-22'),
    fila('17-09-22'),
    fila('18-09-22'),
    fila('19-09-22'),
    fila('20-09-22'),
    fila('21-09-22'),
    fila('22-09-22'),
    fila('23-09-22'),
    fila('24-09-22'),
    fila('25-09-22'),
    fila('26-09-22'),
    fila('27-09-22'),
    fila('28-09-22'),
    fila('29-09-22'),
    fila('30-09-22')
]

function fila(fecha){
    return {
        'Fecha': fecha, 
        'Turno': 0,
        'L76-01 ENCENDIDA': '8:00:00',
        'L76-01 APAGADA': '0:00:00',
        'L76-01 CORTANDO': '6:07:21',
        'L76-01 PARADA PAUSA': '1:52:39',
        'L76-01 PARADA ERROR': '0:00:00',
        'L76-01 PARADA MANTE': '0:00:00',
        'L76-01 PARADA TOTAL': '1:52:39',
        'L76-01 CHAPAS CORTADAS': '30',
        'L76-02 ENCENDIDA': '8:00:00',
        'L76-02 APAGADA': '0:00:00',
        'L76-02 CORTANDO': '6:07:21',
        'L76-02 PARADA PAUSA': '1:52:39',
        'L76-02 PARADA ERROR': '0:00:00',
        'L76-02 PARADA MANTE': '0:00:00',
        'L76-02 PARADA TOTAL': '1:52:39',
        'L76-02 CHAPAS CORTADAS': '30',
        'EQUIPO TOTAL HORAS': '24:00:00'
    }
}

const summary_row_old = ['97:07:02', '126:52:58', '48:08:09', '42:59:58', '5:58:55', '0:00:00', '48:58:53', '446','97:07:02', '126:52:58', '48:08:09', '42:59:58', '5:58:55', '0:00:00', '48:58:53', '446', '296:00:00']

const summary_data = [
    ['TIEMPO CORTANDO <span class="bolder">L76 01</span>', '58:06:22',  'HH:MM:SS'],
    ['PORCENTAJE CORTANDO <span class="bolder">L76 01</span>', '51.37',  '%', 489, 'CHAPAS CORTADAS L79 01'],
    ['TIEMPO CORTANDO <span class="bolder">L76 02</span>', '55:04:12',  'HH:MM:SS'],
    ['PORCENTAJE CORTANDO <span class="bolder">L76 02</span>', '50.16',  '%', 363, 'CHAPAS CORTADAS L79 02'],
    ['TIEMPO CORTANDO <span class="bolder">L76 01+L76 02</span>', '113:11:03',  'HH:MM:SS'],
    ['PORCENTAJE CORTANDO <span class="bolder">L76 01+L76 02</span>', '50.77',  '%', 852, 'CHAPAS CORTADAS L76 01+L76 02', 47, 'CHAPAS CORTADAS DE MEDIA/DÍA'],
    ['COSTE LABORAL POR HORA', '25.00', '€'],
    ['TOTAL COSTE LABORAL POR HORA', '72.45', '€/h'],
    ['<span class="bolder bigger">PRODUCTIVIDAD</span>', '98.10', '%'],
]


const group_data = {
    'nombre': 'GRANADA 1',
    'trabajadores': ['RUBÉN CORTÉS', 'FERNANDO TOVAR', 'JOSÉ MIGEL ASENSIO'],
    'fecha': '01/08/2022 - 28/08/2022'
}
