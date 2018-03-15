<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Description of Locations
 *
 * @author Dennis
 */
class Location extends Model {
	public $timestamps = false;
	
	protected $fillable = [
        'name',
		'lat',
		'lng',
		'element',
		'available'
    ];
	
	//put your code here
	public static function getElements() {
		return [
			'wood' => 'Hout',
			'stone' => 'Steen',
			'copper' => 'Koper',
			'corn' => 'Graan',
			'hop' => 'Hop',
			'yeast' => 'Gist'
		];
	}
	
	public function elementName() {
		return self::getElements()[$this->element];
	}
	
	public function users() {
		return $this->belongsToMany(User::class, 'user_locations');
	}
	
	public function userLocations() {
		return $this->hasMany(UserLocation::class);
	}
}
