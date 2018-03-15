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
	
	
	public static function getWinner() {
		$found = [];
		foreach(UserLocation::whereNotNull('cafe')->orderBy('cafe')->pluck('user_id', 'id') as $user_id) {
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
	public function countFlags() {
		return $this->userLocations()->whereNotNull('flag')->count();
	}
	public function countHouses() {
		return $this->userLocations()->whereNotNull('house')->count();
	}
	public function countBbs() {
		return $this->userLocations()->whereNotNull('bb')->count();
	}
	public function countCafes() {
		return $this->userLocations()->whereNotNull('cafe')->count();
	}
	public function countElements() {
		$elements = [];
		$build = $this->userLocations()->select(\DB::raw('COUNT(flag) as flags'), \DB::raw('COUNT(house) as houses'), \DB::raw('COUNT(bb) as bbs'), \DB::raw('COUNT(cafe) as cafes'))->first()->toArray();
		
		foreach(Location::getElements() as $element => $name) {
			$elements[$element] = $this->{$element};
			
			foreach($this->userLocations()->join('locations', function($join) use ($element) {
				$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
			})->select('scan', 'flag', 'house', 'bb', 'cafe')->get() as $location) {
				if($location->scan) {
					$elements[$element] += 2;
				}
				if($location->flag) {
					$elements[$element] += floor($location->flag->diffInMinutes(Carbon::now()) / 3);
				}
				if($location->house) {
					$elements[$element] += floor($location->house->diffInMinutes(Carbon::now()) / 2);
				}
				if($location->bb) {
					$elements[$element] += $location->house->diffInMinutes(Carbon::now());
				}
				if($location->cafe) {
					$elements[$element] += floor($location->house->diffInSeconds(Carbon::now()) / 30);
				}
			}
			
			$elements[$element] -= UserLocation::$costs['flag'][$element] * $build['flags'];
			$elements[$element] -= UserLocation::$costs['house'][$element] * $build['houses'];
			$elements[$element] -= UserLocation::$costs['bb'][$element] * $build['bbs'];
			$elements[$element] -= UserLocation::$costs['cafe'][$element] * $build['cafes'];
		}
		
		return $elements;
	}
	public function countElement($element) {
		$count = $this->{$element};
		foreach($this->userLocations()->join('locations', function($join) use ($element) {
			$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
		})->select('scan', 'flag', 'house', 'bb', 'cafe')->get() as $location) {
			if($location->scan) {
				$count += 2;
			}
			if($location->flag) {
				$count += floor($location->flag->diffInMinutes(Carbon::now()) / 3);
			}
			if($location->house) {
				$count += floor($location->house->diffInMinutes(Carbon::now()) / 2);
			}
			if($location->bb) {
				$count += $location->house->diffInMinutes(Carbon::now());
			}
			if($location->cafe) {
				$count += floor($location->house->diffInSeconds(Carbon::now()) / 30);
			}
		}
		
		$build = $this->userLocations()->select(\DB::raw('COUNT(flag) as flags'), \DB::raw('COUNT(house) as houses'), \DB::raw('COUNT(bb) as bbs'), \DB::raw('COUNT(cafe) as cafes'))->first()->toArray();
		
		// todo, er af waar van gebouwd is
		$count -= UserLocation::$costs['flag'][$element] * $build['flags'];
		$count -= UserLocation::$costs['house'][$element] * $build['houses'];
		$count -= UserLocation::$costs['bb'][$element] * $build['bbs'];
		$count -= UserLocation::$costs['cafe'][$element] * $build['cafes'];
				
		return $count;
	}
	
}
