<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MaterialRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialRequest::with(['requester', 'assignee', 'material']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_materi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('requester', function($subQ) use ($search) {
                      $subQ->where('nama_lengkap', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('prioritas') && $request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $requests = $query->paginate(10)->withQueryString();
        
        return view('admin.request-materi.index', compact('requests'));
    }
    
    public function show(MaterialRequest $request)
    {
        $request->load(['requester', 'assignee', 'material']);
        return view('admin.request-materi.show', compact('request'));
    }
    
    public function approve(Request $request, MaterialRequest $materialRequest)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }
        
        $materialRequest->update([
            'status' => 'approved',
            'assigned_to' => Auth::id(),
            'admin_notes' => $request->admin_notes
        ]);
        
        return redirect()->route('admin.request-materi.index')
            ->with('success', 'Request materi telah disetujui!');
    }
    
    public function reject(Request $request, MaterialRequest $materialRequest)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }
        
        $materialRequest->update([
            'status' => 'rejected',
            'assigned_to' => Auth::id(),
            'admin_notes' => $request->admin_notes
        ]);
        
        return redirect()->route('admin.request-materi.index')
            ->with('success', 'Request materi telah ditolak.');
    }
    
    public function markInProgress(MaterialRequest $materialRequest)
    {
        $materialRequest->update([
            'status' => 'in_progress',
            'assigned_to' => Auth::id()
        ]);
        
        return redirect()->back()
            ->with('success', 'Request telah ditandai sebagai dalam proses.');
    }
    
    public function complete(Request $request, MaterialRequest $materialRequest)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }
        
        $materialRequest->update([
            'status' => 'completed',
            'material_id' => $request->material_id,
            'completed_at' => now()
        ]);
        
        return redirect()->back()
            ->with('success', 'Request telah diselesaikan!');
    }
    
    public function statistics()
    {
        $stats = [
            'total_requests' => MaterialRequest::count(),
            'pending_requests' => MaterialRequest::pending()->count(),
            'approved_requests' => MaterialRequest::where('status', 'approved')->count(),
            'completed_requests' => MaterialRequest::where('status', 'completed')->count(),
            'rejected_requests' => MaterialRequest::where('status', 'rejected')->count(),
            'requests_by_priority' => MaterialRequest::selectRaw('prioritas, count(*) as count')
                ->groupBy('prioritas')
                ->pluck('count', 'prioritas')
                ->toArray(),
            'requests_by_status' => MaterialRequest::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray()
        ];
        
        return response()->json($stats);
    }
}
