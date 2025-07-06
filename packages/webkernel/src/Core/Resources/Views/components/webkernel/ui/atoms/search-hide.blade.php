<span class="search-hide"><svg style=";" wire:loading.remove.delay.default="1" wire:target="search"
    class="fi-input-wrp-icon h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
    <path fill-rule="evenodd"
        d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
        clip-rule="evenodd"></path>
</svg></span>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Ajout du CSS dynamiquement
    const style = document.createElement('style');
    style.innerHTML = `
            .fi-global-search-field {
                transition: all 0.3s ease-in-out;
                width: 0;
                opacity: 0;
                padding: 0;
                overflow: hidden;
            }

            .fi-global-search-field:focus-within {
                width: auto;
                opacity: 1;
                padding: 10px;
            }

            .fi-input-wrp {
                transition: width 0.3s ease-in-out, padding 0.3s ease-in-out;
            }

            .search-hide {
                cursor: pointer;
            }
        `;
    document.head.appendChild(style);

    // Sélecteurs
    const searchIcon = document.querySelector('.search-hide');
    const searchField = document.querySelector('.fi-global-search-field input');

    if (!searchIcon || !searchField) return;

    const showSearch = () => {
        searchIcon.style.display = 'none';
        const container = searchField.closest('.fi-global-search-field');
        if (container) {
            container.style.width = '200px';
            container.style.opacity = '1';
            container.style.padding = '10px';
            searchField.focus();
        }
    };

    const hideSearch = () => {
        const container = searchField.closest('.fi-global-search-field');
        if (container) {
            container.style.width = '0';
            container.style.opacity = '0';
            container.style.padding = '0';
        }
        searchIcon.style.display = 'inline-block';
    };

    // Clic sur l'icône
    searchIcon.addEventListener('click', showSearch);

    // Blur du champ
    searchField.addEventListener('blur', hideSearch);

    // Ctrl+K ou Cmd+K
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            showSearch();
        }
    });
});
</script>
