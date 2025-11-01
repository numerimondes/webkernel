// WebkernelBuilder Theme Initialization
(function() {
    'use strict';

    // Force dark mode immediately to prevent flickering
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');

    // Add WebkernelBuilder root class
    document.documentElement.classList.add('wkb-root');

    console.log('WebkernelBuilder theme initialized');
})();
