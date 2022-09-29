// Given an array of objects and an ID of an HTML table, generates the table

function btables_generate_table(ID, data){
    const table = document.getElementById(ID)
    while (table.lastElementChild) {
        table.removeChild(table.lastElementChild);
    }
    
    //setting up thead
    cols_names = Array.from(new Set(data.map(Object.keys).flat()))
    const thead_elem = document.createElement('thead')
    const tr_elem = document.createElement('tr')
    cols_names.forEach(col_name => {
        const td_elem = document.createElement('th')
        td_elem.innerHTML = col_name
        tr_elem.appendChild(td_elem)
    })
    thead_elem.appendChild(tr_elem)
    table.appendChild(thead_elem)
    
    //setting up tbody
    const tbody_elem = document.createElement('tbody')
    data.forEach(row_data=>{
        const tr_elem = document.createElement('tr')
        cols_names.forEach(field=>{
            const td_elem = document.createElement('td')
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
                td_elem.innerHTML = '---'
            }
            tr_elem.appendChild(td_elem)
        })
        tbody_elem.appendChild(tr_elem)
    })
    table.appendChild(tbody_elem)
}