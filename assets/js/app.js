// Load all images from assets/images to build filder
const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

document.addEventListener('DOMContentLoaded', function () {
  toggleBurgers();
  hideMessages();
  loadableButtons();
});

// Toggle navbar/burger menu
toggleBurgers = function() {
  var $navbarBurgers = document.querySelectorAll('.navbar-burger');
  $navbarBurgers.forEach(function ($el) {
    $el.addEventListener('click', function () {
      var target = $el.dataset.target;
      var $target = document.getElementById(target);
      $el.classList.toggle('is-active');
      $target.classList.toggle('is-active');
    });
  });
}

// Hide toast messages
hideMessages = function() {
  $messages = document.querySelectorAll('.message button.delete');
  $messages.forEach(function ($el) { 
    $el.addEventListener('click', function () {
      $el.parentElement.parentElement.classList.toggle('is-hidden');
    });
  });
}

// Make the 'loadable' buttons show spinner when pressed
loadableButtons = function() {
  $buttons = document.querySelectorAll('.is-loadable');
  $buttons.forEach(function ($el) { 
    $el.addEventListener('click', function () {
      $el.classList.add('is-loading');
    });
  });
}