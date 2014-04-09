function getPage(url, callback) {
  var request = new XMLHttpRequest();
  request.open('GET', url, true);

  request.onreadystatechange = function() {
    if (this.readyState === 4){
      if (this.status >= 200 && this.status < 400){
        callback(this.responseText, false);
      } else {
        callback(this.responseText, true);
      }
    }
  };
  request.send();
  request = null;
}

function highlight(parent) {
  var items = parent.querySelectorAll('pre code');
  for (var i = items.length - 1; i >= 0; i--) {
    hljs.highlightBlock(items[i]);
  }
}

function homeFn() {
  var pages = [
    {
      file: '[HOW-TO]-Installation.html',
      id: 'installation'
    },
    {
      file: '[COMMUNITY]-Sites-using-Phile.html',
      id: 'sites'
    },
    {
      file: '[DEVELOPER]-Developer-Guidelines.html',
      id: 'developer'
    },
    {
      file: '[COMMUNITY]-Plugins.html',
      id: 'plugins'
    },
  ];
  pages.forEach(function(item) {
    getPage('html/' + item.file, function(res, err) {
      if (err) {
        console.error(err, res);
      }
      var elem = document.getElementById(item.id);
      elem.innerHTML = res;
      highlight(elem);
    });
  });
}

var scroller = '', interior = '';

// shim layer with setTimeout fallback
window.requestAnimFrame = (function () {
  return window.requestAnimationFrame ||
  window.webkitRequestAnimationFrame ||
  window.mozRequestAnimationFrame ||
  window.oRequestAnimationFrame ||
  window.msRequestAnimationFrame ||
  function (callback) {
    window.setTimeout(callback, 1000 / 60);
  };
})();

document.addEventListener('DOMContentLoaded', function() {
  if (document.body.className == 'home-page') {
    homeFn();
  } else if(document.body.className == 'docs-page') {
    hljs.initHighlightingOnLoad();
  }
  scroller = document.getElementsByClassName('big-heading')[0];
  interior = document.getElementsByClassName('header-inside')[0];
});

function updateBg() {
  var size = (this.pageYOffset > 0) ? this.pageYOffset: 0;
  var o = (1 - (this.pageYOffset / 800)).toFixed(2);
  if (size < 1000) {
    scroller.style.backgroundSize = 100+size+'%';
  }
  if (o > 0) {
    interior.style.opacity = o;
  }
}

window.addEventListener('mousewheel', function() {
  requestAnimFrame(updateBg);
});
