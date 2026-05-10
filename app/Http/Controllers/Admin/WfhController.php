<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Dispute, WfhOrder};
use App\Services\WfhService;
use Illuminate\Http\Request;

class WfhController extends Controller
{
    public function __construct(private WfhService $wfhService) {}

    // Monitoring semua order WFH
    public function index(Request $request)
    {
        $query = WfhOrder::with(['pelanggan', 'mitra', 'mitraLayanan.masterLayanan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('order_code', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.wfh.index', compact('orders'));
    }

    // Detail order
    public function show(WfhOrder $wfhOrder)
    {
        $wfhOrder->load(['pelanggan', 'mitra', 'mitraLayanan.masterLayanan', 'escrowLedgers']);
        $dispute = Dispute::where('order_type', 'wfh')
            ->where('order_id', $wfhOrder->id)->first();

        return view('admin.wfh.show', compact('wfhOrder', 'dispute'));
    }

    // Resolusi dispute
    public function resolusiDispute(Request $request, WfhOrder $wfhOrder)
    {
        $request->validate([
            'resolusi' => 'required|in:refund_to_customer,release_to_mitra',
        ]);

        try {
            $this->wfhService->selesaikanDispute($wfhOrder, $request->resolusi);

            Dispute::where('order_type', 'wfh')
                ->where('order_id', $wfhOrder->id)
                ->update([
                    'status'      => 'resolved',
                    'resolution'  => $request->resolusi,
                    'admin_notes' => $request->admin_notes,
                    'resolved_by' => auth()->id(),
                    'resolved_at' => now(),
                ]);

            return redirect()->route('admin.wfh.show', $wfhOrder->id)
                ->with('success', 'Dispute berhasil diselesaikan.');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
