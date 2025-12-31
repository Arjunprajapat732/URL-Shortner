<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // SuperAdmin dashboard - show all companies and all short URLs
            $companies = Company::withCount(['users', 'shortUrls'])
                ->with(['shortUrls' => function ($query) {
                    $query->selectRaw('company_id, SUM(hits) as total_hits')
                        ->groupBy('company_id');
                }])
                ->get();
            
            $totalUrls = ShortUrl::count();
            $totalHits = ShortUrl::sum('hits');
            
            // Get filter from request, default to 'today'
            $filter = $request->get('filter', 'today');
            
            // Apply filter to recent short URLs
            $query = ShortUrl::with(['user', 'company']);
            $query = $this->applyDateFilter($query, $filter);
            $recentShortUrls = $query->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return view('dashboard.super-admin', compact('companies', 'totalUrls', 'totalHits', 'recentShortUrls', 'filter'));
        } 
        elseif ($user->isAdmin()) {
            // Admin dashboard - show company stats and team members
            $company = $user->company;
            $teamMembers = User::where('company_id', $company->id)
                ->with(['shortUrls' => function ($query) {
                    $query->selectRaw('user_id, SUM(hits) as total_hits')
                        ->groupBy('user_id');
                }])
                ->withCount('shortUrls')
                ->get();
            
            $companyShortUrls = ShortUrl::where('company_id', $company->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $totalUrls = ShortUrl::where('company_id', $company->id)->count();
            $totalHits = ShortUrl::where('company_id', $company->id)->sum('hits');
            
            return view('dashboard.admin', compact('company', 'teamMembers', 'companyShortUrls', 'totalUrls', 'totalHits'));
        } 
        elseif ($user->isMember()) {
            // Member dashboard - show only their short URLs
            $shortUrls = ShortUrl::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $totalUrls = ShortUrl::where('user_id', $user->id)->count();
            $totalHits = ShortUrl::where('user_id', $user->id)->sum('hits');
            
            return view('dashboard.member', compact('shortUrls', 'totalUrls', 'totalHits'));
        }
        
        abort(403, 'Unauthorized access.');
    }
    
    /**
     * Apply date filter to query.
     */
    private function applyDateFilter($query, $filter)
    {
        $now = now();
        
        switch ($filter) {
            case 'today':
                return $query->whereDate('created_at', $now->toDateString());
            case 'last_week':
                $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
                $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();
                return $query->whereBetween('created_at', [
                    $lastWeekStart,
                    $lastWeekEnd
                ]);
            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                return $query->whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year);
            default:
                return $query;
        }
    }
    
    /**
     * Download short URLs for SuperAdmin dashboard.
     */
    public function downloadShortUrls(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $filter = $request->get('filter', 'today');
        
        $query = ShortUrl::with(['user', 'company']);
        $query = $this->applyDateFilter($query, $filter);
        $shortUrls = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'short_urls_' . $filter . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($shortUrls) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['Short URL', 'Long URL', 'Hits', 'Company', 'User', 'Created On']);
            
            // CSV Data
            foreach ($shortUrls as $shortUrl) {
                fputcsv($file, [
                    url('/s/' . $shortUrl->short_code),
                    $shortUrl->long_url,
                    $shortUrl->hits,
                    $shortUrl->company->name ?? 'N/A',
                    $shortUrl->user->name ?? 'N/A',
                    $shortUrl->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
