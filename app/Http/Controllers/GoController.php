<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Session;

class GoController extends Controller {

	public function Test($a, $b)
	{
		$ar = [];
		array_push($ar, "t$a$b");

		$t = [3, 51];
		if(array_search("t{$t[0]}{$t[1]}", $ar) === FALSE)
		{
			return 'FALSE';
		}
		return 'TRUE';
	}

	public function index($n = 5)
	{
		return view('go')->with('n', $n);
	}

	public function Sim($n = 5, $maxSteps = 10)
	{
		$steps = array();

		$all = array();
		for($i = 0; $i < $n; $i++)
		{
			for($j = 0; $j < $n; $j++)
			{
				array_push($all, "t$i$j");
			}
		}

		shuffle($all);

		$turn = 0;
		for($i = 0; $i < $maxSteps; $i++)
		{
			array_push($steps, [
									'id' => $all[$i],
									'turn' => (!$turn ? 'black': 'white')
							]);
			$turn = !$turn;
		}

		//dd($steps);
		 return view('Sim')->with('steps', json_encode($steps))
		 					->with('n', $n);
		//return json_encode($steps);
	}

	public function SimByStep($n = 5, $maxSteps = 10)
	{
		$all = array();
		$board = array();

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

		return view('SimByStep')->with('n', $n);
	}

	public function SimAuto($n = 5, $maxSteps = 10)
	{
		$all = array();
		$board = array();

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

		return view('SimAuto')->with('n', $n);
	}

	public function RequestNext()
	{
		//ajax
		$all = Session::get('all');
		$turn = Session::get('turn');
		$step = Session::get('step');
		$board = Session::get('board');

		//update
		$step++;

		$x = 0;
		$y = 0;
		$selectedIdx = 0;
		$selectedId = '';
		$candidate = $all;
		$result = FALSE;
		do
		{
			$selectedIdx = rand(0, count($candidate) - 1);
			$selectedId = $candidate[ $selectedIdx ];

			$split = explode('-', $selectedId);
			$x = $split[1];
			$y = $split[2];

			array_splice($candidate, $selectedIdx, 1);

			$board[$x][$y] = $turn;
			$result = $this->DoILive($x, $y, $turn, $board);
			$board[$x][$y] = '';

		}while($result !== TRUE && count($candidate) > 0);
		//}while(FALSE);

		if(count($candidate) == 0)
		{
			//no next good step
			//Session::put('all', $all);
			Session::put('turn', $turn === 'black' ? 'white' : 'black');
			Session::put('step', $step);
			//Session::put('board', $board);

			return json_encode([
				'valid' => false,
				'step' => [
					'turn' => $turn,
					'step' => $step
				],
				'kill' => []
			]);
		}

		array_splice($all, array_search($selectedId, $all), 1);
		$board[$x][$y] = $turn;

		$killingList = $this->DoKill($x, $y, $turn, $board);

		//update the board
		foreach ($killingList as $id) {
			$split = explode('-', $id);
			$x = $split[1];
			$y = $split[2];

			$board[$x][$y] = '';

			array_push($all, $id);
		}

		//store
		Session::put('all', $all);
		Session::put('turn', $turn === 'black' ? 'white' : 'black');
		Session::put('step', $step);
		Session::put('board', $board);

		return json_encode([
			'valid' => true,
			'step' => [
				'id' => $selectedId,
				'turn' => $turn,
				'step' => $step
			],
			'kill' => $killingList
		]);

		// return json_encode([
		// 			'id' => $selectedId,
		// 			'turn' => $turn,
		// 			'step' => $step
		// 		]);						
	}

	//-----------------------------------------
	private function DoILive($x, $y, $turn, $board)
	{
		$n = SESSION::get('n');
		$ally = $turn;
		$enemy = ($turn === 'black' ? 'white' : 'black');

		$candidates = [];//
		$pastCandidates = [];//have been searched
		array_push($candidates, [$x, $y]);
		while(!empty($candidates))
		{
			//$target = $candidates[count($candidates) - 1];
			$target = array_pop($candidates);

			if($target[1]-1 >= 0)
			{
				if($board[$target[0]][$target[1]-1] === '')
				{
					return true;
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
					return true;
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
					return true;
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
					return true;
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

		//return true;
		//return false;
		return $pastCandidates;
	}

	private function DoKill($x, $y, $turn, $board)
	{
		$n = SESSION::get('n');
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
				$result = $this->DoILive($xx, $yy, $enemy, $board);
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
				$result = $this->DoILive($xx, $yy, $enemy, $board);
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
				$result = $this->DoILive($xx, $yy, $enemy, $board);
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
				$result = $this->DoILive($xx, $yy, $enemy, $board);
				if($result !== TRUE)
				{
					$killingList = array_merge($killingList, $result);
				}
			}
		}

		return array_values(array_unique($killingList));
	}
}