@props([])

<div class="webkernel-component webkernel-page test_page">
    <div class="page-header">
        <h1>{{ isset($title) ? $title : 'test-page' }}</h1>
    </div>

    <div class="page-content">
        {{ $slot ?? '' }}
    </div>
</div>
