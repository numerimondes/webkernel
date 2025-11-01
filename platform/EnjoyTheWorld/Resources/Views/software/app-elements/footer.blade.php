@php
/**
 * Footer component - single Blade file
 */

$footerMenus = $footerMenus ?? [
    [
        'title' => 'Pages',
        'links' => [
            ['label' => 'Our Team', 'url' => '/team'],
            ['label' => 'Pricing Plans', 'url' => '/pricing-plan'],
            ['label' => 'Gallery', 'url' => '/gallery'],
            ['label' => 'Settings', 'url' => '/profile-settings'],
            ['label' => 'Profile', 'url' => '/my-profile'],
            ['label' => 'Listings', 'url' => '/agent-listings'],
        ]
    ],
    [
        'title' => 'Company',
        'links' => [
            ['label' => 'About Us', 'url' => '/about-us'],
            ['label' => 'Careers', 'url' => '#'],
            ['label' => 'Blog', 'url' => '/blog-grid'],
            ['label' => 'Affiliate Program', 'url' => '#'],
            ['label' => 'Add Your Listing', 'url' => '/add-flight'],
            ['label' => 'Our Partners', 'url' => '#'],
        ]
    ],
    [
    'title' => 'Destinations',
    'links' => [
        ['label' => 'Marigot', 'url' => '#'],
        ['label' => 'Philipsburg', 'url' => '#'],
        ['label' => 'Grand Case', 'url' => '#'],
        ['label' => 'Orient Bay', 'url' => '#'],
        ['label' => 'Simpson Bay', 'url' => '#'],
        ['label' => 'Maho Beach', 'url' => '#'],
    ]

    ],
    [
        'title' => 'Support',
        'links' => [
            ['label' => 'Contact Us', 'url' => '/contact-us'],
            ['label' => 'Legal Notice', 'url' => '#'],
            ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
            ['label' => 'Terms and Conditions', 'url' => '/terms-conditions'],
            ['label' => 'Chat Support', 'url' => '/chat'],
            ['label' => 'Refund Policy', 'url' => '#'],
        ]
    ],
    [
        'title' => 'Services',
        'links' => [
            ['label' => 'Hotel', 'url' => '/hotel-grid'],
            ['label' => 'Activity Finder', 'url' => '#'],
            ['label' => 'Flight Finder', 'url' => '/flight-grid'],
            ['label' => 'Holiday Rental', 'url' => '/tour-grid'],
            ['label' => 'Car Rental', 'url' => '/car-grid'],
            ['label' => 'Holiday Packages', 'url' => '/tour-details'],
        ]
    ],
];

$footerStores = $footerStores ?? [
    'show' => false,
    'items' => [
        ['enabled' => true, 'img' => '/assets/img/icons/googleplay.svg', 'url' => '#'],
        ['enabled' => true, 'img' => '/assets/img/icons/appstore.svg', 'url' => '#'],
    ]
];

$footerContacts = $footerContacts ?? [
    [
        'label' => 'Customer Support',
        'value' => '+1 56589 54598',
        'icon'  => 'ti ti-headphones-filled',
        'bg'    => 'bg-primary',
        'is_email' => false,
    ],
    [
        'label' => 'Drop Us an Email',
        'value' => '[email protected]',
        'icon'  => 'ti ti-message',
        'bg'    => 'bg-secondary',
        'is_email' => true,
    ],
];
@endphp

<!-- Footer -->
<footer>
   <div class="container">
      <div class="footer-top">
         <div class="row row-cols-lg-5 row-cols-md-3 row-cols-sm-2 row-cols-1">
            @foreach($footerMenus as $menu)
            <div class="col">
               <div class="footer-widget">
                  <h5>{{ $menu['title'] }}</h5>
                  <ul class="footer-menu">
                     @foreach($menu['links'] as $link)
                     <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                     @endforeach
                  </ul>
               </div>
            </div>
            @endforeach
         </div>

         <div class="footer-wrap bg-white">
            <div class="row align-items-center">

               <div class="col-lg-6 col-xl-3 col-xxl-3">
                  <div class="mb-3 text-center text-xl-start">
                     <a href="/" class="d-block footer-logo-light">
                        <img src="/assets/img/enjoy-sxm-black-on-white.png" alt="logo" />
                     </a>
                     <a href="/" class="footer-logo-dark">
                        <img src="/assets/img/enjoy-sxm-white-on-black.png" alt="logo" />
                     </a>
                  </div>
               </div>

               @if($footerStores['show'])
               <div class="col-lg-6 col-xl-4 col-xxl-4">
                  <div class="d-flex align-items-center justify-content-center flex-wrap">
                     <h6 class="fs-14 fw-medium me-2 mb-2">Available on :</h6>
                     @foreach($footerStores['items'] as $store)
                        @if($store['enabled'])
                        <a href="{{ $store['url'] }}" class="d-block mb-3 me-2">
                           <img src="{{ $store['img'] }}" alt="store" />
                        </a>
                        @endif
                     @endforeach
                  </div>
               </div>
               @endif

               <div class="col-lg-12 col-xl-5 col-xxl-5">
                  <div class="d-sm-flex align-items-center justify-content-center justify-content-xl-end">
                     @foreach($footerContacts as $contact)
                     <div class="d-flex align-items-center justify-content-center justify-content-sm-start me-0 pe-0 me-sm-3 pe-sm-3 border-end mb-3">
                        <span class="avatar avatar-lg {{ $contact['bg'] }} rounded-circle flex-shrink-0">
                           <i class="{{ $contact['icon'] }} fs-24"></i>
                        </span>
                        <div class="ms-2">
                           <p class="mb-1">{{ $contact['label'] }}</p>
                           @if($contact['is_email'])
                           <p class="fw-medium text-dark"><a href="mailto:{{ $contact['value'] }}">{{ $contact['value'] }}</a></p>
                           @else
                           <p class="fw-medium text-dark">{{ $contact['value'] }}</p>
                           @endif
                        </div>
                     </div>
                     @endforeach
                  </div>
               </div>

            </div>
         </div>

         <div class="footer-img">
            <img src="/assets/img/bg/footer.svg" class="img-fluid" alt="img" />
         </div>

      </div>
   </div>

   <!-- Footer Bottom -->
   <div class="footer-bottom">
      <div class="container">
         <div class="row">
            <div class="col-md-12">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;">
                  <p style="font-size:14px;margin:0;white-space:nowrap;">
                    Copyright &copy; {{ date('Y') }}. All Rights Reserved,
                    <a href="https://numerimondes.com/refferer/product/enjoytheworld/source/this_url=enjoysxm" style="color:#0d6efd;font-weight:500;display:inline-flex;align-items:center;">
                      <img src="https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png" width="13px" style="margin-right:3px;display:inline-block;vertical-align:middle;">Numerimondes
                    </a>
                  </p>

                  <div class="d-flex align-items-center">
                     <ul class="social-icon">
                        <li><a href="javascript:void(0);"><i class="fa-brands fa-facebook"></i></a></li>
                        <li><a href="javascript:void(0);"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="javascript:void(0);"><i class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="javascript:void(0);"><i class="fa-brands fa-linkedin"></i></a></li>
                        <li><a href="javascript:void(0);"><i class="fa-brands fa-pinterest"></i></a></li>
                     </ul>
                  </div>
                  <ul class="card-links">
                     @for($i=1; $i<=6; $i++)
                     <li>
                        <a href="javascript:void(0);">
                           <img src="/assets/img/icons/card-0{{ $i }}.svg" alt="card" />
                        </a>
                     </li>
                     @endfor
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- /Footer Bottom -->
</footer>
<!-- /Footer -->
