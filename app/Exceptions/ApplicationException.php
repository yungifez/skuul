<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Arr;

class ApplicationException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
