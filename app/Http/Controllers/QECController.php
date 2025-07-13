<?php

namespace App\Http\Controllers;

use App\Models\CRR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QECController extends Controller
{
    public function dashboard()
    {
        return view('Qualityenhancementcell.dashboard');
    }

    public function failedPLOs()
    {
        return view('Qualityenhancementcell.simple_failed_plos');
    }

    public function fetchCRRData()
    {
        $crrs = CRR::with(['lecturer', 'course'])
            ->orderBy('last_updated', 'desc')
            ->get()
            ->map(function ($crr) {
                return [
                    'id' => $crr->id,
                    'course_code' => $crr->course_code,
                    'course_name' => $crr->course_name,
                    'lecturer' => $crr->lecturer->name,
                    'total_students' => $crr->total_students,
                    'failed_plos' => count($crr->failed_plos),
                    'last_updated' => $crr->last_updated->format('Y-m-d H:i'),
                    'status' => $crr->status,
                    'qec_comments' => $crr->qec_comments
                ];
            });

        $stats = [
            'total' => $crrs->count(),
            'pending' => $crrs->where('status', 'pending review')->count(),
            'reviewed' => $crrs->where('status', 'reviewed')->count(),
            'needs_attention' => $crrs->where('status', 'needs attention')->count()
        ];

        return response()->json([
            'crrs' => $crrs,
            'stats' => $stats
        ]);
    }

    public function viewCRR($id)
    {
        $crr = CRR::with(['lecturer', 'course'])->findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'id' => $crr->id,
                'course_code' => $crr->course_code,
                'course_name' => $crr->course_name,
                'lecturer' => $crr->lecturer->name,
                'total_students' => $crr->total_students,
                'failed_plos' => $crr->failed_plos,
                'last_updated' => $crr->last_updated->format('Y-m-d H:i'),
                'status' => $crr->status,
                'qec_comments' => $crr->qec_comments
            ]);
        }

        return view('Qualityenhancementcell.view_crr', compact('crr'));
    }

    public function downloadCRR($id)
    {
        $crr = CRR::findOrFail($id);
        
        if (!$crr->file_path || !Storage::exists($crr->file_path)) {
            return back()->with('error', 'CRR file not found.');
        }

        return Storage::download($crr->file_path);
    }

    public function updateCRR(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending review,reviewed,needs attention',
            'qec_comments' => 'nullable|string'
        ]);

        $crr = CRR::findOrFail($id);
        $crr->status = $request->status;
        $crr->qec_comments = $request->qec_comments;
        $crr->last_updated = now();
        $crr->save();

        return response()->json([
            'message' => 'CRR updated successfully',
            'crr' => [
                'id' => $crr->id,
                'status' => $crr->status,
                'qec_comments' => $crr->qec_comments,
                'last_updated' => $crr->last_updated->format('Y-m-d H:i')
            ]
        ]);
    }
}