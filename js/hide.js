'use strict'
const items = document.querySelectorAll('.offl__item');
const button = document.querySelector('#offl_show_all_btn');

for (let i = positions.number; i < items.length; i++) {
	items[i].style.display = 'none';
}

if (items.length > positions.number) {
	button.style.display = 'block';
}

button.addEventListener('click', ()=>{
	items.forEach((item, index)=>{
		item.style.display = 'list-item';
	});
	button.style.display = 'none';
});