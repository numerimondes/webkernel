;(function ($) {
  "use strict";


    window.App = {};
    App.config = {
      headroom: {
        enabled: true,
        options: {
          classes : {
            initial : "headroom",
            pinned : "is-pinned",
            unpinned : "is-unpinned",
            top : "is-top",
            notTop : "is-not-top",
            bottom : "is-bottom",
            notBottom : "is-not-bottom",
            frozen: "is-frozen",
          },
        }
      },
      ajax: {
        enabled: true,
      },
      cursorFollower: {
        enabled: true,
        disableBreakpoint: '992',
      },
    }

    App.html = document.querySelector('html');
    App.body = document.querySelector('body');

    window.onload = function () {

      if (App.config.cursorFollower.enabled) {
        Cursor.init();
      }
      

    }

    const Cursor = (function() {

    const cursor = document.querySelector(".tx-js-cursor");
    let follower;
    let label;
    let icon;

    let clientX;
    let clientY;
    let cursorWidth;
    let cursorHeight;
    let cursorTriggers;
    let state;

    function variables() {

      follower = cursor.querySelector(".tx-js-follower");
      label = cursor.querySelector(".tx-js-label");
      icon = cursor.querySelector(".tx-js-icon");

      clientX = -100;
      clientY = -100;
      cursorWidth = cursor.offsetWidth / 2;
      cursorHeight = cursor.offsetHeight / 2;
      cursorTriggers;
      state = false;

    }

    function init() {

      if (!cursor) return;

      variables();
      state = true;
      cursor.classList.add('is-enabled');

      document.addEventListener("mousedown", e => {
        cursor.classList.add('is-mouse-down');
      });

      document.addEventListener("mouseup", e => {
        cursor.classList.remove('is-mouse-down');
      });

      document.addEventListener("mousemove", (event) => {
        clientX = event.clientX;
        clientY = event.clientY;
      });

      const render = () => {
        cursor.style.transform = `translate(${clientX - cursorWidth}px, ${clientY - cursorHeight}px)`;
        requestAnimationFrame(render);
      };

      requestAnimationFrame(render);

      update();
      breakpoint();

    }

    function enterHandler({ target }) {

      cursor.classList.add('is-active');

      if (target.getAttribute('data-cursor-label')) {
        App.body.classList.add('is-cursor-active');
        cursor.classList.add('has-label');
        label.innerHTML = target.getAttribute('data-cursor-label');
      }

      if (target.getAttribute('data-cursor-icon')) {
        App.body.classList.add('is-cursor-active');
        cursor.classList.add('has-icon');
        const iconAttr = target.getAttribute('data-cursor-icon');
      }

    }
    
    function leaveHandler({ target }) {

      App.body.classList.remove('is-cursor-active');
      cursor.classList.remove('is-active');
      cursor.classList.remove('has-label');
      cursor.classList.remove('has-icon');

    }

    function update() {

      if (!cursor) return;

      cursorTriggers = document.querySelectorAll([
        "button",
        "a",
        "input",
        "[data-cursor]",
        "[data-cursor-label]",
        "[data-cursor-icon]",
        "textarea",
        ".progress-wrap",
        ".service-button-next",
        ".service-button-prev",
        ".tx-swiper-arrow",
        ".invite-navigation .tp-bullet"
      ]);
      
      cursorTriggers.forEach(el => {
        el.addEventListener("mouseenter", enterHandler);
        el.addEventListener("mouseleave", leaveHandler);
      });

    }

    function clear() {

      if (!cursor) return;
      
      cursorTriggers.forEach(el => {
        el.removeEventListener("mouseenter", enterHandler);
        el.removeEventListener("mouseleave", leaveHandler);
      });

    }

    function hide() {

      if (!cursor) return;
      cursor.classList.add('is-hidden');

    }

    function show() {

      if (!cursor) return;
      cursor.classList.remove('is-hidden');

    }

    function breakpoint() {

      if (!state) return;
      if (!App.config.cursorFollower.disableBreakpoint) return;

      let width = (window.innerWidth > 0) ? window.innerWidth : screen.width;

      if (width < App.config.cursorFollower.disableBreakpoint) {
        state = false;
        cursor.classList.remove('is-enabled');
        clear();
      } else {
        state = true;
        cursor.classList.add('is-enabled');
        update();
      }

      window.addEventListener('resize', () => {
        let width = (window.innerWidth > 0) ? window.innerWidth : screen.width;

        if (width < App.config.cursorFollower.disableBreakpoint) {
          state = false;
          cursor.classList.remove('is-enabled');
          clear();
        } else {
          state = true;
          cursor.classList.add('is-enabled');
          update();
        }
      })

    }

    return {
      init: init,
      update: update,
      clear: clear,
      hide: hide,
      show: show,
    };

  })();
})(jQuery);