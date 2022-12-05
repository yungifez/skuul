<?php

namespace App\Exceptions;

use Exception;

class ClassGroupNotEmptyException extends Exception
{
    public function report()
    {
        return false;
    }

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
            ], 404);
        }else{
            return back()->with('danger', $this->getMessage());
        }
    
    }
}
