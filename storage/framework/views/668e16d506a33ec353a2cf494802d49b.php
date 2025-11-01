<?php if(!isset($hideFooter) || !$hideFooter): ?>
    <footer class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-8 mt-auto">
        <?php echo $__env->yieldContent('footer'); ?>
        <?php echo $__env->yieldPushContent('footer'); ?>

        <?php if(!isset($footerContent)): ?>
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <?php echo e($companyName ?? config('app.name')); ?>

                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <?php echo e($companyDescription ?? 'Professional website builder with dynamic themes and modern design capabilities.'); ?>

                        </p>
                        <?php if(isset($socialLinks)): ?>
                            <div class="flex space-x-4">
                                <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($url); ?>"
                                       class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       aria-label="<?php echo e(ucfirst($platform)); ?>">
                                        <?php switch($platform):
                                            case ('facebook'): ?>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                                </svg>
                                                <?php break; ?>
                                            <?php case ('twitter'): ?>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                                </svg>
                                                <?php break; ?>
                                            <?php case ('linkedin'): ?>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M19 0H5a5 5 0 00-5 5v14a5 5 0 005 5h14a5 5 0 005-5V5a5 5 0 00-5-5zM8 19H5V8h3v11zM6.5 6.732c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zM20 19h-3v-5.604c0-3.368-4-3.113-4 0V19h-3V8h3v1.765c1.396-2.586 7-2.777 7 2.476V19z" clip-rule="evenodd" />
                                                </svg>
                                                <?php break; ?>
                                            <?php case ('github'): ?>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                                </svg>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <span class="text-xs"><?php echo e(strtoupper($platform)); ?></span>
                                        <?php endswitch; ?>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <?php if(isset($footerLinks['quick'])): ?>
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Links</h3>
                            <ul class="space-y-2">
                                <?php $__currentLoopData = $footerLinks['quick']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e($link['url']); ?>"
                                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                            <?php echo e($link['title']); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(isset($footerLinks['resources'])): ?>
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resources</h3>
                            <ul class="space-y-2">
                                <?php $__currentLoopData = $footerLinks['resources']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e($link['url']); ?>"
                                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                            <?php echo e($link['title']); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contact</h3>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <?php if(isset($contactInfo['email'])): ?>
                                <p>
                                    <a href="mailto:<?php echo e($contactInfo['email']); ?>" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                        <?php echo e($contactInfo['email']); ?>

                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if(isset($contactInfo['phone'])): ?>
                                <p>
                                    <a href="tel:<?php echo e($contactInfo['phone']); ?>" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                        <?php echo e($contactInfo['phone']); ?>

                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if(isset($contactInfo['address'])): ?>
                                <p><?php echo e($contactInfo['address']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            &copy; <?php echo e(date('Y')); ?> <?php echo e($companyName ?? config('app.name')); ?>. All rights reserved.
                        </p>

                        <?php if(isset($footerLinks['legal'])): ?>
                            <div class="flex space-x-6 mt-4 md:mt-0">
                                <?php $__currentLoopData = $footerLinks['legal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($link['url']); ?>"
                                       class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                        <?php echo e($link['title']); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php echo $footerContent; ?>

        <?php endif; ?>
    </footer>
<?php endif; ?>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/WebsiteBuilder/Resources/Views/live-website/layouts/partials/footer.blade.php ENDPATH**/ ?>