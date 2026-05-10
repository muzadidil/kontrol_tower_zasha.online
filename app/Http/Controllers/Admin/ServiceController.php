<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Dispute, ServiceOrder};
use App\Services\ServiceJasaService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(private ServiceJasaService $serviceJasa) {}

    public function index(Request $request)
    {
        $query = ServiceOrder::with(['pelanggan', 'mitra', 'mitraLayanan.masterLayanan']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('order_code', 'like', '%' . $request->search . '%');
        $orders = $query->latest()->paginate(20);
        return view('admin.service.index', compact('orders'));
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $serviceOrder->load(['pelanggan', 'mitra', 'mitraLayanan.masterLayanan', 'orderItems']);
        $dispute = Dispute::where('order_type', 'service')->where('order_id', $serviceOrder->id)->first();
        return view('admin.service.show', compact('serviceOrder', 'dispute'));
    }

    public function resolusiDispute(Request $request, ServiceOrder $serviceOrder)
    {
        $request->validate(['resolusi' => 'required|in:release_to_mitra,refund_to_customer']);
        try {
            $this->serviceJasa->selesaikanDispute($serviceOrder, $request->resolusi);
            Dispute::where('order_type', 'service')->where('order_id', $serviceOrder->id)
                ->update(['status' => 'resolved', 'resolution' => $request->resolusi, 'admin_notes' => $request->admin_notes, 'resolved_by' => auth()->id(), 'resolved_at' => now()]);
            return back()->with('success', 'Dispute selesai.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }
}
