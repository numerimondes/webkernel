<script src="https://cdn.tailwindcss.com"></script>
@php
    $company = new \Numerimondes\Numerimondes\Models\NMCoreCompanies();
@endphp

<div class="space-y-6">
    <!-- Company Status -->
    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Company Status</h3>
        <select
            name="status_id"
            class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
        >
            <option value="" disabled selected>-- Select Status --</option>
            @foreach($company->getEnumOptions('status_id') as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <!-- Company Type -->
    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Company Type</h3>
        <select
            name="company_type_id"
            class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
        >
            <option value="" disabled selected>-- Select Type --</option>
            @foreach($company->getEnumOptions('company_type_id') as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
