<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Session;

class Board extends Model {

	//




	//static public function
	static public function CreateBoard($n, $maxSteps) 
	{
		$all = [];
		$board = [];
		$record = [];

		SESSION::put('n', $n);

		for($i = 0; $i < $n; $i++)
		{
			$row = array();
			for($j = 0; $j < $n; $j++)
			{
				array_push($all, "t-$i-$j");
				array_push($row, '');
			}
			array_push($board, $row);
		}

		Session::put('all', $all);
		Session::put('turn', 'black');
		Session::put('step', 0);
		Session::put('board', $board);
		Session::put('record', $record);
		Session::put('maxSteps', $maxSteps);
	}
}
