<?php

namespace App\Http\Controllers\V2\Front\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\InterventionImage\InterventionController;
use Dompdf\Dompdf;


class makeContractController extends Controller
{
    public function uploadFile(Request $request)
    {
        $email = "ahmedsamir117111@gmail.com";
        $otp = 1585;
        $html = view('contract', compact('email', 'otp'))->render();

        // Load Dompdf
        $dompdf = new Dompdf();

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (optional: set the filename to save the PDF to)
        $dompdf->render();
        $pdfContent = $dompdf->output();
        $intervintionImage = new InterventionController();
        $pdfFileName = $intervintionImage->uploadPDF($pdfContent, 'pdfs'); // Assuming 'pdfs' is the folder name



        // Output PDF to the browser
        return $dompdf->stream('document.pdf');
    }
}
