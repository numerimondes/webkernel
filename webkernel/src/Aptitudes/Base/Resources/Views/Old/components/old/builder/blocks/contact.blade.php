
{{--
    resources/views/builder/blocks/contact.blade.php
    @meta name: Contact Form
    @meta icon: 📧
    @meta category: Forms
    @meta preview: Contact form with fields and validation
    @meta description: Professional contact form with name, email, and message fields
--}}

@php
$blockId = $blockId ?? 'contact-default';
$title = $title ?? 'Contactez-nous';
$description = $description ?? 'Nous sommes là pour vous aider';
$alignment = $alignment ?? 'center';
$variant = $variant ?? 'default';
@endphp

<section class="py-16 {{ $variant === 'dark' ? 'bg-gray-900' : 'bg-gray-50 dark:bg-gray-800/50' }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-{{ $alignment }} mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $title }}
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300">
                {{ $description }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
            <form class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <x-filament::input.wrapper>
                            <label for="contact-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nom complet
                            </label>
                            <x-filament::input
                                id="contact-name"
                                type="text"
                                placeholder="Votre nom"
                                required
                            />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <x-filament::input.wrapper>
                            <label for="contact-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email
                            </label>
                            <x-filament::input
                                id="contact-email"
                                type="email"
                                placeholder="votre@email.com"
                                required
                            />
                        </x-filament::input.wrapper>
                    </div>
                </div>

                                <div>
                    <x-filament::input.wrapper>
                        <label for="contact-subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sujet
                        </label>
                        <x-filament::input
                            id="contact-subject"
                            type="text"
                            placeholder="Sujet de votre message"
                        />
                    </x-filament::input.wrapper>
                </div>

                                <div>
                    <x-filament::input.wrapper>
                        <label for="contact-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Message
                        </label>
                        <textarea
                            id="contact-message"
                            rows="6"
                            class="block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600
                                   rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-gray-900 dark:text-gray-100
                                   transition-colors resize-none"
                            placeholder="Décrivez votre demande..."
                            required></textarea>
                    </x-filament::input.wrapper>
                </div>

                <div class="text-{{ $alignment }}">
                    <x-filament::button type="submit" color="primary" size="lg">
                        <x-filament::icon icon="heroicon-o-paper-airplane" class="w-5 h-5 mr-2" />
                        Envoyer le message
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</section>
