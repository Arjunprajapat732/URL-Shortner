<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of team members.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Only Admin can view team members
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $company = $user->company;
        $teamMembers = User::where('company_id', $company->id)
            ->withCount('shortUrls')
            ->paginate(15);
        
        // Calculate total hits for each member
        foreach ($teamMembers as $member) {
            $member->total_hits = ShortUrl::where('user_id', $member->id)->sum('hits');
        }
        
        return view('team-members.index', compact('teamMembers'));
    }
}
