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

			if($target[1]-1 >= 0)
			{
				if($board[$target[0]][$target[1]-1] === '')
				{
					return TRUE;
				}
				else if($board[$target[0]][$target[1]-1] === $ally)
				{
					$xx = $target[0];
					$yy = $target[1] - 1;
					if(array_search("t-$xx-$yy", $pastCandidates) === FALSE)
						array_push($candidates, [$xx, $yy]);
				}
			}

			if($target[0]+1 < $n)
			{
				if($board[$target[0]+1][$target[1]] === '')
				{
					return TRUE;
				}
				else if($board[$target[0]+1][$target[1]] === $ally)
				{
					$xx = $target[0] + 1;
					$yy = $target[1];
					if(array_search("t-$xx-$yy", $pastCandidates) === FALSE)
						array_push($candidates, [$xx, $yy]);
				}
			}

			if($target[1]+1 < $n)
			{
				if($board[$target[0]][$target[1]+1] === '')
				{
					return TRUE;
				}
				else if($board[$target[0]][$target[1]+1] === $ally)
				{
					$xx = $target[0];
					$yy = $target[1] + 1;
					if(array_search("t-$xx-$yy", $pastCandidates) === FALSE)
						array_push($candidates, [$xx, $yy]);
				}
			}

			if($target[0]-1 >= 0)
			{
				if($board[$target[0]-1][$target[1]] === '')
				{
					return TRUE;
				}
				else if($board[$target[0]-1][$target[1]] === $ally)
				{
					$xx = $target[0] - 1;
					$yy = $target[1];
					if(array_search("t-$xx-$yy", $pastCandidates) === FALSE)
						array_push($candidates, [$xx, $yy]);
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

		$ally = $turn;
		$enemy = ($turn === 'black' ? 'white' : 'black');

		$killingList = [];

		$target = [$x, $y];

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
}
