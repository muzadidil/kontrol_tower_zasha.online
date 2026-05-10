<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Dispute, TenagaOrder};
use App\Services\TenagaService;
use Illuminate\Http\Request;

class TenagaController extends Controller
{
    public function __construct(private TenagaService $tenagaService) {}

    public function index(Request $request)
    {
        $query = TenagaOrder::with(['pelanggan', 'mitra', 'mitraLayanan.masterLayanan']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('order_code', 'like', '%' . $request->search . '%');
        $orders = $query->latest()->paginate(20);
        return view('admin.tenaga.index', compact('orders'));
    }

    public function show(TenagaOrder $tenagaOrder)
    {
        $tenagaOrder->load(['pelanggan', 'mitra', 'mitraLayanan.masterLayanan']);
        $dispute = Dispute::where('order_type', 'tenaga')->where('order_id', $tenagaOrder->id)->first();
        return view('admin.tenaga.show', compact('tenagaOrder', 'dispute'));
    }

    public function resolusiDispute(Request $request, TenagaOrder $tenagaOrder)
    {
        $request->validate(['resolusi' => 'required|in:release_to_mitra,refund_to_customer']);
        try {
            $this->tenagaService->selesaikanDispute($tenagaOrder, $request->resolusi);
            Dispute::where('order_type', 'tenaga')->where('order_id', $tenagaOrder->id)
                ->update(['status' => 'resolved', 'resolution' => $request->resolusi, 'admin_notes' => $request->admin_notes, 'resolved_by' => auth()->id(), 'resolved_at' => now()]);
            return back()->with('success', 'Dispute selesai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
