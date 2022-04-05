<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        # code...
        return view('user_addresses.index',[
            'addresses' => $request->user()->addresses
        ]);
    }
}
