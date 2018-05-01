<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\UserLocation;
use Carbon\Carbon;

class UsersController extends Controller
{
	public function home(Request $request) {
		if($request->session()->has('user')) {
			$user = User::find($request->session()->get('user'));
			if($user->lock !== $request->session()->get('user_lock')) {
				$request->session()->flush();
				return redirect()->to('/')->with([
					'error' => 'Afgemeld',
					'message' => 'Je bent afgemeld door de spelleiding' 
				]);
			}
			
			if($request->ajax()) {
				return $this->checkup($request, $user);
			}
			// todo locaties tonen na tijdstip dat ze open zijn
			return view('users.overview', [
				'user' => $user,
				'locations' => Location::leftJoin('user_locations', function($join) use ($user) {
					$join->on('user_locations.location_id', '=', 'locations.id')
							->where('user_locations.user_id', '=', $user->id);
				})->whereIn('locations.id', $user->availableLocations())
				->select('locations.*', 'user_locations.user_id', 'user_locations.scan', 'user_locations.buy')->get()
			]);
		} else {
			return view('users.home');
		}
	}
	
	public function archived(Request $request) {
		if($request->session()->has('user')) {
			$user = User::find($request->session()->get('user'));
			if($user->lock !== $request->session()->get('user_lock')) {
				$request->session()->flush();
				return redirect()->to('/')->with([
					'error' => 'Afgemeld',
					'message' => 'Je bent afgemeld door de spelleiding' 
				]);
			}
			
			
			return view('users.archived', [
				'user' => $user,
				'winner' => User::getWinner()
			]);
		} else {
			return redirect()->to('/');
		}
	}
	public function checkup(Request $request, $user) {
		// nieuwe locaties erbij, en is er al gebouwd door andere groepen
		$locations = [];
		foreach(Location::leftJoin('user_locations', function($join) use ($user) {
				$join->on('user_locations.location_id', '=', 'locations.id')
						->where('user_locations.user_id', '=', $user->id);
			})->whereIn('locations.id', $user->availableLocations())
			->select('locations.*', 'user_locations.user_id', 'user_locations.scan', 'user_locations.buy')->get() as $location) {
			if($location->buy) {
				$step = 1;
			} else if($location->scan) {
				$step = 0;
			} else {
				$step = null;
			}
			
			$locations[] = [
				'id' => $location->id,
				'element' => ($location->user_id ? $location->element : 'unknown'),
				'step' => $step,
				'name' => $location->name,
				'lat' => (float)$location->lat,
				'lng' => (float)$location->lng
			];
		}
		
		// alle elementen? Dan de draak locatie tonen
		$hasAll = true;
		$hasElements = $user->hasElements();
		foreach($hasElements as $check) {
			if(!$check) {
				$hasAll = false;
				break;
			}
		}
		if($hasAll) {
			$locations[] = [
				'id' => 0,
				'element' => 'draak',
				'step' => null,
				'name' => 'Draak',
				'lat' => 52.19690360262851,
				'lng' => 6.2151158749220485
			];
		}
		
		// je huidige elementen
		return [
			'locations' => $locations,
			'elements' => $hasElements,
			'winner' => (User::getWinner() ? true : false)
		];
	}
	
	public function info(Request $request) {
		if($request->session()->has('user')) {
			$user = User::find($request->session()->get('user'));
			if($user->lock !== $request->session()->get('user_lock')) {
				$request->session()->flush();
				return redirect()->to('/')->with([
					'error' => 'Afgemeld',
					'message' => 'Je bent afgemeld door de spelleiding' 
				]);
			}
			return view('users.info');
		}
		return redirect()->to('/');
	}
	
	public function qr(Request $request, $qr) {
		$user = User::where('code', '=', $qr)->first();
		if($user) {
			if($request->has('confirm')) {
				if($user->lock) {
					return redirect()->to('/')->with([
						'title' => 'Team ' . $user->name . ',',
						'message' => 'Deze code is al gescand' 
					]);
				}
				$user->lock = str_random(10);
				$user->save();
				$request->session()->put('user', $user->id);
				$request->session()->put('user_lock', $user->lock);

				return redirect()->to('/')->with([
					'title' => 'Team ' . $user->name . ',',
					'message' => 'Gefeliciteerd jullie kunnen beginnen' 
				]);
			} else {
				return view('continue', [
					'title' => 'Aanmelden team ' . $user->name,
					'link' => $request->url . '?confirm'
				]);
			}
		} else {
			return redirect()->to('/');
		}
	}
	
