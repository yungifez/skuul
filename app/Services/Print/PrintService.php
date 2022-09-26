<?php

namespace App\Services\Print;

use Barryvdh\DomPDF\Facade\Pdf;

class PrintService
{
    /**
     * create a pdf from a view.
     *
     * @param string $name
     * @param string $view
     * @param array  $data
     *
     * @return void
     */
    public static function createPdfFromView(string $name, string $view, array $data)
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> true,
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ])
        );

        return $pdf;
    }
}
