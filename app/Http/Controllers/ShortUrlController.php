<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'today');
        
        // Base query based on user role
        if ($user->isSuperAdmin()) {
            $query = ShortUrl::with(['user', 'company']);
        } elseif ($user->isAdmin()) {
            $query = ShortUrl::with(['user', 'company'])
                ->where('company_id', $user->company_id);
        } elseif ($user->isMember()) {
            $query = ShortUrl::with(['user', 'company'])
                ->where('user_id', $user->id);
        } else {
            abort(403, 'Unauthorized action.');
        }
        
        // Apply date filter
        $query = $this->applyDateFilter($query, $filter);
        
        $shortUrls = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('short-urls.index', compact('shortUrls', 'filter'));
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
     * Download short URLs as CSV.
     */
    public function download(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'today');
        
        // Base query based on user role
        if ($user->isSuperAdmin()) {
            $query = ShortUrl::with(['user', 'company']);
        } elseif ($user->isAdmin()) {
            $query = ShortUrl::with(['user', 'company'])
                ->where('company_id', $user->company_id);
        } elseif ($user->isMember()) {
            $query = ShortUrl::with(['user', 'company'])
                ->where('user_id', $user->id);
        } else {
            abort(403, 'Unauthorized action.');
        }
        
        // Apply date filter
        $query = $this->applyDateFilter($query, $filter);
        
        $shortUrls = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'short_urls_' . $filter . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($shortUrls, $user) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            $headers = ['Short URL', 'Long URL', 'Hits', 'Created On'];
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                $headers[] = 'User';
            }
            if ($user->isSuperAdmin()) {
                $headers[] = 'Company';
            }
            fputcsv($file, $headers);
            
            // CSV Data
            foreach ($shortUrls as $shortUrl) {
                $row = [
                    url('/s/' . $shortUrl->short_code),
                    $shortUrl->long_url,
                    $shortUrl->hits,
                    $shortUrl->created_at->format('Y-m-d H:i:s'),
                ];
                if ($user->isSuperAdmin() || $user->isAdmin()) {
                    $row[] = $shortUrl->user->name;
                }
                if ($user->isSuperAdmin()) {
                    $row[] = $shortUrl->company->name;
                }
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // SuperAdmin cannot create short URLs
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }
        
        // Admin and Member can create short URLs
        if (!$user->isAdmin() && !$user->isMember()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('short-urls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // SuperAdmin cannot create short URLs
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }
        
        // Admin and Member can create short URLs
        if (!$user->isAdmin() && !$user->isMember()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'long_url' => 'required|url|max:2048',
        ]);
        
        // Generate unique short code
        do {
            $shortCode = Str::random(8);
        } while (ShortUrl::where('short_code', $shortCode)->exists());
        
        $shortUrl = ShortUrl::create([
            'short_code' => $shortCode,
            'long_url' => $validated['long_url'],
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);
        
        return redirect()->route('short-urls.index')
            ->with('success', 'Short URL created successfully.')
            ->with('short_url', $shortUrl->short_url);
    }
}
