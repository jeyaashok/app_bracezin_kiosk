<?php

namespace Tji\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tji\Traits\BooleanTrait;
use Tji\Traits\DateFilterTrait;
use Tji\Traits\FilterTrait;
use Tji\Traits\SearchableTrait;
use Carbon\Carbon;
use Request;
use ErrorResponse;

/******* Available Index *********
 * MorphTo Relations & Filters:
 * resource()
 * person()
 * for()
 * moneyTo()
 * moneyFrom()
 ******* Index *********/

trait TjiModelTrait {

	use BooleanTrait, DateFilterTrait, FilterTrait, SearchableTrait;

	/** Resource MorphTo */
	public function resource() {
		return $this->morphTo();
	}

	/** Person MorphTo */
	public function person() {
		return $this->morphTo();
	}

	/** Model MorphTo */
	public function model() {
		return $this->morphTo();
	}

	/** Whom MorphTo */
	public function whom() {
		return $this->morphTo()->withTrashed();
	}

	/** From MorphTo */
	public function from() {
		return $this->morphTo()->withTrashed();
	}

	/** For MoneyTo */
	public function for() {
		return $this->morphTo()->withTrashed();
	}

	/** MoneyTo MorphTo */
	public function moneyTo() {
		return $this->morphTo()->withTrashed();
	}

	/** MoneyFrom MorphTo */
	public function moneyFrom() {
		return $this->morphTo()->withTrashed();
	}

	/** MoneyFrom MorphTo */
	public function verify() {
		return $this->morphTo()->withTrashed();
	}

	/** MoneyFrom MorphTo */
	public function getDistanceMilesAttribute() {
		$miles = $this->GetMiles();
		return $this->attributes['distanceMiles'] = @$miles;
	}

	/** MoneyFrom MorphTo */
	public function getDistanceKmphAttribute() {
        $miles = $this->GetMiles();
	  	$kmph = $miles * 1.609344;
		return $this->attributes['distanceKmph'] = @$kmph;
	}

	public function ScopeGetMiles() {
		$input = Request::all();
		$latitude = @$input['latitude'] ?: null;
    	$longitude = @$input['longitude'] ?: null;
        if(is_null($latitude) || is_null($longitude)) { throw new ErrorResponse('Required Current Location latitude and longitude',405,'info'); }
        $latitudeTo = $this->latitude;
        $longitudeTo = $this->longitude;
        if(is_null($latitudeTo) || is_null($longitudeTo)) { return 'Unknown'; }
        $deta = $longitudeTo - $longitude;
        $dist = (sin(deg2rad($latitude)) * sin(deg2rad($latitudeTo))) +  (cos(deg2rad($latitude)) * cos(deg2rad($latitudeTo))) * cos(deg2rad($deta));
		$dist = max(-1, min(1, $dist));
        $rad = M_PI/180;
        $miles = (acos($dist) / $rad * 60 * 1.853);
		return $miles;
	}

	public function ScopeGetKmph() {
		$miles = $this->GetMiles();
	  	$kmph = $miles * 1.609344;
		return $kmph;
	}

}
