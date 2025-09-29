<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handler untuk menangani semua jenis error API dan mengubahnya menjadi JSON
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    $message = $e->validator->errors()->first();
                    $errors = $e->validator->errors();
                    return $this->error(422, $message, $errors);
                }

                if ($e instanceof AuthenticationException) {
                    return $this->error(401, 'Unauthenticated. Please login first.');
                }

                if ($e instanceof AuthorizationException) {
                    return $this->error(403, 'Forbidden. You do not have permission to perform this action.');
                }

                if ($e instanceof NotFoundHttpException) {
                    return $this->error(404, 'The requested resource was not found.');
                }

                // Fallback untuk error server lainnya (500)
                // Di mode produksi, jangan tampilkan pesan error asli
                $message = config('app.debug') ? $e->getMessage() : 'Unexpected server error.';
                return $this->error(500, $message);
            }
        });
    }
}

