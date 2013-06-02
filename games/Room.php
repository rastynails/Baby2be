<?php
class Room {
	var $gameID, $siteID, $roomID;
	var $folderPath, $lockFilePath;
	
	var $roomName;
	var $roomCapacity;
	var $players;
	var $tables;
	
	var $lockFileHandle, $lockCount;
	
	
	public function Room($siteID, $gameID, $roomID) {
		global $filesPath;
		
		$this->siteID = $siteID;
		$this->gameID = $gameID;
		$this->roomID = $roomID;
		
		$this->folderPath = "$filesPath/site_$siteID/game_$gameID/room_$roomID";
		$this->lockFilePath = $this->folderPath . '/lockFile';
		
		if(!file_exists($this->folderPath)) {
			mkdir($this->folderPath, 0777, true);
			
			touch($this->lockFilePath);
		}
		
		$this->players = array();
		$this->tables = array();
			
		$this->lockCount = 0;
	}
	
	
	public function lock() {
		$this->lockCount++;
		if($this->lockCount > 1) return true;
		
		$this->lockFileHandle = fopen($this->lockFilePath, 'r+');
		if($this->lockFileHandle === false) return false;
		
		if(!flock($this->lockFileHandle, LOCK_EX)) return false;
		
		if(!file_exists($this->lockFilePath)) return false;
		
		return true;
	}
	
	public function unlock() {
		$this->lockCount--;
		
		if($this->lockCount <= 0) {
			$this->lockCount = 0;
			
			fclose($this->lockFileHandle);
		}
	}
	
	public function loadInfo() {
		global $defaultRoomNames, $defaultRoomCapacities;
		global $gameRoomNames, $gameRoomCapacities;
		
		if(!isset($gameRoomNames[$this->gameID])) {
			$roomNames = $defaultRoomNames;
		} else {
			$roomNames = $gameRoomNames[$this->gameID];
		}
		
		if(!isset($gameRoomCapacities[$this->gameID])) {
			$roomCapacities = $defaultRoomCapacities;
		} else {
			$roomCapacities = $gameRoomCapacities[$this->gameID];
		}
		
		$this->roomName = isset($roomNames[$this->roomID - 1]) ? $roomNames[$this->roomID - 1] : '';
		$this->roomCapacity = isset($roomCapacities[$this->roomID - 1]) ? $roomCapacities[$this->roomID - 1] : -1;
		
		$this->players = array();
		$this->tables = array();
		
		$directory = opendir($this->folderPath);
		
		while(($fileName = readdir($directory)) !== false) {
			if(substr($fileName, 0, 7) == 'player_') {
				$playerUID = substr($fileName, 7);
				$player = new Player($this->siteID, $this->gameID, $this->roomID, $playerUID);
				$player->loadInfo();
				$this->players[] = $player;
				
			} else if(substr($fileName, 0, 6) == 'table_') {
				$tableUID = substr($fileName, 6);
				$table = new Table($this->siteID, $this->gameID, $this->roomID, $tableUID);
				$table->loadInfo();
				$this->tables[] = $table;
			}
		}
		
		closedir($directory);
	}
	
	public function checkIsFull() {
		if($this->roomCapacity == -1) return false;
		
		if(count($this->players) >= $this->roomCapacity) return true;
		
		return false;
	}
	
	public function playerJoined($playerName, $isRobot) {
		global $extraKeyLength;
		
		$extraKeyCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		$dateString = date('YmdHis');
		
		for($i=0;true;$i++) {
			$playerUID = "$dateString$i";
			
			if(!file_exists("$this->folderPath/player_$playerUID")) break;
		}
		
		$player = new Player($this->siteID, $this->gameID, $this->roomID, $playerUID);
		$player->playerName = $this->getUniquePlayerName($playerName);
		$player->isRobot = $isRobot;
		$player->lastActivityTime = time();
		
		$player->extraKey = '';
		for($i=0;$i<$extraKeyLength;$i++) {
			$player->extraKey .= substr($extraKeyCharacters, floor(rand(0, strlen($extraKeyCharacters) - 1)), 1);
		}
		
		$player->create();
		$player->saveInfo();
		
		$this->players[] = $player;
		
		$idlePlayers = $this->getIdlePlayers();
		
		for($i=0;$i<count($idlePlayers);$i++) {
			if($idlePlayers[$i]->playerUID == $player->playerUID) continue;
			
			$idlePlayers[$i]->sendPlayerJoinedRoomMessage($player);
		}
		
		return $player;
	}
	
