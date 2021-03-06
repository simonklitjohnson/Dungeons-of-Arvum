<?php

class healthpotTile
{
	public $solid;
	public $representation;
	public $color;
	public $amount;
	public $room;
	public function __construct($amount = 1, $thisroom = null)
	{
		$this->solid = false;
		$this->representation = "&";
		$this->color = "#5CCC6B";
		$this->amount = $amount;
		$this->room = $thisroom;
	}

	public function pickup($player)
	{
		global $vacant_rooms, $rooms;
		

		$room = $vacant_rooms[array_rand($vacant_rooms, 1)];
		$xcoord = rand($room["_x1"], $room["_x2"]);
		$ycoord = rand($room["_y1"], $room["_y2"]);
		setTile($xcoord, $ycoord, new Tile(new healthpotTile($this->amount, $room['id'])));
		array_push($vacant_rooms, $rooms[$this->room]);
		$player->addHealthpot($this->amount, $healthpot);
		//unset($this);
	}
}