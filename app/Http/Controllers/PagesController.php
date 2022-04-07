<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Undocumented function
     *
     * @return void
     * @author Nick <thorfinnkarlsefni@icloud.com>
     * @date 2022 04 05
     */
    public function root()
    {
        return view('pages.root');
    }
}
