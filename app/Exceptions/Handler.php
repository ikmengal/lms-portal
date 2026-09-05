<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Spatie\Activitylog\Facades\Activity;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->reportable(function (Throwable $e, Request $request) {
            Activity::causedBy(auth()->user())
                ->useLog('errors')
                ->log('Error: ' . $e->getMessage());
        });
    }

    public function render(Request $request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        return parent::render($request, $e);
    }
}
