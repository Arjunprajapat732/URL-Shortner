<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    /**
     * Show the form for creating a new invitation.
     */
    public function create()
    {
        $user = auth()->user();
        
        // SuperAdmin can invite Admin (creates new company)
        // Admin can invite Admin or Member (in their company)
        if ($user->isSuperAdmin()) {
            return view('invitations.create');
        } elseif ($user->isAdmin()) {
            return view('invitations.create', ['company' => $user->company]);
        }
        
        abort(403, 'Unauthorized action.');
    }

    /**
     * Store a newly created invitation.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // SuperAdmin can invite Admin for a new company
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|unique:invitations,email',
                'company_name' => 'required|string|max:255',
                'company_email' => 'required|email|unique:companies,email',
            ]);
            
            // Create company
            $company = Company::create([
                'name' => $validated['company_name'],
                'email' => $validated['company_email'],
            ]);
            
            // Create invitation
            $invitation = Invitation::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'Admin',
                'company_id' => $company->id,
                'invited_by' => $user->id,
            ]);
            
        } elseif ($user->isAdmin()) {
            // Admin can invite Admin or Member in their company
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|unique:invitations,email',
                'role' => ['required', Rule::in(['Admin', 'Member'])],
            ]);
            
            $invitation = Invitation::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'company_id' => $user->company_id,
                'invited_by' => $user->id,
            ]);
        } else {
            abort(403, 'Unauthorized action.');
        }
        
        // In a real application, you would send an email here
        // Mail::to($invitation->email)->send(new InvitationMail($invitation));
        
        return redirect()->route('dashboard')->with('success', 'Invitation sent successfully.');
    }

    /**
     * Accept an invitation.
     */
    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();
        
        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Process invitation acceptance.
     */
    public function processAccept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();
        
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Create user
        $user = \App\Models\User::create([
            'name' => $invitation->name,
            'email' => $invitation->email,
            'password' => bcrypt($validated['password']),
            'role' => $invitation->role,
            'company_id' => $invitation->company_id,
        ]);
        
        // Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);
        
        // Log in the user
        auth()->login($user);
        
        return redirect()->route('dashboard')->with('success', 'Account created successfully.');
    }
}
