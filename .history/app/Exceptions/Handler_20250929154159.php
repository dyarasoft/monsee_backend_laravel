<?php

namespace App\Exceptions;

use App\Traits\ApiResponser; // 1. Import Trait yang sudah kita buat
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException; // 2. Import ValidationException
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser; // 3. Gunakan Trait di dalam Handler

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

        // 4. Tambahkan blok kode ini
        // Ini akan menangani ValidationException secara spesifik
        $this->renderable(function (ValidationException $e, $request) {
            // Cek jika request ditujukan untuk API
            if ($request->is('api/*')) {
                // Ambil pesan error pertama
                $message = $e->validator->errors()->first();
                // Ambil semua error
                $errors = $e->validator->errors();

                // Kembalikan response JSON menggunakan Trait kita
                return $this->error(422, $message, $errors);
            }
        });
    }
}
