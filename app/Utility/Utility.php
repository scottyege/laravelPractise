<?php namespace App\Utility;

class Utility
{
	static public function IDSplit($selectedId)
	{
		return explode('-', $selectedId);
	}
}

?>