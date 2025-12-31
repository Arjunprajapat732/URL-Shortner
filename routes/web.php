<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicShortUrlController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Public route for short URL redirection
Route::get('/s/{shortCode}', [PublicShortUrlController::class, 'redirect'])->name('short-url.redirect');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/download-short-urls', [DashboardController::class, 'downloadShortUrls'])->name('dashboard.download-short-urls');
    
    // Companies (SuperAdmin only)
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    
    // Short URLs
    Route::resource('short-urls', ShortUrlController::class)->except(['show', 'edit', 'update', 'destroy']);
    Route::get('/short-urls/download', [ShortUrlController::class, 'download'])->name('short-urls.download');
    
    // Team Members (Admin only)
    Route::get('/team-members', [TeamMemberController::class, 'index'])->name('team-members.index');
    
    // Invitations
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public invitation acceptance
Route::get('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitations/accept/{token}', [InvitationController::class, 'processAccept'])->name('invitations.process-accept');

require __DIR__.'/auth.php';
