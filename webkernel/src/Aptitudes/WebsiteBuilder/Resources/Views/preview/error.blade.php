<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Error - Website Builder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            text-align: center;
        }

        .error-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-details {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: left;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9rem;
            color: #495057;
            word-break: break-all;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-1px);
        }

        .preview-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #e74c3c;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .error-container {
                padding: 2rem 1.5rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        @if($isPreview ?? false)
            <div class="preview-badge">PREVIEW MODE</div>
        @endif

        <div class="error-icon">⚠️</div>

        <h1 class="error-title">Preview Error</h1>

        <p class="error-message">
            We encountered an error while trying to render the preview. This could be due to a configuration issue or a problem with the page content.
        </p>

        @if(isset($error) && $error)
            <div class="error-details">
                <strong>Error Details:</strong><br>
                {{ $error }}
            </div>
        @endif

        <div class="error-actions">
            <a href="javascript:history.back()" class="btn btn-primary">
                ← Go Back
            </a>

            <a href="javascript:location.reload()" class="btn btn-secondary">
                🔄 Try Again
            </a>
        </div>
    </div>
</body>
</html>
