
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">


<title><?php echo e($pageTitle ?? ($title ?? 'Numerimondes')); ?></title>
<meta name="description" content="<?php echo e($pageDescription ?? ($description ?? 'Professional website builder with dynamic themes and modern design capabilities')); ?>">
<meta name="keywords" content="<?php echo e($keywords ?? 'website builder, themes, design, web development, responsive'); ?>">
<meta name="author" content="<?php echo e($author ?? config('app.name')); ?>">
<meta name="robots" content="<?php echo e($robots ?? 'index, follow'); ?>">


<?php if(isset($canonical)): ?>
    <link rel="canonical" href="<?php echo e($canonical); ?>">
<?php else: ?>
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">
<?php endif; ?>


<meta property="og:type" content="<?php echo e($ogType ?? 'website'); ?>">
<meta property="og:title" content="<?php echo e($ogTitle ?? ($pageTitle ?? ($title ?? config('app.name')))); ?>">
<meta property="og:description" content="<?php echo e($ogDescription ?? ($pageDescription ?? ($description ?? 'Professional website builder with dynamic themes'))); ?>">
<meta property="og:url" content="<?php echo e($ogUrl ?? url()->current()); ?>">
<meta property="og:site_name" content="<?php echo e($ogSiteName ?? config('app.name')); ?>">
<meta property="og:locale" content="<?php echo e($ogLocale ?? str_replace('-', '_', app()->getLocale())); ?>">


<?php if(isset($ogImage)): ?>
    <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <?php if(isset($ogImageWidth)): ?>
        <meta property="og:image:width" content="<?php echo e($ogImageWidth); ?>">
    <?php endif; ?>
    <?php if(isset($ogImageHeight)): ?>
        <meta property="og:image:height" content="<?php echo e($ogImageHeight); ?>">
    <?php endif; ?>
    <?php if(isset($ogImageAlt)): ?>
        <meta property="og:image:alt" content="<?php echo e($ogImageAlt); ?>">
    <?php endif; ?>
<?php else: ?>
    <meta property="og:image" content="<?php echo e(asset('images/default-og-image.jpg')); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo e(config('app.name')); ?> - Website Builder">
<?php endif; ?>


<meta name="twitter:card" content="<?php echo e($twitterCard ?? 'summary_large_image'); ?>">
<meta name="twitter:title" content="<?php echo e($twitterTitle ?? ($ogTitle ?? ($pageTitle ?? $title ?? config('app.name')))); ?>">
<meta name="twitter:description" content="<?php echo e($twitterDescription ?? ($ogDescription ?? ($pageDescription ?? ($description ?? 'Professional website builder with dynamic themes')))); ?>">
<meta name="twitter:image" content="<?php echo e($twitterImage ?? ($ogImage ?? asset('images/default-og-image.jpg'))); ?>">
<?php if(isset($twitterSite)): ?>
    <meta name="twitter:site" content="<?php echo e($twitterSite); ?>">
<?php endif; ?>
<?php if(isset($twitterCreator)): ?>
    <meta name="twitter:creator" content="<?php echo e($twitterCreator); ?>">
<?php endif; ?>


<?php if(isset($articleAuthor)): ?>
    <meta property="article:author" content="<?php echo e($articleAuthor); ?>">
<?php endif; ?>
<?php if(isset($articlePublishedTime)): ?>
    <meta property="article:published_time" content="<?php echo e($articlePublishedTime); ?>">
<?php endif; ?>
<?php if(isset($articleModifiedTime)): ?>
    <meta property="article:modified_time" content="<?php echo e($articleModifiedTime); ?>">
<?php endif; ?>
<?php if(isset($articleSection)): ?>
    <meta property="article:section" content="<?php echo e($articleSection); ?>">
<?php endif; ?>
<?php if(isset($articleTags)): ?>
    <?php $__currentLoopData = $articleTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <meta property="article:tag" content="<?php echo e($tag); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>


<?php if(isset($favicon)): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e($favicon); ?>">
<?php else: ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
<?php endif; ?>


<link rel="apple-touch-icon" sizes="57x57" href="<?php echo e($appleTouchIcon57 ?? asset('images/icons/apple-touch-icon-57x57.png')); ?>">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo e($appleTouchIcon114 ?? asset('images/icons/apple-touch-icon-114x114.png')); ?>">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo e($appleTouchIcon72 ?? asset('images/icons/apple-touch-icon-72x72.png')); ?>">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo e($appleTouchIcon144 ?? asset('images/icons/apple-touch-icon-144x144.png')); ?>">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo e($appleTouchIcon60 ?? asset('images/icons/apple-touch-icon-60x60.png')); ?>">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo e($appleTouchIcon120 ?? asset('images/icons/apple-touch-icon-120x120.png')); ?>">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo e($appleTouchIcon76 ?? asset('images/icons/apple-touch-icon-76x76.png')); ?>">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo e($appleTouchIcon152 ?? asset('images/icons/apple-touch-icon-152x152.png')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($appleTouchIcon180 ?? asset('images/icons/apple-touch-icon-180x180.png')); ?>">


<link rel="icon" type="image/png" sizes="192x192" href="<?php echo e($androidIcon192 ?? asset('images/icons/android-icon-192x192.png')); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($faviconIcon32 ?? asset('images/icons/favicon-32x32.png')); ?>">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo e($faviconIcon96 ?? asset('images/icons/favicon-96x96.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e($faviconIcon16 ?? asset('images/icons/favicon-16x16.png')); ?>">


<?php if(isset($manifest) || file_exists(public_path('manifest.json'))): ?>
    <link rel="manifest" href="<?php echo e($manifest ?? asset('manifest.json')); ?>">
<?php endif; ?>


<meta name="theme-color" content="<?php echo e($themeColor ?? '#3b82f6'); ?>">
<meta name="msapplication-TileColor" content="<?php echo e($tileColor ?? '#3b82f6'); ?>">
<meta name="msapplication-TileImage" content="<?php echo e($tileImage ?? asset('images/icons/ms-icon-144x144.png')); ?>">


<?php if(isset($safariPinnedTab)): ?>
    <link rel="mask-icon" href="<?php echo e($safariPinnedTab); ?>" color="<?php echo e($safariPinnedTabColor ?? '#3b82f6'); ?>">
<?php endif; ?>


<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//unpkg.com">


<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


<?php if(isset($alternateLanguages)): ?>
    <?php $__currentLoopData = $alternateLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <link rel="alternate" hreflang="<?php echo e($lang); ?>" href="<?php echo e($url); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>


<?php if(isset($rssFeed)): ?>
    <link rel="alternate" type="application/rss+xml" title="<?php echo e($rssFeedTitle ?? config('app.name') . ' RSS'); ?>" href="<?php echo e($rssFeed); ?>">
<?php endif; ?>


<?php if(isset($structuredData)): ?>
    <script type="application/ld+json">
        <?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>

    </script>
<?php endif; ?>


<?php if(isset($customMeta)): ?>
    <?php $__currentLoopData = $customMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($meta['property'])): ?>
            <meta property="<?php echo e($meta['property']); ?>" content="<?php echo e($meta['content']); ?>">
        <?php elseif(isset($meta['name'])): ?>
            <meta name="<?php echo e($meta['name']); ?>" content="<?php echo e($meta['content']); ?>">
        <?php elseif(isset($meta['http-equiv'])): ?>
            <meta http-equiv="<?php echo e($meta['http-equiv']); ?>" content="<?php echo e($meta['content']); ?>">
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/WebsiteBuilder/Resources/Views/live-website/layouts/partials/meta.blade.php ENDPATH**/ ?>