<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Board;
use App\Utility\GoAI;

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

		$step++;

		if($turn !== $userTurn)
		{
			return json_encode([
					'valid' => false,
					'msg' => 'turn is wrong'
				]);
		}
		
		$stepIdx = array_search($userStepId, $all);
		if($stepIdx === FALSE)
		{
			return json_encode([
					'valid' => false,
					'msg' => 'this pos is wrong'
				]);
		}

		$split = explode('-', $userStepId);
		$x = $split[1];
		$y = $split[2];


		$result = Board::DoILive($x, $y, $turn, $board);
		$board[$x][$y] = $turn;//assume that this hand is legal
		$killingList = Board::DoKill($x, $y, $turn, $all, $board);

		if($result !== TRUE)
		{//if it is a dead hand
			if(empty($killingList) === TRUE)
			{//and this hand can't kill anyone, so it is indeed useless, reject!!!!
				return json_encode([
					'valid' => false,
					'msg' => 'this makes you suicide'
				]);
			}
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
		return GoAI::GenerateNextStep();
	}
}
