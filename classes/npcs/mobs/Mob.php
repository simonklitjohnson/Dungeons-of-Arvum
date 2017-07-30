<?php

class Mob 
{
	public $playercolors = [];

	public function acquireTarget($x, $y, $viewrange, $map)
	{
		$ystart = $y + $viewrange;
		$yend = $y - $viewrange;
		$xstart = $x - $viewrange;
		$xend = $x + $viewrange;
		if($ystart < 0)
		{
			$ystart = 0;
		}
		if($xstart < 0)
		{
			$xstart = 0;
		}

		for ($i=$yend; $i <= $ystart; $i++) {
			for($ix = $xstart; $ix <= $xend; $ix++)
			{
				if($map[$ix][$i] != null)
				{
					if($map[$ix][$i]->type() == "player")
					{
						return $map[$ix][$i];
					}
				}
			}
		}
		return null;
	}

	public function damage($amount, $type, $player)
	{
		$this->curhp = $this->curhp - $amount;
		status($player->clientid, "You dealt " . $amount . " " . $type . " damage to " . $this->name . ".", "#5CCC6B");
		if($this->curhp <= 0)
		{
			$this->die($player);
		}
	}

	public function die($player)
	{
		status($player->clientid, "You killed " . $this->name . ".", "#5CCC6B");
		$player->killed($this);
		if(isset($this->loot) && $this->loot != null)
		{
			if(is_array($this->loot))
			{
				$i = 0;
				foreach($this->loot as $loot)
				{ /* Multi loot is not finished */
					setTile($this->x+($i), $this->y, new Tile(new Itemtile($loot)));
					$i++;
				}
			} else {
				setTile($this->x, $this->y, new Tile(new Itemtile($this->loot)));
			}
		} else {
			setTile($this->x, $this->y, new Tile(new Floor()));
		}
		unset($this);
	}

	public function move()
	{
		global $map;
		$allowed_to_move = false;
		$seconds_per_square = 1000/$this->movementspeed;
		$curtime = round(microtime(true) * 1000);
		//echo "Seconds per square is: " . $seconds_per_square . "\n";
		//echo "Last move was: " . $this->lastmove . "\n";
		//echo "Current move is: " . $curtime . "\n";
		//echo "Difference is: " . ($curtime - $this->lastmove);
		if($this->lastmove == 0 or ($curtime - $this->lastmove) >= $seconds_per_square)
		{
			//echo "\n\nMOVE IT!\n\n";
			$allowed_to_move = true;
			$this->lastmove = round(microtime(true) * 1000);
		} else {
			$allowed_to_move = false;
			//echo "\n\nDON'T MOVE IT!\n\n";
		}


		$seconds_per_attack = 1000/$this->attackspeed;
		$curtime = round(microtime(true) * 1000);
		//echo "Seconds per square is: " . $seconds_per_square . "\n";
		//echo "Last move was: " . $this->lastmove . "\n";
		//echo "Current move is: " . $curtime . "\n";
		//echo "Difference is: " . ($curtime - $this->lastmove);
		if(($this->target->x >= ($this->x - ($this->range)) and $this->target->x <= ($this->x + ($this->range))) and $this->target->y >= ($this->y - ($this->range)) and $this->target->y <= ($this->y + ($this->range)))
		{
			if($this->lastattack == 0 or ($curtime - $this->lastattack) >= $seconds_per_attack)
			{
				$this->doAttack();
				$this->lastattack = round(microtime(true) * 1000);
			}
		} elseif($allowed_to_move) {
		
			if($this->target == null)
			{
				$found_direction = false;
				$oldx = $this->x;
				$oldy = $this->y;
				$newx = $this->x;
				$newy = $this->y;
				while(!$found_direction)
				{
					$direction = rand(1,4);
					switch ($direction) {
						case 1:
							if($map[($this->x + 1)][$this->y]->representation() == ".")
							{
								$found_direction = true;
								$newx++;
								continue;
							}
							break;
						case 2:
							if($map[($this->x - 1)][$this->y]->representation() == ".")
							{
								$found_direction = true;
								$newx--;
								continue;
							}
							break;
						case 3:
							if($map[($this->x)][($this->y + 1)]->representation() == ".")
							{
								$found_direction = true;
								$newy++;
								continue;
							}

							break;
						case 4:
							if($map[($this->x)][($this->y - 1)]->representation() == ".")
							{
								$found_direction = true;
								$newy--;
								continue;
							}
							break;
					}
				}
				setTile($oldx, $oldy, new Tile(new Floor()));
				setTile($newx, $newy, $this);
				$this->x = $newx;
				$this->y = $newy;
			} else {
				// Here we have to chase the player.
				$ran = 0;
				$moved = false;
				$attack = false;
					
				$gox = null;
				$goy = null;
				if(($this->target->x < ($this->x - ($this->viewrange*2)) or $this->target->x > ($this->x + ($this->viewrange*2))) or $this->target->y < ($this->y - ($this->viewrange*2)) or $this->target->y > ($this->y + ($this->viewrange*2)))
				{
					$moved = false;
					$attack = false;
					$this->target = null;
					$ran = 0;
					return;
				}
				if($this->target->x > $this->x)
				{
					$gox = "right";
				}elseif($this->target->x < $this->x) {
					$gox = "left";
				} else{
					$gox = "still";
				}
				if($this->target->y > $this->y)
				{
					$goy = "down";
				}elseif($this->target->y < $this->y) {
					$goy = "up";
				} else{
					$goy = "still";
				}

				if($goy != "still" && $gox != "still")
				{
					$yorx = rand(1,2);
					if($yorx == 1) //Y
					{
						if($goy == "up")
						{
							if($map[$this->x][($this->y-1)]->representation() == ".")
							{
								$this->doMove(0,-1);
								$moved = true;
							} else {
								//continue;
							}
						}elseif($goy == "down")
						{
							if($map[$this->x][($this->y+1)]->representation() == ".")
							{
								$this->doMove(0,1);
								$moved = true;
							} else {
								//continue;
							}
						}
					}elseif($yorx == 2) //X
					{
						if($gox == "left")
						{
							if($map[($this->x-1)][($this->y)]->representation() == ".")
							{
								$this->doMove(-1,0);
								$moved = true;
							} else {
								//continue;
							}
						}elseif($gox == "right")
						{
							if($map[($this->x+1)][($this->y)]->representation() == ".")
							{
								$this->doMove(1,0);
								$moved = true;
							} else {
								//continue;
							}
						}
					}
				}elseif($goy == "still" && $gox != "still")
				{
					if($gox == "left")
					{
							if($map[($this->x-1)][($this->y)]->representation() == ".")
							{
								$this->doMove(-1,0);
								$moved = true;
							} else {
								//continue;
							}
					}elseif($gox == "right")
					{
							if($map[($this->x+1)][($this->y)]->representation() == ".")
							{
								$this->doMove(1,0);
								$moved = true;
							} else {
								//continue;
							}
					}
				}elseif($goy != "still" && $gox == "still")
				{
					if($goy == "up")
					{
							if($map[$this->x][($this->y-1)]->representation() == ".")
							{
								$this->doMove(0,-1);
								$moved = true;
							} else {
								//continue;
							}
					}elseif($goy == "down")
					{
							if($map[$this->x][($this->y+1)]->representation() == ".")
							{
								$this->doMove(0,1);
								$moved = true;
							} else {
								//continue;
							}
					}
				} else {
					$moved = true;
					//continue;
				}
			
				$ran = 0;
				$moved = false;
			}
		}
		
	}