	private function getUniquePlayerName($playerName) {
		$countPlayers = count($this->players);
		
		for($i=0;$i<$countPlayers;$i++) {
			if($this->players[$i]->playerName == $playerName) break;
		}
		
		if($i >= $countPlayers) return $playerName;
		
		$index = 2;
		
		while(true) {
			$newPlayerName = "$playerName $index";
			
			for($i=0;$i<$countPlayers;$i++) {
				if($this->players[$i]->playerName == $newPlayerName) break;
			}
			
			if($i >= $countPlayers) return $newPlayerName;
			
			$index++;
		}
		
		return $playerName;
	}
	
	private function getIdlePlayers() {
		for($i=0;$i<count($this->players);$i++) {
			$isIdle = true;
			
			for($j=0;$j<count($this->tables);$j++) {
				if(!$this->tables[$j]->isPlaying) continue;
				
				for($k=0;$k<count($this->tables[$j]->playerUIDs);$k++) {
					if($this->players[$i]->playerUID == $this->tables[$j]->playerUIDs[$k]) {
						$isIdle = false;
						break;
					}
				}
				
				if(!$isIdle) break;
				
				for($k=0;$k<count($this->tables[$j]->viewerUIDs);$k++) {
					if($this->players[$i]->playerUID == $this->tables[$j]->viewerUIDs[$k]) {
						$isIdle = false;
						break;
					}
				}
				
				if(!$isIdle) break;
			}
			
			if(!$isIdle) continue;
			
			$idlePlayers[] = $this->players[$i];
		}
		
		return $idlePlayers;
	}
	
	public function removeInactivePlayers() {
		global $inactiveTimeout;
		
		for($i=0;$i<count($this->players);$i++) {
			if($this->players[$i]->lastActivityTime + $inactiveTimeout < time()) {
				$inactivePlayers[] = $this->players[$i];
				
				$this->players[$i]->sendSelfDisconnectedMessage();
				
				$this->players[$i]->destroy();
				array_splice($this->players, $i, 1);
				$i--;
			}
		}
		
		if(count($inactivePlayers) == 0) return;
		
		$playingPlayerUIDs = array();
		
		for($i=0;$i<count($this->tables);$i++) {
			$playersRemoved = $this->tables[$i]->removePlayers($inactivePlayers);
			$this->tables[$i]->removeViewers($inactivePlayers);
			
			if(count($this->tables[$i]->playerUIDs) == 0) {
				$this->tables[$i]->destroy();
				array_splice($this->tables, $i, 1);
				$i--;
				
				continue;
			}
			
			if($playersRemoved && $this->tables[$i]->isPlaying) {
				$playingPlayerUIDs = array_merge($playingPlayerUIDs, $this->tables[$i]->playerUIDs);
			}
		}
		
		$idlePlayers = $this->getIdlePlayers();
		
		for($i=0;$i<count($idlePlayers);$i++) {
			$idlePlayers[$i]->sendPlayersDisconnectedMessage($inactivePlayers);
		}
		
		for($i=0;$i<count($playingPlayerUIDs);$i++) {
			if($playingPlayerUIDs[$i] == 'robot') continue;
			
			$player = $this->getPlayer($playingPlayerUIDs[$i]);
			$player->sendPlayersDisconnectedMessage($inactivePlayers);
		}
	}
	
