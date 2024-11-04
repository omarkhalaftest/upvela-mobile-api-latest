<?php

namespace App\Http\Controllers\Helper\InterventionImage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class InterventionController extends Controller
{
    public function uploadImage($image, $folderName)
    {


        // Generate a unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();

        // Define the folder path
        $folderPath = public_path('uploads/' . $folderName);

        // Move the uploaded file to the specific directory
        $image->move($folderPath, $filename);

        // Resize the image using Intervention\Image
        $resizedImage = Image::make($folderPath . '/' . $filename)->resize(300, 200);

        // Save the resized image
        $resizedImage->save($folderPath . '/' . $filename);

        // Return the filename with the folder path
        return   $filename;
    }

    public function uploadPDF($pdfContent, $folderName)
    {
        // Generate a unique filename
        $filename = uniqid() . '.pdf';

        // Define the folder path
        $folderPath = public_path('uploads/' . $folderName);

        // Create the directory if it doesn't exist
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Move the PDF content to the specific directory
        file_put_contents($folderPath . '/' . $filename, $pdfContent);

        // Return the filename with the folder path
        return $filename;
    }
}
