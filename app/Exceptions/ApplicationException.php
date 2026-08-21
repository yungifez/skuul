<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ApplicationException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  Request  $request
     * @return RedirectResponse|JsonResponse
     */
    public function render($request)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => $this->getMessage(),
            ], 400);
        } else {
            return back()->with('danger', $this->getMessage())->withInput(Arr::Except(request()->post(), ['_token', '_method']));
        }
    }
}
