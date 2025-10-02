<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallLogRequest;
use App\Http\Requests\UpdateCallLogRequest;
use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallLog::with('agent');

        // Filter by agent
        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by call status
        if ($request->has('call_status') && $request->call_status) {
            $query->where('call_status', $request->call_status);
        }

        // Filter by call type
        if ($request->has('call_type') && $request->call_type) {
            $query->where('call_type', $request->call_type);
        }

        // Filter by landlord name
        if ($request->has('landlord_name') && $request->landlord_name) {
            $query->where('landlord_name', 'like', '%' . $request->landlord_name . '%');
        }

        // Filter by property address
        if ($request->has('property_address') && $request->property_address) {
            $query->where('property_address', 'like', '%' . $request->property_address . '%');
        }

        // Filter by phone number
        if ($request->has('landlord_phone') && $request->landlord_phone) {
            $query->where('landlord_phone', 'like', '%' . $request->landlord_phone . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('call_datetime', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('call_datetime', '<=', $request->date_to);
        }

        // Filter by call outcome
        if ($request->has('call_outcome') && $request->call_outcome) {
            $query->where('call_outcome', $request->call_outcome);
        }

        // Order by call datetime (most recent first)
        $query->orderBy('call_datetime', 'desc');

        // Get all results (no pagination for simple web interface)
        $callLogs = $query->get();
        
        // Get agents for filter dropdown
        $agents = User::where('role', 'agent')->get();

        return view('admin.call-logs.index', compact('callLogs', 'agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.call-logs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCallLogRequest $request)
    {
        $data = $request->validated();
        $data['agent_id'] = auth()->id();
        
        $callLog = CallLog::create($data);

        return redirect()->route('admin.call-logs.index')
            ->with('success', 'Call log created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CallLog $callLog)
    {
        // Ensure the user can only access their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to call log');
        }

        return view('admin.call-logs.show', compact('callLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallLog $callLog)
    {
        // Ensure the user can only edit their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to call log');
        }

        return view('admin.call-logs.edit', compact('callLog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCallLogRequest $request, CallLog $callLog)
    {
        // Ensure the user can only update their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to call log');
        }

        $callLog->update($request->validated());

        return redirect()->route('admin.call-logs.index')
            ->with('success', 'Call log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CallLog $callLog)
    {
        // Ensure the user can only delete their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to call log');
        }

        $callLog->delete();

        return redirect()->route('admin.call-logs.index')
            ->with('success', 'Call log deleted successfully.');
    }

    /**
     * Get call logs statistics for dashboard
     */
    public function stats(Request $request)
    {
        $query = CallLog::query();

        // Filter by agent if specified
        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by date range if specified
        if ($request->has('date_from') && $request->date_from) {
            $query->where('call_datetime', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('call_datetime', '<=', $request->date_to);
        }

        $stats = [
            'total_calls' => $query->count(),
            'calls_by_type' => $query->groupBy('call_type')
                ->selectRaw('call_type, count(*) as count')
                ->pluck('count', 'call_type'),
            'calls_by_status' => $query->groupBy('call_status')
                ->selectRaw('call_status, count(*) as count')
                ->pluck('count', 'call_status'),
            'calls_by_outcome' => $query->groupBy('call_outcome')
                ->selectRaw('call_outcome, count(*) as count')
                ->pluck('count', 'call_outcome'),
            'instruction_won_count' => $query->where('call_outcome', 'instruction_won')->count(),
            'pending_count' => $query->where('call_outcome', 'pending')->count(),
            'follow_up_needed_count' => $query->where('follow_up_needed', true)->count(),
            'viewing_booked_count' => $query->where('viewing_booked', true)->count(),
        ];

        return view('admin.call-logs.stats', compact('stats'));
    }

    /**
     * Get call logs that need follow-up
     */
    public function followUps(Request $request)
    {
        $query = CallLog::with('agent')
            ->where('follow_up_needed', true);

        // Filter by agent if specified
        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by overdue follow-ups
        if ($request->has('overdue') && $request->overdue) {
            $query->where(function ($q) {
                $q->whereNull('follow_up_datetime')
                  ->orWhere('follow_up_datetime', '<', now());
            });
        }

        $followUps = $query->orderBy('follow_up_datetime', 'asc')->get();
        $agents = User::where('role', 'agent')->get();

        return view('admin.call-logs.follow-ups', compact('followUps', 'agents'));
    }

    /**
     * Get recent call logs for the authenticated agent
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $recentCalls = CallLog::with('agent')
            ->where('agent_id', auth()->id())
            ->orderBy('call_datetime', 'desc')
            ->limit($limit)
            ->get();

        return view('admin.call-logs.recent', compact('recentCalls'));
    }

    /**
     * Check if a phone number has been called before
     */
    public function checkPhone(Request $request)
    {
        $phone = $request->get('phone');
        $excludeId = $request->get('exclude_id');

        if (empty($phone)) {
            return response()->json([
                'has_been_called' => false,
                'call_history' => null
            ]);
        }

        $hasBeenCalled = CallLog::hasBeenCalledBefore($phone, $excludeId);
        $callHistory = null;

        if ($hasBeenCalled) {
            $callHistory = CallLog::getCallHistorySummary($phone, $excludeId);
        }

        return response()->json([
            'has_been_called' => $hasBeenCalled,
            'call_history' => $callHistory
        ]);
    }

    /**
     * Get previous calls for a phone number
     */
    public function getPreviousCalls(Request $request)
    {
        $phone = $request->get('phone');
        $excludeId = $request->get('exclude_id');

        if (empty($phone)) {
            return response()->json(['calls' => []]);
        }

        $previousCalls = CallLog::getPreviousCalls($phone, $excludeId);

        return response()->json([
            'calls' => $previousCalls->map(function ($call) {
                return [
                    'id' => $call->id,
                    'call_datetime' => $call->call_datetime->format('Y-m-d H:i'),
                    'call_type' => $call->call_type,
                    'call_status' => $call->call_status,
                    'call_outcome' => $call->call_outcome,
                    'landlord_name' => $call->landlord_name,
                    'property_address' => $call->property_address,
                    'agent_notes' => $call->agent_notes,
                    'agent_name' => $call->agent->name ?? 'Unknown'
                ];
            })
        ]);
    }

    /**
     * Update next step status for a call log.
     */
    public function updateNextStep(Request $request, CallLog $callLog)
    {
        $request->validate([
            'next_step_status' => 'nullable|in:send_terms,send_compliance_docs,awaiting_response,collect_keys,tenant_reference_started,other'
        ]);

        $callLog->update([
            'next_step_status' => $request->next_step_status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Next step status updated successfully',
            'next_step_status' => $callLog->next_step_status
        ]);
    }
}