	public function doAttack()
	{
		global $map;
		/*$ystart = $this->y + $this->radius;
		$yend = $this->y - $this->radius;
		$xstart = $this->x - $this->radius;
		$xend = $this->x + $this->radius;

		for ($i=$yend; $i <= $ystart; $i++) {
			for($ix = $xstart; $ix <= $xend; $ix++)
			{
				if($map[$ix][$i] != null)
				{
					var_dump($map[$ix][$i]);
					if($map[$ix][$i]->type() == "player")
					{
						$map[$ix][$i]->damage($damage, $damage_type, $this);
					}
				}
			}
			if(!$floor && $wall)
			{ // Some kind of raycasting has to be done, so it doesn't create radius on other side of wall.
				//$yend = $i;
			}
			$wall = false;
			$floor = false;
		}*/
		$this->target->damage($this->basedamage, $this->damage_type, $this);
	}

	public function doMove($x, $y)
	{
		global $map;
		$oldx = $this->x;
		$oldy = $this->y;
		$newx = $this->x + $x;
		$newy = $this->y + $y;
		setTile($oldx, $oldy, new Tile(new Floor()));
		setTile($newx, $newy, $this);
		$this->x = $newx;
		$this->y = $newy;
		
	}

	public function representation()
	{
		return $this->representation;
	}

	public function solid()
	{
		return true;
	}

	public function type()
	{
		if(!isset($this->type))
		{
			return "npc";
		} else {
			return $this->type;
		}
	}

	public function color($player = null)
	{
		if($player != null)
		{
			if($this->playercolors[$player] != null)
			{
				return $this->playercolors[$player];
			} else {
				return $this->color;
			}
		} else {
			return $this->color;
		}
	}

	public function setColor($clientid, $color)
	{
		$this->playercolors[$clientid] = $color;
		return $this->color($clientid);
	}

	public function resetColor($clientid)
	{
		unset($this->playercolors[$clientid]);
		return true;
	}
}