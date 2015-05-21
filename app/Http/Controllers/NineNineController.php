<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class NineNineController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
		$table = array();

		for($i = 1; $i < 10; $i++)
		{
			$s = array();
			for($j = 1; $j < 10; $j++)
			{
				$v = $i * $j;
				$s[$j] = "$i * $j = $v";
			}
			$table[$i] = $s;
		}

		return view('NineNine')->with('table', $table);
	}

}
