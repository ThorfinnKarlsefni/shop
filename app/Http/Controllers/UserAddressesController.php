<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
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

    public function create()
    {
        return view('user_addresses.create_and_edit',['address' => new UserAddress()]);
    }

    public function store(UserAddressRequest $request)
    {
        // dd(12313);
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'zip',
            'address',
            'contact_name',
            'contact_phone'
        ]));

        return redirect()->route('user_addresses.index');
    }
}
