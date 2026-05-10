<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Dispute, JastipOrder};
use App\Services\JastipService;
use Illuminate\Http\Request;

class JastipController extends Controller
{
    public function __construct(private JastipService $jastipService) {}

    public function index(Request $request)
    {
        $query = JastipOrder::with(['pelanggan', 'mitra']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('order_code', 'like', '%' . $request->search . '%');

        $orders = $query->latest()->paginate(20);
        return view('admin.jastip.index', compact('orders'));
    }

    public function show(JastipOrder $jastipOrder)
    {
        $jastipOrder->load(['pelanggan', 'mitra', 'stops.items']);
        $dispute = Dispute::where('order_type', 'jastip')->where('order_id', $jastipOrder->id)->first();
        return view('admin.jastip.show', compact('jastipOrder', 'dispute'));
    }

    public function resolusiDispute(Request $request, JastipOrder $jastipOrder)
    {
        $request->validate(['resolusi' => 'required|in:release_to_mitra,refund_commission']);

        try {
            $this->jastipService->selesaikanDispute($jastipOrder, $request->resolusi);

            Dispute::where('order_type', 'jastip')
                ->where('order_id', $jastipOrder->id)
                ->update([
                    'status'      => 'resolved',
                    'resolution'  => $request->resolusi,
                    'admin_notes' => $request->admin_notes,
                    'resolved_by' => auth()->id(),
                    'resolved_at' => now(),
                ]);

            return back()->with('success', 'Dispute selesai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
