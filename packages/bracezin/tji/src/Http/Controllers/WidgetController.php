<?php

namespace Tji\Http\Controllers;

use Carbon\Carbon;
use Arr;
use Str;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tji\Http\Controllers\PackageController;

class WidgetController extends PackageController {

	public function productNameArray() {
		$products = $this->productRepository->all(['all' => 1])->pluck('name')->toArray();
		return $products;
	}

	public function day(Request $request) {
		$input = $request->all();
		$input['day'] = ($input && Arr::has($input, 'day') && $input['day'] != '') ? $input['day'] : Carbon::now()->day;
		$input['month'] = ($input && Arr::has($input, 'month') && $input['month'] != '') ? $input['month'] : Carbon::now()->month;
		$input['year'] = ($input && Arr::has($input, 'year') && $input['year'] != '') ? $input['year'] : Carbon::now()->year;
		$input['products'] = $this->productNameArray();
		$month_widget = $this->invoiceItemRepository->itemWidget($input);
		return response()->json(compact('month_widget'));
	}

	public function month(Request $request) {
		$input = $request->all();
		$input['month'] = ($input && Arr::has($input, 'month') && $input['month'] != '') ? $input['month'] : Carbon::now()->month;
		$input['year'] = ($input && Arr::has($input, 'year') && $input['year'] != '') ? $input['year'] : Carbon::now()->year;
		$input['products'] = $this->productNameArray();
		$month_widget = $this->invoiceItemRepository->itemWidget($input);
		return response()->json(compact('month_widget'));
	}

	public function year(Request $request) {
		$input = $request->all();
		$input['month'] = ($input && Arr::has($input, 'month') && $input['month'] != '') ? $input['month'] : Carbon::now()->month;
		$input['year'] = ($input && Arr::has($input, 'year') && $input['year'] != '') ? $input['year'] : Carbon::now()->year;
		$input['products'] = $this->productNameArray();
		$month_widget = $this->invoiceItemRepository->itemWidget($input);
		return response()->json(compact('month_widget'));
	}

	public function testGetApi(Request $request) {
		$input = $request->all();
		$mobile = Arr::has($input, 'mobile') ? $input['mobile'] : '';
		$message = "Received response from API - Get Method : " . (string)$mobile;
		return response()->json(compact('message'));
	}

	public function testPostApi(Request $request) {
		$message = "Received response from API - Post Method";
		return response()->json(compact('message'));
	}

	public function cacheClear() {
		\Artisan::call('modelCache:clear');
		\Artisan::call('optimize:clear');
		if(Storage::disk('public')->exists('json/database.json')) {
			Storage::disk('public')->delete('json/database.json');
		}
		if(Storage::disk('public')->exists('json/permissions.json')) {
			Storage::disk('public')->delete('json/permissions.json');
		}
		return response()->json(['done'], 200);
	}

	public function clearLogFiles() {
		\Artisan::command('logs:clear', function() {

		    exec('rm ' . storage_path('logs/*.log'));

		    $this->comment('Logs have been cleared!');

		})->describe('Clear log files');

	}

}
