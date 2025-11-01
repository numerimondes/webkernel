document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-component="button"][data-key-bindings]').forEach(button => {
        const bindings = button.dataset.keyBindings.split(',').map(s => s.trim());

        document.addEventListener('keydown', function(e) {
            bindings.forEach(binding => {
                const keys = binding.toLowerCase().split('+');
                const hasCtrl = keys.includes('ctrl') && (e.ctrlKey || e.metaKey);
                const hasShift = keys.includes('shift') && e.shiftKey;
                const hasAlt = keys.includes('alt') && e.altKey;
                const key = keys[keys.length - 1];

                if (
                    (!keys.includes('ctrl') || hasCtrl) &&
                    (!keys.includes('shift') || hasShift) &&
                    (!keys.includes('alt') || hasAlt) &&
                    e.key.toLowerCase() === key
                ) {
                    e.preventDefault();
                    button.click();
                }
            });
        });
    });
});
