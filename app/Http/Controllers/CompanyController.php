<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Only SuperAdmin can view all companies
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $companies = Company::withCount(['users', 'shortUrls'])
            ->paginate(15);
        
        return view('companies.index', compact('companies'));
    }
}
