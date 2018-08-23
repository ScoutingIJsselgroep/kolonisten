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
		'coalenergy' => 'Kolencentrale',
		'gasenergy' => 'Gascentrale',
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
	public function countCoalenergys() {
		return $this->userLocations()->whereNotNull('coalenergy')->count();
	}
	public function countGasenergys() {
		return $this->userLocations()->whereNotNull('gasenergy')->count();
	}
	public function countSustainables() {
		return $this->userLocations()->whereNotNull('sustainable')->count();
	}
	public function countElements() {
		$elements = [];
		$build = $this->userLocations()->select(\DB::raw('COUNT(fire) as fires'), \DB::raw('COUNT(coalenergy) as coalenergys'), \DB::raw('COUNT(gasenergy) as gasenergys'), \DB::raw('COUNT(sustainable) as sustainables'))->first()->toArray();
		
		foreach(Location::getElements() as $element => $name) {
			$elements[$element] = $this->{$element};
			
			foreach($this->userLocations()->join('locations', function($join) use ($element) {
				$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
			})->select('scan', 'fire', 'coalenergy', 'gasenergy', 'sustainable')->get() as $location) {
				if($location->scan) {
					$elements[$element] += 2;
				}
				if($location->fire) {
					$elements[$element] += round(tanh($location->fire->diffInMinutes(Carbon::now()) * pi() / 20) * 3);
				}
				if($location->coalenergy) {
					$elements[$element] += round(tanh($location->coalenergy->diffInMinutes(Carbon::now()) * pi() / 15) * 6);
				}
				if($location->gasenergy) {
					$elements[$element] += round(tanh($location->gasenergy->diffInMinutes(Carbon::now()) * pi() / 10) * 8);
				}
				if($location->sustainable) {
					$elements[$element] += round(tanh($location->sustainable->diffInMinutes(Carbon::now()) * pi() / 5) * 10);
				}
			}
			
			$elements[$element] -= UserLocation::$costs['fire'][$element] * $build['fires'];
			$elements[$element] -= UserLocation::$costs['coalenergy'][$element] * $build['coalenergys'];
			$elements[$element] -= UserLocation::$costs['gasenergy'][$element] * $build['gasenergys'];
			$elements[$element] -= UserLocation::$costs['sustainable'][$element] * $build['sustainables'];
		}
		
		return $elements;
	}
	public function countElement($element) {
		
		$count = $this->{$element};
		foreach($this->userLocations()->join('locations', function($join) use ($element) {
			$join->on('user_locations.location_id', '=', 'locations.id')->where('element', '=', $element);
		})->select('scan', 'fire', 'coalenergy', 'gasenergy', 'sustainable')->get() as $location) {
			if($location->scan) {
				$count += 2;
			}
			if($location->fire) {
				$count += round(tanh($location->fire->diffInMinutes(Carbon::now()) * pi() / 20) * 3);
			}
			if($location->coalenergy) {
				$count += round(tanh($location->coalenergy->diffInMinutes(Carbon::now()) * pi() / 15) * 6);
			}
			if($location->gasenergy) {
				$count += round(tanh($location->gasenergy->diffInMinutes(Carbon::now()) * pi() / 10) * 8);
			}
			if($location->sustainable) {
				$count += round(tanh($location->sustainable->diffInMinutes(Carbon::now()) * pi() / 5) * 10);
			}
		}
		
		$build = $this->userLocations()->select(\DB::raw('COUNT(fire) as fires'), \DB::raw('COUNT(coalenergy) as coalenergys'), \DB::raw('COUNT(gasenergy) as gasenergys'), \DB::raw('COUNT(sustainable) as sustainables'))->first()->toArray();
		
		// todo, er af waar van gebouwd is
		$count -= UserLocation::$costs['fire'][$element] * $build['fires'];
		$count -= UserLocation::$costs['coalenergy'][$element] * $build['coalenergys'];
		$count -= UserLocation::$costs['gasenergy'][$element] * $build['gasenergys'];
		$count -= UserLocation::$costs['sustainable'][$element] * $build['sustainables'];
				
		return $count;
	}
	
}
