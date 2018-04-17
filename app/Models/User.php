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
	
    protected $dates = [
        'draak'
    ];
	
	
	public static function getWinner() {
		/*$found = [];
		foreach(UserLocation::whereNotNull('cafe')->orderBy('cafe')->pluck('user_id', 'id') as $user_id) {
			if(isset($found[$user_id])) {
				return self::find($user_id);
			}
			$found[$user_id] = true;
		}*/
		return false;
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
			')->take(2)->pluck('id')->toArray());
		}
		
		return array_unique($next_locations);
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
	
	public function countHenx() {
		$henx = $this->henx;
		foreach($this->userLocations()->join('locations', function($join) {
			$join->on('user_locations.location_id', '=', 'locations.id');
		})->select('user_locations.scan', 'user_locations.buy', 'locations.element')->get() as $location) {
			if($location->scan) {
				$henx++;
			}
			if($location->buy && $location->element) {
				$henx -= UserLocation::$costs[$location->element];
			}
		}
		return $henx;
	}
	
	public function hasElements() {
		$elements = [];
		foreach(Location::getElements() as $element => $name) {
			$elements[$element] = $this->hasElement($element);
		}
		return $elements;
	}
	public function hasElement($element) {
		return (bool)$this->userLocations()->join('locations', function($join) use ($element) {
			$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
		})->whereNotNull('buy')->count();
	}
	
}
