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

		$result = Board::DoILive($x, $y, $turn, $board);

		if($result !== TRUE)
		{
			return 'you can not do this hand';
		}

		$board[$x][$y] = $turn;

		$killingList = Board::DoKill($x, $y, $turn, $all, $board);

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
		session([
			'all' => $all,
			'turn' => ($turn === 'black' ? 'white' : 'black'),
			'step' => $step,
			'board' => $board,
			'record' => $record
		]);
		

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

			$result = Board::DoILive($x, $y, $turn, $board);

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

			session([
				'turn' => ($turn === 'black' ? 'white' : 'black'),
				'step' => $step,
				'record' => $record
			]);

			return json_encode($returnMsg);
		}

		array_splice($all, array_search($selectedId, $all), 1);
		$board[$x][$y] = $turn;

		$killingList = Board::DoKill($x, $y, $turn, $all, $board);

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
		session([
			'all' => $all,
			'turn' => ($turn === 'black' ? 'white' : 'black'),
			'step' => $step,
			'board' => $board,
			'record' => $record
		]);

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

			$result = Board::DoILive($x, $y, $turn, $board);

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

			session([
				'turn' => ($turn === 'black' ? 'white' : 'black'),
				'step' => $step,
				'record' => $record
			]);

			return json_encode($returnMsg);
		}

		array_splice($all, array_search($selectedId, $all), 1);
		$board[$x][$y] = $turn;

		$killingList = Board::DoKill($x, $y, $turn, $all, $board);

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
		session([
			'all' => $all,
			'turn' => ($turn === 'black' ? 'white' : 'black'),
			'step' => $step,
			'board' => $board,
			'record' => $record
		]);

		return json_encode($returnMsg);				
	}
}
