<?php

namespace App\Exceptions;

use Exception;

class ResourceNotEmptyException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => $this->getMesage()
            ], 400);
        }else{
            return back()->with('danger', $this->getMessage());
        }
    
    }
}
