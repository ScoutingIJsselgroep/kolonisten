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
				})->where('locations.available', '<', Carbon::now()->format('H:i:s'))
				->select('locations.*', 'user_locations.user_id', 'user_locations.scan', 'user_locations.fire', 'user_locations.coalenergy', 'user_locations.gasenergy', 'user_locations.sustainable')->get()
			]);
		} else {
			return view('users.home');
		}
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
				})
				->select(
					'users.*',
					\DB::raw('count(user_locations.sustainable) as sustainable'),
					\DB::raw('count(user_locations.gasenergy) as gasenergy'),
					\DB::raw('count(user_locations.coalenergy) as coalenergy'),
					\DB::raw('count(user_locations.fire) as fire')
				)
				->orderByRaw('count(user_locations.sustainable) desc')
				->orderByRaw('count(user_locations.gasenergy) desc')
				->orderByRaw('count(user_locations.coalenergy) desc')
				->orderByRaw('count(user_locations.fire) desc')
				->orderByRaw('count(user_locations.id) desc')
				->groupBy('users.id')->get()
			]);
		} else {
			return redirect()->to('/');
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
			})->where('locations.available', '<', Carbon::now()->format('H:i:s'))
			->select('locations.*', 'user_locations.user_id', 'user_locations.scan', 'user_locations.fire', 'user_locations.coalenergy', 'user_locations.gasenergy', 'user_locations.sustainable')->get() as $location) {
			if($location->sustainable) {
				$step = 4;
			} else if($location->gasenergy) {
				$step = 3;
			} else if($location->coalenergy) {
				$step = 2;
			} else if($location->fire) {
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
		
		// je huidige elementen
		return [
			'locations' => $locations,
			'elements' => $user->countElements(),
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
	
	public function build(Request $request, Location $location, $type) {
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
			} else if($type == 'fire') {
				if(!$userLocation->fire) {
					$userLocation->fire = Carbon::now();
					$userLocation->save();
					
					return redirect()->to('/')->with([
						'title' => 'Gefeliciteerd!',
						'message' => 'Je ontvangt nu iedere 3 minuten een extra ' . $location->elementName()
					]);
				}
			} else {
				$otherUserLocation = UserLocation::where('user_id', '<>', $request->session()->get('user'))
					->where('location_id', '=', $location->id)
					->whereNotNull('coalenergy')->first();
				if($otherUserLocation) {
					return redirect()->to('/')->with([
						'error' => 'Helaas',
						'message' => 'Team ' . $otherUserLocation->user->name . ' heeft hier al gebouwd' 
					]);
				} else if($type == 'coalenergy') {
					if(!$userLocation->fire) {
						return redirect()->to('/')->with([
							'error' => 'Helaas',
							'message' => 'Je moet hier eerst een vlag plaatsen' 
						]);
					} else if(!$userLocation->coalenergy) {
						$userLocation->coalenergy = Carbon::now();
						$userLocation->save();
						
						if($user->countCoalenergys()==1) {
							return redirect()->to('/team')->with([
								'title' => 'Gefeliciteerd!',
								'message' => 'Je eerste huis, je kunt een cadeau ophalen, kijk bij de teampagina, voor meer info. Je ontvangt nu iedere 2 minuten een extra ' . $location->elementName()
							]);
						} else {
							return redirect()->to('/')->with([
								'title' => 'Gefeliciteerd!',
								'message' => 'Je ontvangt nu iedere 2 minuten een extra ' . $location->elementName()
							]);
						}
					}
				} else if($type == 'gasenergy') {
					if(!$userLocation->coalenergy) {
						return redirect()->to('/')->with([
							'error' => 'Helaas',
							'message' => 'Je moet hier eerst een huis bouwen' 
						]);
					} else if(!$userLocation->gasenergy) {
						$userLocation->gasenergy = Carbon::now();
						$userLocation->save();
						
						// is het de eerste dan excursie bij de hoek
						
						if($user->countGasenergys()==1) {
							return redirect()->to('/team')->with([
								'title' => 'Gefeliciteerd!',
								'message' => 'Je eerste bed en breakfast, je kunt een cadeau ophalen, kijk bij de teampagina, voor meer info. Je ontvangt nu iedere minuut een extra ' . $location->elementName()
							]);
						} else {
							return redirect()->to('/')->with([
								'title' => 'Gefeliciteerd!',
								'message' => 'Je ontvangt nu iedere minuut een extra ' . $location->elementName()
							]);
						}
					}
				} else if($type == 'sustainable') {
					if(!$userLocation->gasenergy) {
						return redirect()->to('/')->with([
							'error' => 'Helaas',
							'message' => 'Je moet hier eerst een B&ampB bouwen' 
						]);
					} else if(!$userLocation->sustainable) {
						$userLocation->sustainable = Carbon::now();
						$userLocation->save();
						
						if($user->countSustainables()==2) {
							if($user->id == User::getWinner()) {
								return redirect()->to('/team')->with([
									'title' => 'Hulde!',
									'message' => 'Jullie hebben het doel gehaald, en twee caf&eacute;s geopend'
								]);
							} else {
								return redirect()->to('/team')->with([
									'title' => 'Ah, zo geoefend thuis!',
									'message' => 'Jullie hebben het doel gehaald, en twee caf&eacute;s geopend. Maar helaas, was een ander team jullie voor'
								]);
							}
						}
						
						return redirect()->to('/')->with([
							'title' => 'Gefeliciteerd!',
							'message' => 'Je ontvangt nu iedere halve minuut een extra ' . $location->elementName()
						]);
					}
				}
			}
		}
		return redirect()->to('/');
	}
	
	public function icon(Request $request, Location $location, $step = null, $team = false) {
		$img = imagecreatefrompng(public_path('img/marker.png'));
		imagesavealpha($img, true);
		
		if($team) {
			$userLocation = UserLocation::leftJoin('users', function($join) {
					$join->on('user_locations.user_id', '=', 'users.id');
				})->whereNotNull('user_locations.coalenergy')
					->where('location_id', '=', $location->id)
					->select('users.name', 'user_locations.scan', 'user_locations.fire', 'user_locations.coalenergy', 'user_locations.gasenergy', 'user_locations.sustainable')->first();
			if($userLocation && $step !== null) {
				// al gescand
				$element = imagecreatefrompng(public_path('img/' . strtolower($userLocation->name) . '.png'));
				imagecopyresampled($img, $element, 5, 0, 0, 0, 30, 30, 100, 100);
				imagedestroy($element);
				imagesavealpha($img, true);
				
				// al iets gebouwd
				if($userLocation->sustainable) {
					$p = imagecreatefrompng(public_path('img/p' . 4 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->gasenergy) {
					$p = imagecreatefrompng(public_path('img/p' . 3 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->coalenergy) {
					$p = imagecreatefrompng(public_path('img/p' . 2 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				}
			}
			
		} else if($request->session()->has('user')) {
			$userLocation = UserLocation::where('user_id', '=', $request->session()->get('user'))
					->where('location_id', '=', $location->id)->first();
			if($userLocation && $step !== null) {
				// al gescand
				$element = imagecreatefrompng(public_path('img/' . $location->element . '.png'));
				imagecopyresampled($img, $element, 5, 0, 0, 0, 30, 30, 100, 100);
				imagedestroy($element);
				imagesavealpha($img, true);
				
				// al iets gebouwd
				if($userLocation->sustainable) {
					$p = imagecreatefrompng(public_path('img/p' . 4 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->gasenergy) {
					$p = imagecreatefrompng(public_path('img/p' . 3 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->coalenergy) {
					$p = imagecreatefrompng(public_path('img/p' . 2 . '.png'));
					imagecopyresampled($img, $p, 0, 0, 0, 0, 40, 40, 40, 40);
					imagedestroy($p);
				} else if($userLocation->fire) {
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
			'users' => User::all()
		]);
	}
	
	public function unlock(Request $request, User $user) {
		// $request;
		$user->lock = null;
		$user->save();
		
		return redirect()->back();
	}
	
	public function score(Request $request, User $user, $element, $amount) {
		// $request;
		$user->{$element} = $amount;
		$user->save();
		
		return ['amount' => $user->countElement($element)];
	}
}
