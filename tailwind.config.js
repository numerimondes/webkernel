/** @type {import('tailwindcss').Config} */
export default {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
      './storage/framework/views/*.php',
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      './webkernel/**/*.blade.php',
      './webkernel/**/*.js',
      './webkernel/**/*.vue',
      './webkernel/**/*.php',
      './platform/**/*.blade.php',
      './platform/**/*.js',
      './platform/**/*.vue',
      './platform/**/*.php',
      // Force scan all subdirectories recursively
      './webkernel/**/**/*.blade.php',
      './webkernel/**/**/*.js',
      './webkernel/**/**/*.vue',
      './webkernel/**/**/*.php',
      './platform/**/**/*.blade.php',
      './platform/**/**/*.js',
      './platform/**/**/*.vue',
      './platform/**/**/*.php',
    ],
    safelist: [
      {
        pattern: /.*/,
      },
    ],
    theme: {
      extend: {
        fontFamily: {
          sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        },
      },
    },
    plugins: [],
  };
