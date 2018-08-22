<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\User;
use App\Models\UserLocation;
use Carbon\Carbon;

class AdminController extends Controller
{
	public function login(Request $request) {
		if ($request->isMethod('post')) {
			if($request->user == 'ijsselgroep' && $request->pass === env('IJSSELPASS', false)) {
				$request->session()->put('admin', true);
				return redirect()->to('/locations');
			}
			return redirect()->back()->withInput();
		}
		return view('admin.login');
	}
}
