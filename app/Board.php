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

		session([
			'n' => $n,
			'all' => $all,
			'turn' => 'black',
			'step' => 0,
			'board' => $board,
			'record' => $record,
			'maxSteps' => $maxSteps
		]);
	}

	/*
	TRUE: I can live
	or an array of id of dead
	*/
	static public function DoILive($x, $y, $turn, $board)
	{
		$n = session('n');

		$ally = $turn;

		$backupState = $board[$x][$y];
		$board[$x][$y] = $turn;

		$candidates = [];//
		$pastCandidates = [];//those have been searched
		array_push($candidates, [$x, $y]);
		while(!empty($candidates))
		{
			$target = array_pop($candidates);

			for($i = -1; $i < 2; $i+=2)
			{
				$WE = self::LiveCheck($target[0] + $i, $target[1], $ally, $board, $n, $pastCandidates, $candidates);//west to east
				$NS = self::LiveCheck($target[0], $target[1] + $i, $ally, $board, $n, $pastCandidates, $candidates);//north to south
				if($WE === TRUE || $NS === TRUE)
				{
					return TRUE;
				}
			}

			array_push($pastCandidates, "t-{$target[0]}-{$target[1]}");
		}

		$board[$x][$y] = $backupState;

		return $pastCandidates;
	}

	/*
	kill and
	return an array of killed ids
	*/
	static public function DoKill($x, $y, $turn, &$all, &$board)
	{
		$n = session('n');

		$enemy = ($turn === 'black' ? 'white' : 'black');

		$killingList = [];

		$target = [$x, $y];

		for($i = -1; $i < 2; $i+=2)
		{
			self::KillCheck($target[0] + $i, $target[1], $enemy, $board, $n, $killingList);//west to east
			self::KillCheck($target[0], $target[1] + $i, $enemy, $board, $n, $killingList);//north to south
		}

		/*
		if($target[1]-1 >= 0)
		{
			if($board[$target[0]][$target[1]-1] === $enemy)
			{
				$xx = $target[0];
				$yy = $target[1] - 1;
				$result = self::DoILive($xx, $yy, $enemy, $board);
				if($result !== TRUE)
				{
					$killingList = array_merge($killingList, $result);
				}
			}
		}

		if($target[0]+1 < $n)
		{
			if($board[$target[0]+1][$target[1]] === $enemy)
			{
				$xx = $target[0] + 1;
				$yy = $target[1];
				$result = self::DoILive($xx, $yy, $enemy, $board);
				if($result !== TRUE)
				{
					$killingList = array_merge($killingList, $result);
				}
			}
		}

		if($target[1]+1 < $n)
		{
			if($board[$target[0]][$target[1]+1] === $enemy)
			{
				$xx = $target[0];
				$yy = $target[1] + 1;
				$result = self::DoILive($xx, $yy, $enemy, $board);
				if($result !== TRUE)
				{
					$killingList = array_merge($killingList, $result);
				}
			}
		}

		if($target[0]-1 >= 0)
		{
			if($board[$target[0]-1][$target[1]] === $enemy)
			{
				$xx = $target[0] - 1;
				$yy = $target[1];
				$result = self::DoILive($xx, $yy, $enemy, $board);
				if($result !== TRUE)
				{
					$killingList = array_merge($killingList, $result);
				}
			}
		}
		*/

		$killingList = array_values(array_unique($killingList));
		foreach ($killingList as $id) {
			$split = explode('-', $id);
			$x = $split[1];
			$y = $split[2];

			$board[$x][$y] = '';

			array_push($all, $id);
		}

		return $killingList;
	}

	static public function LiveCheck($x, $y, $ally, $board, $n, &$pastCandidates, &$candidates)
	{
		if(($x >= 0) && ($x < $n) && ($y >= 0) && ($y < $n))
		{
			if($board[$x][$y] === '')
			{
				return TRUE;
			}
			else if($board[$x][$y] === $ally)
			{
				if(array_search("t-$x-$y", $pastCandidates) === FALSE)
					array_push($candidates, [$x, $y]);
			}
		}

		return FALSE;
	}

	static public function KillCheck($x, $y, $enemy, $board, $n, &$killingList)
	{
		if(($x >= 0) && ($x < $n) && ($y >= 0) && ($y < $n))
		{
			if($board[$x][$y] === $enemy)
			{
				$result = self::DoILive($x, $y, $enemy, $board);
				if($result !== TRUE)
				{
					$killingList = array_merge($killingList, $result);
				}
			}
		}
	}
}