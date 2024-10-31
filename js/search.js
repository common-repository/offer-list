'use strict';
const search_input = document.querySelector(`#offl__search`);

function search(filter) {
    let items = document.querySelectorAll(`.offl__item`);
    filter = filter.toUpperCase();
    for (let i = 0; i < items.length; i++) {
        let textVal = items[i].querySelector(`.offl__content`).innerText;
        if (textVal.toUpperCase().indexOf(filter) > -1) {
            items[i].style.display = "";
        } else {
            items[i].style.display = "none";
        }
    }
    if (filter.length == 0) {
        for (let i = positions.number; i < document.querySelectorAll('.offl__item').length; i++) {
            document.querySelectorAll('.offl__item')[i].style.display = 'none';
        }
        document.querySelector('#offl_show_all_btn').style.display = 'block';
    }
}

search_input.addEventListener('input', ()=>{
    search(search_input.value);
});