	public function buy(Request $request, Location $location) {
		if($request->session()->has('user')) {
			$user = User::find($request->session()->get('user'));
			if($user->lock !== $request->session()->get('user_lock')) {
				$request->session()->flush();
				return redirect()->to('/')->with([
					'error' => 'Afgemeld',
					'message' => 'Je bent afgemeld door de spelleiding' 
				]);
			}
			
			$userLocation = UserLocation::where('user_id', '=', $request->session()->get('user'))
					->where('location_id', '=', $location->id)->first();
			if(!$userLocation) {
				return redirect()->to('/')->with([
					'error' => 'Helaas',
					'message' => 'Deze plek moet je nog ontdekken' 
				]);
			} else {
				if(!$userLocation->buy) {
					$userLocation->buy = Carbon::now();
					$userLocation->save();
					
					// zijn alle spullen gekocht, dan mag de draak worden gezocht
					foreach($user->hasElements() as $check) {
						if(!$check) {
							return redirect()->to('/')->with([
								'title' => 'Gefeliciteerd!',
								'message' => 'Je bent nu in het bezit van een ' . $location->elementName()
							]);
						}
					}
					
					return redirect()->to('/')->with([
						'title' => 'Gefeliciteerd!',
						'message' => 'Je bezit nu alles wat nodig is om de draak te verslaan! Haal de fiets op en ga hem zoeken'
					]);
				}
			}
		}
		return redirect()->to('/');
	}
	public function iconDragon() {
		$img = imagecreatefrompng(public_path('img/draakje.png'));
		imagesavealpha($img, true);
		
		header('Content-Type: image/png');
		imagepng($img);
		imagedestroy($img);
		die();
	}
	
	public function icon(Request $request, Location $location, $step = null, $team = false) {
		$img = imagecreatefrompng(public_path('img/marker.png'));
		imagesavealpha($img, true);
		
		if($team) {
			$userLocation = UserLocation::leftJoin('users', function($join) {
					$join->on('user_locations.user_id', '=', 'users.id');
				})->whereNotNull('user_locations.house')
					->where('location_id', '=', $location->id)
					->select('users.name', 'user_locations.scan', 'user_locations.buy')->first();
			if($userLocation && $step !== null) {
				// al gescand
				$element = imagecreatefrompng(public_path('img/' . strtolower($userLocation->name) . '.png'));
				imagecopyresampled($img, $element, 5, 0, 0, 0, 30, 30, 40, 40);
				imagedestroy($element);
				imagesavealpha($img, true);
				
				/*
				// al iets gebouwd
				if($userLocation->cafe) {
					$p = imagecreatefrompng(public_path('img/p' . 4 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->bb) {
					$p = imagecreatefrompng(public_path('img/p' . 3 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->house) {
					$p = imagecreatefrompng(public_path('img/p' . 2 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				}*/
			}
			
		} else if($request->session()->has('user')) {
			$userLocation = UserLocation::where('user_id', '=', $request->session()->get('user'))
					->where('location_id', '=', $location->id)->first();
			if($userLocation && $step !== null) {
				// al gescand
				$element = imagecreatefrompng(public_path('img/' . $location->element . '.png'));
				imagecopyresampled($img, $element, 5, 0, 0, 0, 30, 30, 40, 40);
				imagedestroy($element);
				imagesavealpha($img, true);
				
				// al iets gebouwd
				if($userLocation->cafe) {
					$p = imagecreatefrompng(public_path('img/p' . 4 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->bb) {
					$p = imagecreatefrompng(public_path('img/p' . 3 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->house) {
					$p = imagecreatefrompng(public_path('img/p' . 2 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->flag) {
					$p = imagecreatefrompng(public_path('img/p' . 1 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				}
			}
		}
		
		header('Content-Type: image/png');
		imagepng($img);
		imagedestroy($img);
		die();
	}
	
	public function index(Request $request) {
		// $request;
		
		return view('users.index', [
			'users' => User::leftJoin('user_locations', function($join) {
				$join->on('user_locations.user_id', '=', 'users.id');
			})->orderByRaw('count(user_locations.buy) desc')
					->orderByRaw('count(user_locations.id) desc')
					->groupBy('users.id')->select('users.*')->get()
		]);
	}
	
	public function teams(Request $request) {
		if($request->session()->has('user')) {
			$user = User::find($request->session()->get('user'));
			if($user->lock !== $request->session()->get('user_lock')) {
				$request->session()->flush();
				return redirect()->to('/')->with([
					'error' => 'Afgemeld',
					'message' => 'Je bent afgemeld door de spelleiding' 
				]);
			}
			
			return view('users.teams', [
				'self' => $user,
				'users' => User::leftJoin('user_locations', function($join) {
					$join->on('user_locations.user_id', '=', 'users.id');
				})->orderByRaw('ISNULL(draak), draak asc')
						->orderByRaw('count(user_locations.buy) desc')
						->orderByRaw('count(user_locations.id) desc')
						->groupBy('users.id')->select('users.*')->get()
			]);
		} else {
			return redirect()->to('/');
		}
	}
	
	public function add(Request $request) {
		$user = new User;
		$user->name = $request->name;
		
		return redirect()->back();
	}
	
	public function unlock(Request $request, User $user) {
		// $request;
		$user->lock = null;
		$user->save();
		
		return redirect()->back();
	}
	
	public function score(Request $request, User $user, $amount) {
		// $request;
		$user->henx = $amount;
		$user->save();
		
		return ['amount' => $user->countHenx()];
	}
}
