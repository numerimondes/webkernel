<?php
namespace Webkernel\Core\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Laisser Laravel/Filament gérer TOUTES les exceptions normalement
        $response = parent::render($request, $exception);
        
        // Seulement intercepter les réponses d'erreur HTML finales
        // pour remplacer les vues par défaut par nos vues custom
        if ($this->shouldCustomizeErrorPage($request, $response)) {
            return $this->renderCustomErrorPage($request, $response, $exception);
        }

        return $response;
    }

    /**
     * Détermine si on doit customiser la page d'erreur
     */
    protected function shouldCustomizeErrorPage($request, $response)
    {
    
        if ($request->expectsJson()) {
            return false;
        }

        $statusCode = $response->getStatusCode();
        $customizableErrors = [401, 403, 404, 419, 429, 500, 503];
        
        return in_array($statusCode, $customizableErrors);
    }

    /**
     * Rendu de la page d'erreur customisée
     */
    protected function renderCustomErrorPage($request, $response, $exception)
    {
        $statusCode = $response->getStatusCode();
        
        $messages = [
            // Client Errors (4xx)
            401 => 'Unauthorized ! Please authenticate to access this resource.',
            403 => 'Forbidden ! You do not have permission to access this resource.',
            404 => 'Not Found ! The resource you are looking for might have been removed, had its name changed, or is temporarily unavailable.',
            419 => 'Page Expired ! This page has expired, please refresh the page.',
            429 => 'Too Many Requests ! Please wait before making another request.',
            
            // Server Errors (5xx)
            500 => 'Internal Server Error - Something went wrong on our end.',
            503 => 'Service Unavailable - The service is temporarily unavailable.',
        ];

        $json = [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => collect($exception->getTrace())->take(5),
        ];

        return response()->view('webkernel::error.error-page', [
            'type' => (string) $statusCode,
            'message' => $messages[$statusCode] ?? 'Error',
            'code' => $statusCode,
            'json' => $json,
        ], $statusCode);
    }
}