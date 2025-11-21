<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Services\CustomWalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DepositRequestController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions
        if (! Auth::user()->hasPermission('process_deposit') && ! Auth::user()->hasPermission('view_deposit_requests')) {
            abort(403, 'You do not have permission to access deposit requests.');
        }

        $user = Auth::user();
        $agent = $user;

        $startDate = $request->start_date ?? Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $deposits = DepositRequest::with(['user', 'bank', 'agent'])
            ->where('agent_id', $agent->id)
            ->when($request->filled('status') && $request->input('status') !== 'all', function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->orderBy('id', 'desc')
            ->get();

        $totalDeposits = $deposits->sum('amount');

        return view('admin.deposit_request.index', compact('deposits', 'totalDeposits'));
    }

    public function statusChangeIndex(Request $request, DepositRequest $deposit)
    {
        // Check permissions
        if (! Auth::user()->hasPermission('process_deposit')) {
            abort(403, 'You do not have permission to process deposits.');
        }

        try {
            $user = Auth::user();
            $agent = $user;

            // Check if user has permission to handle this deposit
            if ($deposit->agent_id !== $agent->id) {
                return redirect()->back()->with('error', 'You do not have permission to handle this deposit request!');
            }

            $player = User::find($request->player);

            if ($request->status == 1 && $agent->balance < $request->amount) {
                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            $note = 'Deposit request approved by '.$user->user_name.' on '.Carbon::now()->timezone('Asia/Yangon')->format('d-m-Y H:i:s');

            $deposit->update([
                'status' => $request->status,
                'note' => $note,
            ]);

            if ($request->status == 1) {
                $old_balance = $player->balance;
                app(CustomWalletService::class)->transfer($agent, $player, $request->amount,
                    TransactionName::TopUp, [
                        'old_balance' => $old_balance,
                        'new_balance' => $old_balance + $request->amount,
                    ]
                );
                \App\Models\TransferLog::create([
                    'from_user_id' => $agent->id,
                    'to_user_id' => $player->id,
                    'amount' => $request->amount,
                    'type' => 'top_up',
                    'description' => 'Deposit request '.$deposit->id.' approved by '.$user->user_name,
                    'meta' => [
                        'deposit_request_id' => $deposit->id,
                        'player_old_balance' => $old_balance,
                        'player_new_balance' => $old_balance + $request->amount,
                        'refrence_no' => $deposit->refrence_no,
                        'handled_by' => $user->user_name,
                    ],
                ]);
            }

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusChangeReject(Request $request, DepositRequest $deposit)
    {
        // Check permissions
        if (! Auth::user()->hasPermission('process_deposit')) {
            abort(403, 'You do not have permission to process deposits.');
        }

        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        try {
            $user = Auth::user();
            $agent = $user;

            // Check if user has permission to handle this deposit
            if ($deposit->agent_id !== $agent->id) {
                return redirect()->back()->with('error', 'You do not have permission to handle this deposit request!');
            }

            $note = 'Deposit request rejected by '.$user->user_name.' on '.Carbon::now()->timezone('Asia/Yangon')->format('d-m-Y H:i:s');

            $deposit->update([
                'status' => $request->status,
                'note' => $note,
            ]);

            \App\Models\TransferLog::create([
                'from_user_id' => $agent->id,
                'to_user_id' => $deposit->user_id,
                'amount' => $deposit->amount,
                'type' => 'deposit-reject',
                'description' => 'Deposit request '.$deposit->id.' rejected by '.$user->user_name,
                'meta' => [
                    'deposit_request_id' => $deposit->id,
                    'status' => 'rejected',
                    'refrence_no' => $deposit->refrence_no,
                    'handled_by' => $user->user_name,
                ],
            ]);

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function view(DepositRequest $deposit)
    {
        // Check permissions
        if (! Auth::user()->hasPermission('process_deposit') && ! Auth::user()->hasPermission('view_deposit_requests')) {
            abort(403, 'You do not have permission to view deposit requests.');
        }

        $user = Auth::user();
        $agent = $user;

        // Check if user has permission to handle this deposit
        if ($deposit->agent_id !== $agent->id) {
            return redirect()->back()->with('error', 'You do not have permission to handle this deposit request!');
        }

        return view('admin.deposit_request.view', compact('deposit'));
    }

    // log deposit request
    public function DepositShowLog(DepositRequest $deposit)
    {
        // Check permissions
        if (! Auth::user()->hasPermission('process_deposit') && ! Auth::user()->hasPermission('view_deposit_requests')) {
            abort(403, 'You do not have permission to view deposit logs.');
        }

        $user = Auth::user();
        $agent = $user;

        // Check if user has permission to handle this deposit
        if ($deposit->agent_id !== $agent->id) {
            return redirect()->back()->with('error', 'You do not have permission to handle this deposit request!');
        }

        return view('admin.deposit_request.log', compact('deposit'));
    }
}
