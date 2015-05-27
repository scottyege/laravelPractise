<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Board;

use Session;
use Request;
use View;

class GoController extends Controller {

	public function Index()
	{
		$allGames = Board::all();

		return view('go.Base')->with(['allGames' => $allGames]);
	}

	public function Normal($n = 5)
	{
		return view('go')->with(['n' => $n]);	
	}

	public function Store()
	{
		$n = Session::get('n');
		//$board = Session::get('board');
		$record = Session::get('record');

		$model = new Board;
		$model->n = $n;
		$model->data = serialize($record);
		$model->save();

		return $model->toJson();
	}

	public function Show($id)
	{
		$board = Board::find($id);

		$n = $board->n;
		$record = unserialize($board->data);

		$allGames = Board::all();

		return view('go.Show')->with([
									'n' => $n,
									'record' => json_encode($record),
									'created_at' => $board->created_at,
									'allGames' => $allGames
								]);
	}

	public function SimAuto($n = 5, $maxSteps = 10)
	{
		Board::CreateBoard($n, $maxSteps);
		$allGames= Board::all();

		return view('go.SimAuto')->with([
									'n' => $n, 
									'allGames' => $allGames
								]);
	}

	public function HumanComputer($n = 7)
	{
		Board::CreateBoard($n, 0);
		$allGames= Board::all();

		return view('go.HCC')->with([
									'n' => $n, 
									'allGames' => $allGames
								]);
	}

	public function CheckValidState()
	{
		//ajax
		$all = Session::get('all');
		$turn = Session::get('turn');
		$step = Session::get('step');
		$board = Session::get('board');
		$record = Session::get('record');

		$userStepId = Request::get('id');
		$userTurn = Request::get('turn');

		if($turn !== $userTurn)
		{
			return 'something was wrong';
		}
		
		 $stepIdx = array_search($userStepId, $all);
		if($stepIdx === FALSE)
		{
			return 'invalid move';
		}

		$split = explode('-', $userStepId);
		$x = $split[1];
		$y = $split[2];

		$board[$x][$y] = $turn;
		$result = $this->DoILive($x, $y, $turn, $board);
		$board[$x][$y] = '';
		if($result !== TRUE)
		{
			return 'you can not do this hand';
		}
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

		array_splice($all, $stepIdx, 1);

		$returnMsg = [
			'valid' => true,
			'step' => [
				'id' => $userStepId,
				'turn' => $turn,
				'step' => $step
			],
			'kill' => $killingList
		];

		array_push($record, $returnMsg);

		$step++;

		//store
		Session::put('all', $all);
		Session::put('turn', $turn === 'black' ? 'white' : 'black');
		Session::put('step', $step);
		Session::put('board', $board);
		Session::put('record', $record);
		

		return json_encode($returnMsg);
	}

	public function HCCRequestNext()
	{
		//ajax
		$all = Session::get('all');
		$turn = Session::get('turn');
		$step = Session::get('step');
		$board = Session::get('board');
		$record = Session::get('record');

		$x = 0;
		$y = 0;
		$selectedIdx = 0;
		$selectedId = '';
		$candidate = $all;
		$result = FALSE;
		$returnMsg = [];
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
			$returnMsg = [
				'valid' => false,
				'step' => [
					'turn' => $turn,
					'step' => $step
				],
				'kill' => []
			];

			array_push($record, $returnMsg);

			//Session::put('all', $all);
			Session::put('turn', $turn === 'black' ? 'white' : 'black');
			Session::put('step', $step);
			Session::put('record', $record);
			//Session::put('board', $board);

			return json_encode($returnMsg);
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

		$returnMsg = [
			'valid' => true,
			'step' => [
				'id' => $selectedId,
				'turn' => $turn,
				'step' => $step
			],
			'kill' => $killingList
		];

		array_push($record, $returnMsg);

		$step++;

		//store
		Session::put('all', $all);
		Session::put('turn', $turn === 'black' ? 'white' : 'black');
		Session::put('step', $step);
		Session::put('board', $board);
		Session::put('record', $record);

		return json_encode($returnMsg);			
	}

	public function RequestNext()
	{
		//ajax
		$all = Session::get('all');
		$turn = Session::get('turn');
		$step = Session::get('step');
		$board = Session::get('board');
		$record = Session::get('record');
		$maxSteps = Session::get('maxSteps');

		//update
		$step++;
		if($step > $maxSteps)
		{
			return json_encode([
					'gameOver' => true
				]);			
		}

		$x = 0;
		$y = 0;
		$selectedIdx = 0;
		$selectedId = '';
		$candidate = $all;
		$result = FALSE;
		$returnMsg = [];
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
			$returnMsg = [
				'valid' => false,
				'step' => [
					'turn' => $turn,
					'step' => $step
				],
				'kill' => []
			];

			array_push($record, $returnMsg);

			//Session::put('all', $all);
			Session::put('turn', $turn === 'black' ? 'white' : 'black');
			Session::put('step', $step);
			Session::put('record', $record);
			//Session::put('board', $board);

			return json_encode($returnMsg);
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

		$returnMsg = [
			'valid' => true,
			'step' => [
				'id' => $selectedId,
				'turn' => $turn,
				'step' => $step
			],
			'kill' => $killingList
		];

		array_push($record, $returnMsg);

		//store
		Session::put('all', $all);
		Session::put('turn', $turn === 'black' ? 'white' : 'black');
		Session::put('step', $step);
		Session::put('board', $board);
		Session::put('record', $record);

		return json_encode($returnMsg);				
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
