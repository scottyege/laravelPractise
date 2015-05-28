<?php namespace App\Utility;

use App\Board;

use Session;

class GoAI
{

	static public function GenerateNextStep()
	{
		$all = Session::get('all');
		$turn = Session::get('turn');
		$step = Session::get('step');
		$board = Session::get('board');
		$record = Session::get('record');
		$maxSteps = Session::get('maxSteps');

		$step++;
		//update
		if($maxSteps > 0)
		{
			if($step > $maxSteps)
			{
				return json_encode([
					'gameOver' => true
				]);			
			}
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
			$isEye = self::EyeCheck($x, $y, $turn, $board);

		}while(($result !== TRUE || $isEye === TRUE) && count($candidate) > 0);

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

		// $returnMsg = [
		// 	'valid' => true,
		// 	'step' => [
		// 		'id' => '100',
		// 		'turn' => 'whadsf',
		// 		'step' => 100
		// 	],
		// 	'kill' => 'gg'
		// ];

		return json_encode($returnMsg);
	}

	static private function EyeCheck($x, $y, $turn, $board)
	{
		$result = TRUE;
		for($i = -1; $i < 2; $i+=2)
		{
			$result = (($result && self::IsAlly($x + $i, $y, $turn, $board))
			 && self::IsAlly($x, $y + $i, $turn, $board));
		}

		return $result;
	}

	static private function IsAlly($x, $y, $ally, $board)
	{
		$n = session('n');
		if(($x < 0) || ($x >= $n) || ($y < 0) || ($y >= $n))
		{
			return TRUE; //assume the border is ally...
		}

		if($board[$x][$y] === $ally)
		{
			return TRUE;
		}

		return FALSE;
	}
}


?>