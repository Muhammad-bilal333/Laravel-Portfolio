<?php
// app/Http/Controllers/ExportController.php

namespace App\Http\Controllers;

use App\Models\Student; // Replace with your model
use App\Models\assessment; // Replace with your model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportToExcel_old()
    {
        // Get your data (replace with your actual query)
        $data = assessment::all()->toArray();
        
        // Add headers as first row
        array_unshift($data, array_keys($data[0]));
        
        $filename = "students_export_".date('Y-m-d').".csv";
        
        // Create output
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}