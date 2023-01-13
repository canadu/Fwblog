let navbar = document.querySelector('.header .flex .navbar');

const menu_btn = document.querySelector('#menu-btn')
if (menu_btn) {
  menu_btn.addEventListener('click', () => {
    navbar.classList.toggle('active');
    searchForm.classList.remove('active');
    profile.classList.remove('active');
  })
}

let profile = document.querySelector('.header .flex .profile');

const user_btn = document.querySelector('#user-btn')
if (user_btn) {
  user_btn.addEventListener('click', () => {
    profile.classList.toggle('active');
    searchForm.classList.remove('active');
    navbar.classList.remove('active')
  })
}

let searchForm = document.querySelector('.header .flex .search-form');

const search_btn = document.querySelector('#search-btn')
if (search_btn) {
  search_btn.addEventListener('click', () => {
    searchForm.classList.toggle('active');
    navbar.classList.remove('active');
    profile.classList.remove('active');
  })
}

window.onscroll = () => {
  if (profile != null) {
    if (profile.classList.contains('active')) {
      profile.classList.remove('active');
    }
  }
  if (navbar != null) {
    if (navbar.classList.contains('active')) {
      navbar.classList.remove('active');
    }
  }
  if (searchForm != null) {
    if (searchForm.classList.contains('active')) {
      searchForm.classList.remove('active');
    }
  }
}

document.querySelectorAll('.content-150').forEach(content => {
  if (content.innerHTML.length > 150) content.innerHTML = content.innerHTML.slice(0, 150);
});