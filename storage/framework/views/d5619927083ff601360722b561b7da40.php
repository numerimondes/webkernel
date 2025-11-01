<?php
use Carbon\Carbon;

$blogPosts = [
    [
        'title' => 'Whale Watching Adventure in Saint Martin',
        'category' => 'Travel',
        'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?auto=format&fit=crop&w=1200&q=80', // Baleine sautant hors de l’eau
        'link' => '#',
        'author' => 'Christophe Weber',
        'author_image' => 'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/clients/saint-marteen/enjoy-sxm/commons/christophe.png',
        'date' => Carbon::parse('2025-09-09')->addDays(rand(0, Carbon::now()->diffInDays(Carbon::parse('2025-09-09'))))->format('d M Y'),
    ],
    [
        'title' => 'Discover Saint Martin\'s Hidden Beaches',
        'category' => 'Guide',
        'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80', // Plage isolée avec eau turquoise
        'link' => '#',
        'author' => 'Christophe Weber',
        'author_image' => 'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/clients/saint-marteen/enjoy-sxm/commons/christophe.png',
        'date' => Carbon::parse('2025-09-09')->addDays(rand(0, Carbon::now()->diffInDays(Carbon::parse('2025-09-09'))))->format('d M Y'),
    ],
    [
        'title' => 'Sailing Around Anguilla & Saint Martin',
        'category' => 'Rental',
        'image' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80', // Voilier sur mer calme au coucher du soleil
        'link' => '#',
        'author' => 'Christophe Weber',
        'author_image' => 'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/clients/saint-marteen/enjoy-sxm/commons/christophe.png',
        'date' => Carbon::parse('2025-09-09')->addDays(rand(0, Carbon::now()->diffInDays(Carbon::parse('2025-09-09'))))->format('d M Y'),
    ],
];
?>


<!-- Blog Section -->
<section class="section blog-section pt-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-10 text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="section-header text-center">
                    <h2 style="font-size:25px;font-weight:bold;" class="mb-2">Recent <span class="text-primary text-decoration-underline">Articles</span></h2>
                    <p class="sub-title">
                        Discover our curated blog posts focused on Saint Martin adventures, boat rentals, and travel guides.
                    </p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <?php $__currentLoopData = $blogPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6">
                <div class="blog-item mb-4 wow fadeInUp" data-wow-delay="0.2s">
                    <a href="<?php echo e($post['link']); ?>" class="blog-img">
                        <img src="<?php echo e($post['image']); ?>" alt="Blog image" />
                    </a>
                    <span class="badge bg-primary fs-13 fw-medium"><?php echo e($post['category']); ?></span>
                    <div class="blog-info text-center">
                        <div class="d-inline-flex align-items-center justify-content-center">
                            <div class="d-inline-flex align-items-center border-end pe-3 me-3 mb-2">
                                <a href="javascript:void(0);" class="d-flex align-items-center">
                                    <span class="avatar avatar-sm flex-shrink-0 me-2">
                                        <img src="<?php echo e($post['author_image']); ?>" class="rounded-circle border border-white" alt="<?php echo e($post['author']); ?>" />
                                    </span>
                                    <p><?php echo e($post['author']); ?></p>
                                </a>
                            </div>
                            <p class="d-inline-flex align-items-center text-white mb-2">
                                <i class="isax isax-calendar-2 me-2"></i><?php echo e($post['date']); ?>

                            </p>
                        </div>
                        <h5><a href="<?php echo e($post['link']); ?>"><?php echo e($post['title']); ?></a></h5>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="text-center view-all wow fadeInUp">
            <a href="#" class="btn btn-dark d-inline-flex align-items-center">
                View All Articles<i class="isax isax-arrow-right-3 ms-2"></i>
            </a>
        </div>
    </div>
</section>
<!-- /Blog Section -->
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/platform/EnjoyTheWorld/Resources/Views/software/body/12-blog-section.blade.php ENDPATH**/ ?>