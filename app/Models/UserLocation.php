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
class UserLocation extends Model {
	public $timestamps = false;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
		'scan',
		'flag',
		'house',
		'bb',
		'cafe'
	];
    public static $costs = [
		'flag' => [
			'wood' => 1,
			'stone' => 0,
			'copper' => 0,
			'corn' => 0,
			'hop' => 0,
			'yeast' => 0
		],
		'house' => [
			'wood' => 6,
			'stone' => 8,
			'copper' => 2,
			'corn' => 0,
			'hop' => 0,
			'yeast' => 0
		],
		'bb' => [
			'wood' => 4,
			'stone' => 6,
			'copper' => 3,
			'corn' => 3,
			'hop' => 0,
			'yeast' => 0
		],
		'cafe' => [
			'wood' => 2,
			'stone' => 6,
			'copper' => 8,
			'corn' => 5,
			'hop' => 5,
			'yeast' => 5
		]
	];
	public function user() {
		return $this->belongsTo(User::class);
	}
	public function location() {
		return $this->belongsTo(Location::class);
	}
}
