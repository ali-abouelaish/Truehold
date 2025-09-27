<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallLogRequest;
use App\Http\Requests\UpdateCallLogRequest;
use App\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CallLog::with('agent');

        // Filter by agent
        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by call status
        if ($request->has('call_status')) {
            $query->where('call_status', $request->call_status);
        }

        // Filter by call type
        if ($request->has('call_type')) {
            $query->where('call_type', $request->call_type);
        }

        // Filter by landlord name
        if ($request->has('landlord_name')) {
            $query->where('landlord_name', 'like', '%' . $request->landlord_name . '%');
        }

        // Filter by property address
        if ($request->has('property_address')) {
            $query->where('property_address', 'like', '%' . $request->property_address . '%');
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('call_datetime', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('call_datetime', '<=', $request->date_to);
        }

        // Filter by call outcome
        if ($request->has('call_outcome')) {
            $query->where('call_outcome', $request->call_outcome);
        }

        // Order by call datetime (most recent first)
        $query->orderBy('call_datetime', 'desc');

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $callLogs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $callLogs,
            'message' => 'Call logs retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCallLogRequest $request): JsonResponse
    {
        $callLog = CallLog::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $callLog->load('agent'),
            'message' => 'Call log created successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(CallLog $callLog): JsonResponse
    {
        // Ensure the user can only access their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to call log'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'success' => true,
            'data' => $callLog->load('agent'),
            'message' => 'Call log retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCallLogRequest $request, CallLog $callLog): JsonResponse
    {
        // Ensure the user can only update their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to call log'
            ], Response::HTTP_FORBIDDEN);
        }

        $callLog->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $callLog->load('agent'),
            'message' => 'Call log updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CallLog $callLog): JsonResponse
    {
        // Ensure the user can only delete their own call logs or is an admin
        if (auth()->user()->id !== $callLog->agent_id && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to call log'
            ], Response::HTTP_FORBIDDEN);
        }

        $callLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Call log deleted successfully'
        ]);
    }

    /**
     * Get call logs statistics for dashboard
     */
    public function stats(Request $request): JsonResponse
    {
        $query = CallLog::query();

        // Filter by agent if specified
        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by date range if specified
        if ($request->has('date_from')) {
            $query->where('call_datetime', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
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

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Call log statistics retrieved successfully'
        ]);
    }

    /**
     * Get call logs that need follow-up
     */
    public function followUps(Request $request): JsonResponse
    {
        $query = CallLog::with('agent')
            ->where('follow_up_needed', true);

        // Filter by agent if specified
        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by overdue follow-ups
        if ($request->has('overdue') && $request->overdue) {
            $query->where(function ($q) {
                $q->whereNull('follow_up_datetime')
                  ->orWhere('follow_up_datetime', '<', now());
            });
        }

        $followUps = $query->orderBy('follow_up_datetime', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $followUps,
            'message' => 'Follow-up call logs retrieved successfully'
        ]);
    }

    /**
     * Get recent call logs for the authenticated agent
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        
        $recentCalls = CallLog::with('agent')
            ->where('agent_id', auth()->id())
            ->orderBy('call_datetime', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recentCalls,
            'message' => 'Recent call logs retrieved successfully'
        ]);
    }
}
