let header = document.querySelector('.header');

const menu_btn = document.querySelector('#menu-btn')
if (menu_btn) {
    menu_btn.addEventListener('click', () => {
        header.classList.toggle('active');
    })
}

window.onscroll = () => {
    if (header != null) {
        if (header.classList.contains('active')) {
            header.classList.remove('active');
        }
    }
}

document.querySelectorAll('.posts-content').forEach(content => {
    if (content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
});