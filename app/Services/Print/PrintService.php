<?php
namespace App\Services\Print;

use Barryvdh\DomPDF\Facade\Pdf;

class PrintService{
    public static function createPdfFromView(string $name, string $view, array $data)
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );
        return $pdf->download("$name.pdf");
    }
}