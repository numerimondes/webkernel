<div class="preview-container">
    <div class="preview-header">
        <div class="preview-controls">
            <h2 class="preview-title">Preview Mode</h2>
            <div class="preview-actions">
                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="openPreviewWindow('{{ $previewUrls['page'] }}')"
                >
                    <i class="fas fa-external-link-alt"></i> Open Preview
                </button>

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="openPreviewWindow('{{ $previewUrls['by_domain'] }}')"
                >
                    <i class="fas fa-globe"></i> Domain Preview
                </button>

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="copyToClipboard('{{ $previewUrls['page'] }}')"
                >
                    <i class="fas fa-copy"></i> Copy URL
                </button>
            </div>
        </div>

        <div class="preview-urls">
            <div class="url-group">
                <label>Page Preview:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $previewUrls['page'] }}" readonly>
            </div>

            <div class="url-group">
                <label>Domain Preview:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $previewUrls['by_domain'] }}" readonly>
            </div>

            <div class="url-group">
                <label>ID Preview:</label>
                <input type="text" class="form-control form-control-sm" value="{{ $previewUrls['by_id'] }}" readonly>
            </div>
        </div>
    </div>

    <div class="preview-content">
        <div class="preview-iframe-container">
            <iframe
                id="preview-iframe"
                src="{{ $previewUrls['page'] }}"
                frameborder="0"
                class="preview-iframe"
                title="Website Preview"
            ></iframe>
        </div>
    </div>

    <div class="preview-footer">
        <div class="preview-info">
            <span class="badge badge-info">Project: {{ $project->name ?? 'Unknown' }}</span>
            <span class="badge badge-secondary">Page: {{ $page->name ?? 'Homepage' }}</span>
            <span class="badge badge-success">Status: {{ $page->status ?? 'active' }}</span>
        </div>

        <div class="preview-actions-footer">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshPreview()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>

            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen()">
                <i class="fas fa-expand"></i> Fullscreen
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
.preview-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    background: #f8f9fa;
}

.preview-header {
    background: white;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.preview-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #495057;
}

.preview-actions {
    display: flex;
    gap: 0.5rem;
}

.preview-urls {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.url-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.url-group label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6c757d;
    margin: 0;
}

.preview-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.preview-iframe-container {
    flex: 1;
    position: relative;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin: 1rem;
    overflow: hidden;
}

.preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
    background: white;
}

.preview-footer {
    background: white;
    border-top: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
}

.preview-info {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.preview-actions-footer {
    display: flex;
    gap: 0.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .preview-controls {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .preview-actions {
        justify-content: center;
    }

    .preview-urls {
        grid-template-columns: 1fr;
    }

    .preview-footer {
        flex-direction: column;
        gap: 0.5rem;
        align-items: stretch;
    }

    .preview-info {
        justify-content: center;
    }

    .preview-actions-footer {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function openPreviewWindow(url) {
    window.open(url, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy URL to clipboard');
    });
}

function refreshPreview() {
    const iframe = document.getElementById('preview-iframe');
    iframe.src = iframe.src;
}

function toggleFullscreen() {
    const iframe = document.getElementById('preview-iframe');
    if (iframe.requestFullscreen) {
        iframe.requestFullscreen();
    } else if (iframe.webkitRequestFullscreen) {
        iframe.webkitRequestFullscreen();
    } else if (iframe.msRequestFullscreen) {
        iframe.msRequestFullscreen();
    }
}

// Listen for Livewire events
document.addEventListener('livewire:load', function () {
    Livewire.on('openPreviewWindow', function (url) {
        openPreviewWindow(url);
    });
});
</script>
@endpush
