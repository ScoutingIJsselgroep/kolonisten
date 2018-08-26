<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class User extends Model
{
	public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
	
    public static $targets = [
		'fire' => 'Vuur',
		'coalplant' => 'Kolencentrale',
		'gasplant' => 'Gascentrale',
		'sustainable' => 'Duurzaam'
	];
	
	public static function getWinner() {
		$found = [];
		foreach(UserLocation::whereNotNull('sustainable')->orderBy('sustainable')->pluck('user_id', 'id') as $user_id) {
			if(isset($found[$user_id])) {
				return self::find($user_id);
			}
			$found[$user_id] = true;
		}
		return false;
	}
	public function locations() {
		return $this->belongsToMany(Location::class, 'user_locations');
	}
	
	public function userLocations() {
		return $this->hasMany(UserLocation::class);
	}
	
	public function countScans() {
		return $this->userLocations()->whereNotNull('scan')->count();
	}
	public function countFires() {
		return $this->userLocations()->whereNotNull('fire')->count();
	}
	public function countCoalplants() {
		return $this->userLocations()->whereNotNull('coalplant')->count();
	}
	public function countGasplants() {
		return $this->userLocations()->whereNotNull('gasplant')->count();
	}
	public function countSustainables() {
		return $this->userLocations()->whereNotNull('sustainable')->count();
	}
	public function countElements() {
		$elements = [];
		$build = $this->userLocations()->select(\DB::raw('COUNT(fire) as fires'), \DB::raw('COUNT(coalplant) as coalplants'), \DB::raw('COUNT(gasplant) as gasplants'), \DB::raw('COUNT(sustainable) as sustainables'))->first()->toArray();
		
		foreach(Location::getElements() as $element => $name) {
			$elements[$element] = $this->{$element};
			
			foreach($this->userLocations()->join('locations', function($join) use ($element) {
				$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
			})->select('scan', 'fire', 'coalplant', 'gasplant', 'sustainable')->get() as $location) {
				if($location->scan) {
					$elements[$element] += 2;
				}
				if($location->fire) {
					$elements[$element] += round(tanh($location->fire->diffInMinutes(Carbon::now()) * pi() / 20) * 3);
				}
				if($location->coalplant) {
					$elements[$element] += round(tanh($location->coalplant->diffInMinutes(Carbon::now()) * pi() / 15) * 6);
				}
				if($location->gasplant) {
					$elements[$element] += round(tanh($location->gasplant->diffInMinutes(Carbon::now()) * pi() / 10) * 8);
				}
				if($location->sustainable) {
					$elements[$element] += round(tanh($location->sustainable->diffInMinutes(Carbon::now()) * pi() / 5) * 10);
				}
			}
			
			$elements[$element] -= UserLocation::$costs['fire'][$element] * $build['fires'];
			$elements[$element] -= UserLocation::$costs['coalplant'][$element] * $build['coalplants'];
			$elements[$element] -= UserLocation::$costs['gasplant'][$element] * $build['gasplants'];
			$elements[$element] -= UserLocation::$costs['sustainable'][$element] * $build['sustainables'];
		}
		
		return $elements;
	}
	public function countElement($element) {
		
		$count = $this->{$element};
		foreach($this->userLocations()->join('locations', function($join) use ($element) {
			$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
		})->select('scan', 'fire', 'coalplant', 'gasplant', 'sustainable')->get() as $location) {
			if($location->scan) {
				$count += 2;
			}
			if($location->fire) {
				$count += round(tanh($location->fire->diffInMinutes(Carbon::now()) * pi() / 20) * 3);
			}
			if($location->coalplant) {
				$count += round(tanh($location->coalplant->diffInMinutes(Carbon::now()) * pi() / 15) * 6);
			}
			if($location->gasplant) {
				$count += round(tanh($location->gasplant->diffInMinutes(Carbon::now()) * pi() / 10) * 8);
			}
			if($location->sustainable) {
				$count += round(tanh($location->sustainable->diffInMinutes(Carbon::now()) * pi() / 5) * 10);
			}
		}
		
		$build = $this->userLocations()->select(\DB::raw('COUNT(fire) as fires'), \DB::raw('COUNT(coalplant) as coalplants'), \DB::raw('COUNT(gasplant) as gasplants'), \DB::raw('COUNT(sustainable) as sustainables'))->first()->toArray();
		
		// todo, er af waar van gebouwd is
		$count -= UserLocation::$costs['fire'][$element] * $build['fires'];
		$count -= UserLocation::$costs['coalplant'][$element] * $build['coalplants'];
		$count -= UserLocation::$costs['gasplant'][$element] * $build['gasplants'];
		$count -= UserLocation::$costs['sustainable'][$element] * $build['sustainables'];
				
		return $count;
	}
	
	public function availableLocations() {
		$next_locations = $locations = array_merge([$this->start_location_id], $this->userLocations()->orderBy('scan')->pluck('location_id')->toArray());
		foreach($locations as $location_id) {
			$location = Location::find($location_id);
			
			$next_locations = array_merge($next_locations, Location::whereNotIn('locations.id', $next_locations)->orderByRaw('
				ASIN(SQRT(
				POWER(SIN((locations.lat - abs(' . $location->lat . ')) * pi()/180 / 2),
				2) + COS(locations.lat * pi()/180 ) * COS(abs(' . $location->lat . ') *
				pi()/180) * POWER(SIN((locations.lng - ' . $location->lng . ') *
				pi()/180 / 2), 2) ))
			')->take(1)->pluck('id')->toArray());
		}
		
		return array_unique($next_locations);
	}
}
