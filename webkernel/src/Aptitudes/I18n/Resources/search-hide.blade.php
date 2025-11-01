@php
    use Filament\Support\Icons\Heroicon;
@endphp

<span class="search-hide" style="color:gray; transition: opacity 0.2s; " tabindex="0">
    <x-filament::icon icon="heroicon-o-magnifying-glass" />
</span>

<style>
.search-hide:hover, .search-hide:focus {
    opacity: 1 !important;
    filter: drop-shadow(0 2px 4px rgba(50, 118, 195, 0.15));
}
</style>

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
