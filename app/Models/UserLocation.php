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
		'fire',
		'coalenergy',
		'gasenergy',
		'sustainable'
	];
    public static $costs = [
		'fire' => [
			'wood' => 1,
			'coal' => 0,
			'fe' => 0,
			'al' => 0,
			'ch4' => 0,
			'si' => 0
		],
		'coalenergy' => [
			'wood' => 6,
			'coal' => 8,
			'fe' => 2,
			'al' => 0,
			'ch4' => 0,
			'si' => 0
		],
		'gasenergy' => [
			'wood' => 4,
			'coal' => 6,
			'fe' => 3,
			'al' => 3,
			'ch4' => 0,
			'si' => 0
		],
		'sustainable' => [
			'wood' => 2,
			'coal' => 6,
			'fe' => 8,
			'al' => 5,
			'ch4' => 5,
			'si' => 5
		]
	];
	public function user() {
		return $this->belongsTo(User::class);
	}
	public function location() {
		return $this->belongsTo(Location::class);
	}
}
