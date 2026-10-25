<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function export(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $data = $request->validate(['from' => 'nullable|date|before_or_equal:to', 'to' => 'nullable|date|after_or_equal:from']);
        $from = isset($data['from']) ? Carbon::parse($data['from'])->startOfDay() : now()->subDays(29)->startOfDay();
        $to = isset($data['to']) ? Carbon::parse($data['to'])->endOfDay() : now()->endOfDay();
        $rows = DB::table('users')->selectRaw('DATE(created_at) as day, COUNT(*) as signups, SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as verified')
            ->whereBetween('created_at', [$from, $to])->groupByRaw('DATE(created_at)')->orderBy('day')->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['date', 'signups', 'verified signups']);
            foreach ($rows as $row) fputcsv($out, [$row->day, $row->signups, $row->verified]);
            fclose($out);
        }, 'analytics.csv', ['Content-Type' => 'text/csv']);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $data = $request->validate(['from' => 'nullable|date|before_or_equal:to', 'to' => 'nullable|date|after_or_equal:from']);
        $from = isset($data['from']) ? Carbon::parse($data['from'])->startOfDay() : now()->subDays(29)->startOfDay();
        $to = isset($data['to']) ? Carbon::parse($data['to'])->endOfDay() : now()->endOfDay();
        $base = User::query()->whereBetween('created_at', [$from, $to]);
        $signups = (clone $base)->count();
        $verified = (clone $base)->whereNotNull('email_verified_at')->count();
        $totalUsers = User::count();
        $monthly = DB::table('users')->selectRaw('DATE(created_at) as day, COUNT(*) as total, SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as verified')
            ->whereBetween('created_at', [$from, $to])->groupByRaw('DATE(created_at)')->orderBy('day')->get();
        return view('analytics.index', compact('from', 'to', 'signups', 'verified', 'totalUsers', 'monthly'));
    }
}