	public function openTable($player, $possibleNoOfPlayers) {
		$table = $this->getPlayerTable($player->playerUID);
		if($table != null) return null;
		
		$dateString = date('YmdHis');
		
		for($i=0;true;$i++) {
			$tableUID = "$dateString$i";
			
			if(!file_exists("$this->folderPath/table_$tableUID")) break;
		}
		
		$table = new Table($this->siteID, $this->gameID, $this->roomID, $tableUID);
		$table->possibleNoOfPlayers = $possibleNoOfPlayers;
		$table->playerUIDs[] = $player->playerUID;
		$table->viewerUIDs = array();
		$table->isPlaying = false;
		
		$table->create();
		
		$table->saveInfo();
		
		$this->tables[] = $table;
		
		$idlePlayers = $this->getIdlePlayers();
		
		for($i=0;$i<count($idlePlayers);$i++) {
			if($idlePlayers[$i]->playerUID == $player->playerUID) continue;
			
			$idlePlayers[$i]->sendPlayerOpenedTableMessage($player, $table);
		}
		
		return $table;
	}
	
	public function joinTable($player, $tableUID) {
		$table = $this->getPlayerTable($player->playerUID);
		if($table != null) return false;
		
		$table = $this->getTable($tableUID);
		
		$success = $table->playerJoined($player);
		
		if($success) {
			$idlePlayers = $this->getIdlePlayers();
		
			for($i=0;$i<count($idlePlayers);$i++) {
				if($idlePlayers[$i]->playerUID == $player->playerUID) continue;
				
				$idlePlayers[$i]->sendPlayerJoinedTableMessage($player, $table);
			}
		}
		
		return $success;
	}
	
	public function robotJoinTable($tableUID) {
		$table = $this->getTable($tableUID);
		
		$success = $table->robotJoined();
		
		if($success) {
			$idlePlayers = $this->getIdlePlayers();
		
			for($i=0;$i<count($idlePlayers);$i++) {
				$idlePlayers[$i]->sendRobotJoinedTableMessage($table);
			}
		}
		
		return $success;
	}
	
	public function playerInvitesPlayer($player, $invitedPlayerUID) {
		$table = $this->getPlayerTable($player->playerUID);
		
		if($table == null) {
			$success = false;
		} else {
			$invitedPlayer = $this->getPlayer($invitedPlayerUID);
			
			if($invitedPlayer == null) {
				$success = false;
			} else {
				$success = true;
				$invitedPlayer->sendInviteMessage($player, $table);
			}
		}
		
		return $success;
	}
	
	public function getPlayerTable($playerUID) {
		for($i=0;$i<count($this->tables);$i++) {
			for($j=0;$j<count($this->tables[$i]->playerUIDs);$j++) {
				if($this->tables[$i]->playerUIDs[$j] == $playerUID) {
					return $this->tables[$i];
				}
			}
		}
		
		return null;
	}
	
	public function getPlayer($playerUID) {
		for($i=0;$i<count($this->players);$i++) {
			if($this->players[$i]->playerUID == $playerUID) return $this->players[$i];
		}
		
		return null;
	}
	
	public function getTable($tableUID) {
		for($i=0;$i<count($this->tables);$i++) {
			if($this->tables[$i]->tableUID == $tableUID) return $this->tables[$i];
		}
		
		return null;
	}
	
	public function startPlaying($player) {
		$table = $this->getPlayerTable($player->playerUID);
		
		if($table == null) return false;
		if(!$table->getCanStartPlaying()) return false;
		
		for($i=0;$i<count($table->playerUIDs);$i++) {
			$randomSeeds[] = rand(0, 0x7FFFFFFF);
		}
		
		$idlePlayers = $this->getIdlePlayers();
		
		for($i=0;$i<count($idlePlayers);$i++) {
			$idlePlayers[$i]->sendPlayingStartedMessage($table, $randomSeeds);
		}

		$table->startPlaying($this);
		
		return true;
	}
	
	public function viewTable($player, $tableUID) {
		$table = new Table($this->siteID, $this->gameID, $this->roomID, $tableUID);
		$table->loadInfo();
		
		$success = $table->playerViewed($player);
		
		if($success) {
			$idlePlayers = $this->getIdlePlayers();
		
			for($i=0;$i<count($idlePlayers);$i++) {
				if($idlePlayers[$i]->playerUID == $player->playerUID) continue;
				
				$idlePlayers[$i]->sendPlayerViewedTableMessage($player, $table);
			}
		}
		
		return $success;
	}
}
?>