<?php
$primary_color = '#131b28fb';  // couleur principale
$secondary_color = '{{ $secondary_color }}'; // couleur secondaire
?>

<style>
/* Datepicker active states */
.daterangepicker .ranges li.active,
.daterangepicker td.active,
.daterangepicker td.active:hover {
    background-color: <?php echo e($primary_color); ?>;
}

/* Quill editor hover */
.ql-toolbar span:hover {
    color: <?php echo e($primary_color); ?> !important;
}

/* Background colors */
.bg-primary {
    background-color: <?php echo e($primary_color); ?> !important;
    border: 1px solid <?php echo e($primary_color); ?> !important;
    color: #fff;
}

.bg-primary.bg-opacity-100 {
    background-color: <?php echo e($primary_color); ?> !important;
}

.bg-secondary {
    background-color: <?php echo e($secondary_color); ?> !important;
}

/* Soft backgrounds */
.bg-soft-primary {
    background-color: #fbdcd7;
    color: <?php echo e($primary_color); ?>;
}

.bg-soft-secondary {
    background-color: #d4e8c9;
    color: <?php echo e($secondary_color); ?>;
}

/* Navigation styles */
.nav.nav-style-1 .nav-link.active,
.nav-link.active,
.nav-pills .nav-link.active,
.nav-pills .show > .nav-link,
.navbar-nav .nav-link.active,
.navbar-nav .show > .nav-link,
.nav-tabs.nav-style-5 .nav-link.active,
.nav-tabs.nav-style-5 .nav-link:hover,
.nav-tabs.nav-style-5 .nav-link:focus {
    background-color: <?php echo e($primary_color); ?>;
    color: #fff;
    border: 0 !important;
}

.nav.nav-style-2 .nav-item .nav-link.active,
.nav-style-6.nav-pills .nav-link.active,
.nav.nav-style-3 .nav-link.active {
    border: 1px solid <?php echo e($primary_color); ?>;
    border-block-start: 2px solid <?php echo e($primary_color); ?>;
    border-block-end: 3px solid <?php echo e($primary_color); ?>;
    background-color: transparent;
    color: <?php echo e($primary_color); ?>;
}

.nav.nav-style-1 .nav-link:hover {
    background: transparent;
    color: <?php echo e($primary_color); ?>;
}

/* Banner and sections */
.dark-mode .banner-form-tab-six .nav li .nav-link.active,
.banner-form-tab-six .nav li .nav-link.active,
.banner-form-tab-six .nav li .nav-link:hover,
.banner-form-tab .nav li .nav-link.active,
.banner-form-tab .nav li .nav-link:hover,
.banner-form .nav li .nav-link.active,
.banner-form .nav li .nav-link:hover,
.section-header-six .badge::before,
.rounded-arrow-next:hover {
    background: <?php echo e($primary_color); ?>;
    color: #fff;
}

/* Buttons */
.btn-primary,
.btn-check:checked + .btn,
.btn.active,
.btn.show,
.btn.show:hover,
.btn:first-child:active,
:not(.btn-check) + .btn:active,
.btn.btn-primary,
.btn.btn-primary:hover,
.btn.btn-primary:focus,
.btn.btn-primary.focus,
.btn.btn-primary:active,
.btn.btn-primary.active {
    background-color: <?php echo e($primary_color); ?>;
    border: 1px solid <?php echo e($primary_color); ?>;
    color: #fff;
}

.btn-secondary,
.btn.btn-secondary,
.btn.btn-secondary:hover,
.btn.btn-secondary:focus,
.btn.btn-secondary.focus,
.btn.btn-secondary:active,
.btn.btn-secondary.active {
    background-color: <?php echo e($secondary_color); ?>;
    border: 1px solid <?php echo e($secondary_color); ?>;
    color: #fff;
}

.btn-primary-light,
.btn-primary-light:hover,
.btn-primary-light:focus,
.btn-primary-light:active,
.btn-primary-ghost,
.btn-primary-ghost:active,
.btn-outline-primary {
    background-color: transparent;
    color: <?php echo e($primary_color); ?>;
    border: 1px solid <?php echo e($primary_color); ?>;
}

.btn-primary-light:hover,
.btn-primary-light:focus,
.btn-primary-light:active {
    background-color: <?php echo e($primary_color); ?>;
    color: #fff;
    border-color: <?php echo e($primary_color); ?>;
}

.btn-secondary-light,
.btn-secondary-light:hover,
.btn-secondary-light:focus,
.btn-secondary-light:active,
.btn-secondary-ghost,
.btn-secondary-ghost:active {
    background-color: transparent;
    color: <?php echo e($secondary_color); ?>;
    border: 1px solid <?php echo e($secondary_color); ?>;
}

.btn-secondary-light:hover,
.btn-secondary-light:focus,
.btn-secondary-light:active {
    background-color: <?php echo e($secondary_color); ?>;
    color: #fff;
    border-color: <?php echo e($secondary_color); ?>;
}

footer .footer-top .footer-widget h5::before {
  content: '';
  position: absolute;
  bottom: -6px;
  left: 0;
  width: 38px;
  height: 2px;
  background: <?php echo e($primary_color); ?>;
  border-radius: 30px;
}


.text-primary {
  color: <?php echo e($primary_color); ?> !important;
  opacity: 1;
}


.form-check-input:checked {
  background-color: <?php echo e($primary_color); ?>;
  border-color: <?php echo e($primary_color); ?>;
}
.form-check-input.form-checked-outline:checked {
  background-color: transparent;
  border-color: <?php echo e($primary_color); ?>;
}
.form-check-input.form-checked-secondary:checked {
  background-color: <?php echo e($secondary_color); ?>;
  border-color: <?php echo e($secondary_color); ?>;
}

.badge.badge-primary {
  background: <?php echo e($primary_color); ?>;
  color: #fff;
}
.badge.badge-soft-primary {
  background: rgba(207, 52, 37, 0.1);
  color: <?php echo e($primary_color); ?>;
}

.badge.badge-secondary {
  background: <?php echo e($secondary_color); ?>;
  color: #fff;
}
.badge.badge-soft-secondary {
  background: rgba(152, 170, 48, 0.1);
  color: <?php echo e($secondary_color); ?>;
}

</style>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/platform/EnjoyTheWorld/Resources/Views/software/app-elements/head-styles.blade.php ENDPATH**/ ?>