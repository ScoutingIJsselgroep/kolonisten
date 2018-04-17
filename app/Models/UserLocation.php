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
		'buy'
	];
    public static $costs = [
		'wortel' => 1,
		'helm' => 2,
		'schild' => 3,
		'zwaard' => 4,
		'harnas' => 5,
		'paard' => 6
	];
	public function user() {
		return $this->belongsTo(User::class);
	}
	public function location() {
		return $this->belongsTo(Location::class);
	}
}
