<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;

class PublicShortUrlController extends Controller
{
    /**
     * Redirect to the original URL.
     */
    public function redirect($shortCode)
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->firstOrFail();
        
        // Increment hits
        $shortUrl->incrementHits();
        
        // Redirect to the long URL
        return redirect($shortUrl->long_url);
    }
}
