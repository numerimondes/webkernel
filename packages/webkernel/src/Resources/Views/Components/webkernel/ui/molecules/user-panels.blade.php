<x-filament::dropdown
    placement="bottom-start"
    size
    teleport
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-menu'])
    "
>
    <x-slot name="trigger">
        <button
            type="button"
            class="fi-tenant-menu-trigger group flex w-full items-center justify-center gap-x-3 rounded-lg p-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
        >
            <x-filament-panels::avatar.tenant
                :tenant="null"
                class="shrink-0"
            />

            <span class="grid justify-items-start text-start">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Tenant Label
                </span>

                <span class="text-gray-950 dark:text-white">
                    Tenant Name
                </span>
            </span>

            <x-filament::icon
                icon="heroicon-m-chevron-down"
                icon-alias="panels::tenant-menu.toggle-button"
                class="ms-auto h-5 w-5 shrink-0 text-gray-400 transition duration-75 group-hover:text-gray-500 group-focus-visible:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400 dark:group-focus-visible:text-gray-400"
            />
        </button>
    </x-slot>

    {{-- Dropdown Profile + Billing --}}
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            :color="'primary'"
            :href="'#profile-url'"
            :icon="'heroicon-m-cog-6-tooth'"
            tag="a"
            :target="null"
        >
            Profile
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item
            :color="'gray'"
            :href="'#billing-url'"
            :icon="'heroicon-m-credit-card'"
            tag="a"
            :target="null"
        >
            Billing
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>

    {{-- Dropdown Items --}}
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            :action="null"
            :color="'info'"
            :href="'#item-url'"
            :icon="'heroicon-m-link'"
            :method="null"
            :tag="'a'"
            :target="null"
        >
            Item Label
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>

    {{-- Dropdown Tenants --}}
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            :href="'#tenant-url'"
            :image="'https://via.placeholder.com/32'"
            tag="a"
        >
            Tenant A
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>

    {{-- Registration Item --}}
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            :color="'success'"
            :href="'#register-url'"
            :icon="'heroicon-m-plus'"
            tag="a"
            :target="null"
        >
            Register Tenant
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>

