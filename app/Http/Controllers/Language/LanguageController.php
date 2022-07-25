<?php

namespace App\Http\Controllers\Language;

use App;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{

    /**
     * Switch language
     *
     * @param $locale
     * @return RedirectResponse
     */
    public function switch($locale)
    {

        // Set locale
        App::setLocale($locale);

        // Add to session
        session()->put('locale', $locale);

        // Redirect back
        return redirect()->back();

    }

}
