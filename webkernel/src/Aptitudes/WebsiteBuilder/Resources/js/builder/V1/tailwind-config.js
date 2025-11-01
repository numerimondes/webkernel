// WebkernelBuilder Tailwind Configuration
window.WebkernelBuilderTailwindConfig = {
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                'wkb-dark': '#000000',
                'wkb-darker': '#000000',
                'wkb-gray': '#1a1a1a',
                'wkb-light': '#ffffff',
                'wkb-accent': '#3b82f6',
                'canvas-bg': '#000000',
                'canvas-border': '#333333',
            }
        }
    }
};

// Apply the configuration if Tailwind is available
if (typeof tailwind !== 'undefined') {
    tailwind.config = window.WebkernelBuilderTailwindConfig;
}
