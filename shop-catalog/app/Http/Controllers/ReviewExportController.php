<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReviewsExport;

class ReviewExportController extends Controller
{
    public function export()
    {
        return Excel::download(new ReviewsExport, 'ulasan_produk.xlsx');
    }
}