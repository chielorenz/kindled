// Load all images from assets/images to build filder
const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

document.addEventListener('DOMContentLoaded', function () {
  loadCookieConsens();
  toggleBurgers();
  hideMessages();
  autoHideMessages();
  loadableButtons();
});

// Load cookieconsent component
loadCookieConsens = function() {
  window.cookieconsent.initialise({
    "palette": {
      "popup": { "background": "#ffffff", "text": "#46237a" },
      "button": { "background": "#46237a", "text": "#ffffff" }
    },
    "theme": "classic",
    "content": { "dismiss": "GOT IT!" }
  });
}

// Toggle burger menu
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

// Hide toast messages after 5 seconds
autoHideMessages = function() {
  setTimeout(function() { 
    $messages = document.querySelectorAll('.message');
    $messages.forEach(function ($el) { 
      $el.classList.toggle('is-hidden');
    });
  }, 5000);
}

// Make the 'loadable' buttons show spinner when pressed
loadableButtons = function() {
  $buttons = document.querySelectorAll('.is-loadable');
  $buttons.forEach(function ($el) { 
    $el.addEventListener('click', function () {
      $el.classList.add('is-loading');
      setTimeout(function(){ $el.classList.remove('is-loading'); }, 5000);
    });
  });
}