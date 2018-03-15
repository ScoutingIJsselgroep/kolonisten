<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\User;
use App\Models\UserLocation;
use Carbon\Carbon;

class LocationsController extends Controller
{
	public function qr(Request $request, $qr) {
		$location = Location::where('code', '=', $qr)->where('locations.available', '<', Carbon::now()->format('H:i:s'))->first();
		if($location) {
			if($request->has('confirm')) {
				if($request->session()->has('user')) {
					$user = User::find($request->session()->get('user'));
					if($user->lock !== $request->session()->get('user_lock')) {
						$request->session()->flush();
						return redirect()->to('/')->with([
							'error' => 'Afgemeld',
							'message' => 'Je bent afgemeld door de spelleiding' 
						]);
					}
					
					$userLocation = UserLocation::where('user_id', '=', $user->id)->where('location_id', '=', $location->id)->first();
					if(!$userLocation) {
						$userLocation = new UserLocation;
						$userLocation->user_id = $user->id;
						$userLocation->location_id = $location->id;
						$userLocation->scan = Carbon::now();
						$userLocation->save();
						
						return redirect()->to('/')->with([
							'title' => 'Locatie ' . $location->name . ' gevonden',
							'message' => 'Gefeliciteerd met het vinden van deze locatie! Jullie verdienen 2 ' . \App\Models\Location::getElements()[$location->element]
						]);
					}
					return redirect()->to('/')->with([
						'error' => 'Locatie ' . $location->name,
						'message' => 'Deze locatie hadden jullie al gevonden' 
					]);
				} else {
					return redirect()->to('/')->with([
						'error' => 'Team onbekend',
						'message' => 'Meld je eerst aan' 
					]);
				}
			} else {
				return view('continue', [
					'title' => 'Locatie ' . $location->name . ' gevonden',
					'link' => $request->url . '?confirm'
				]);
			}
		} else {
			return redirect()->to('/')->with([
				'error' => 'Locatie niet gevonden',
				'message' => 'Misschien later in het spel' 
			]);
		}
	}
	
	
	
	
	public function index(Request $request) {
		// $request;
		
		return view('locations.index', [
			'locations' => Location::all()
		]);
	}
	public function table(Request $request) {
		// $request;
		if($request->has('print')) {
			return view('locations.print', [
				'locations' => Location::orderBy('available')->get()
			]);
		} else {
			return view('locations.table', [
				'locations' => Location::orderBy('available')->get()
			]);
		}
	}
	
	public function add(Request $request) {
		$location = new Location;
		$location->fill($request->all());
		$location->code = str_random(10);
		$location->save();
		
		return redirect()->back();
	}
	
	public function edit(Request $request, Location $location) {
		$location->fill($request->all());
		$location->save();
		
		return redirect()->back();
	}
	
	public function delete(Request $request, Location $location) {
		$location->delete();
		
		return redirect()->back();
	}
	
	public function icon(Request $request, Location $location) {
		$img = imagecreatefrompng(public_path('img/marker.png'));
		imagesavealpha($img, true);
		
		$element = imagecreatefrompng(public_path('img/' . $location->element . '.png'));
		imagecopyresampled($img, $element, 5, 0, 0, 0, 30, 30, 100, 100);
		imagedestroy($element);
		imagesavealpha($img, true);
		
		header('Content-Type: image/png');
		imagepng($img);
		imagedestroy($img);
		die();
	}
	
	public function teams(Request $request) {
		if($request->session()->has('user')) {
			$user = User::find($request->session()->get('user'));
		} else {
			$user = false;
		}
		return view('locations.teams', [
			'user' => $user,
			'locations' => Location::leftJoin('user_locations', function($join) {
				$join->on('user_locations.location_id', '=', 'locations.id')
						->whereNotNull('user_locations.house');
			})->leftJoin('users', function($join) {
				$join->on('user_locations.user_id', '=', 'users.id');
			})->where('locations.available', '<', Carbon::now()->format('H:i:s'))->select('locations.*', 'users.name', 'user_locations.scan', 'user_locations.flag', 'user_locations.house', 'user_locations.bb', 'user_locations.cafe')->get()
		]);
	}
}
