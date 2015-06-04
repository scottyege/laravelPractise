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
			'maxSteps' => $maxSteps,
			'passCount' => 0
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
	static public function DoIKill($x, $y, $turn, &$board)
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

	static public function AtBorder($x, $y, $board)
	{
		$n = session('n');
		$nMinus1 = $n - 1;

		return ($x == 0 || $x == $nMinus1 || $y == 0 || $y == $nMinus1);
	}

	static public function KillThemAll($killingList, &$all, &$board)
	{
		$killingList = array_values(array_unique($killingList));
		foreach ($killingList as $id) {
			$split = explode('-', $id);
			$xx = $split[1];
			$yy = $split[2];

			$board[$xx][$yy] = '';

			array_push($all, $id);
		}
	}

	static public function TerritoryCounting($all, $board)
	{
		$candidates = $all;

		$allGroups = self::GroupingEmpty($candidates, $board);
		//$ss = [array_pop($candidates)];

		return $allGroups;
		//return $ss;
	}

	static private function GroupingEmpty(&$candidates, $board)
	{

		$allGroups = [];

		while(!empty($candidates))
		{
			$firstID = array_pop($candidates);
			$stack = [];
			$group = [];

			array_push($stack, $firstID);
			array_push($group, $firstID);

			while(!empty($stack))
			{
				$id = array_pop($stack);

				$split = explode('-', $id);
				$xx = $split[1];
				$yy = $split[2];

				for($i = -1; $i < 2; $i+=2)
				{
					self::AddGroupEmpty($xx + $i, $yy, $board, $candidates, $group, $stack);
					self::AddGroupEmpty($xx, $yy + $i, $board, $candidates, $group, $stack);
				}

			}

			$sizeOfGroup = count($group);
			if($sizeOfGroup > 0)
			{
				$color = self::IdentifyEmptyGroup($group, $board);
				//it is a valid group
				array_push($allGroups, [
						'size' => $sizeOfGroup,
						'group' => $group,
						'color' => $color
				]);
			}

		}

		return $allGroups;
	}

	static private function AddGroupEmpty($x, $y, &$board, &$candidates, &$group, &$stack)
	{
		$n = session('n');

		if($x < 0 || $x >= $n || $y < 0 || $y >= $n)
		{
			return false;
		}

		// if(($x >= 0 && $x < $n) && ($y >= 0 && $y < $n))
		// {
		if($board[$x][$y] === '')
		{
			$idr = "t-$x-$y";
			//
			//if(array_search($idr, $group) === false)
			if(in_array($idr, $group) === false)
			{
				array_push($group, $idr);
				array_push($stack, $idr);
			}

			// if(in_array($idr, $stack) === false)
			// {
			// 	array_push($stack, $idr);
			// }

			$idxInCan = array_search($idr, $candidates);
			if($idxInCan !== false)
			{
				array_splice($candidates, $idxInCan, 1);
			}
			// if(in_array($idr, $candidates) === true)
			// {
			// 	array_splice($candidates, array_search($idr, $candidates), 1);
			// }

			return true;
		}
		// }

		return false;
	}

	static public function IdentifyEmptyGroup($group, $board)
	{
		$sizeOfGroup = count($group);

		$color = false;
		$result = true;

		foreach ($group as $idr)
		{
			$split = explode('-', $idr);
			$x = $split[1];
			$y = $split[2];

			for($i = -1; $i < 2; $i+=2)
			{
				$result = $result
					&& self::CheckSurround($x + $i, $y, $board, $color)
					&& self::CheckSurround($x, $y + $i, $board, $color);
			}
		}

		if($color !== false && $result === true)
			return $color;
		return false;
	}

	static public function CheckSurround($x, $y, &$board, &$color)
	{
		$n = session('n');

		if($x < 0 || $x >= $n || $y < 0 || $y >= $n)
		{
			return true;
		}

		if($color === false)
		{
			if($board[$x][$y] !== '')
			{
				$color = $board[$x][$y];
			}
			return true;
		}

		return ($board[$x][$y] === $color);
	}

	static public function Counting($x, $y, $board, &$count, &$firstColor, &$candidates)
	{
		// $n = session('n');
		//
		// if($x < 0 || $x >= $n || $y < 0 || $y >= $n)
		// {
		// 	return false;
		// }
		//
		// if($board[$x][$y] === '')
		// {
		// 	$count++;
		// 	if()
		// 	array_push($group, "t-{$x}-{$y}");
		// }
		// else
		// {
		//
		// }
		//
		// if($firstColor === false)
		// {
		//
		// }
	}
}
