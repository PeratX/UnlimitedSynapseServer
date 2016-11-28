<?php

/**
 * UnlimitedSynapseServer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PeratX
 */

namespace us\server\network\protocol;

use sf\console\Logger;
use us\server\util\Binary;

class Session{

	private $receiveBuffer = "";
	private $sendBuffer = "";
	/** @var SessionManager */
	private $sessionManager;
	/** @var resource */
	private $socket;
	private $ip;
	private $port;

	public function __construct(SessionManager $sessionManager, $socket){
		$this->sessionManager = $sessionManager;
		$this->socket = $socket;
		socket_getpeername($this->socket, $address, $port);
		$this->ip = $address;
		$this->port = $port;
		Logger::notice("Client [$address:$port] has connected.");
	}

	public function getHash(){
		return $this->ip . ':' . $this->port;
	}

	public function getIp() : string {
		return $this->ip;
	}

	public function getPort() : int{
		return $this->port;
	}

	public function update(){
		$err = socket_last_error($this->socket);
		socket_clear_error($this->socket);
		if($err == 10057 or $err == 10054){
			Logger::error("Synapse client [$this->ip:$this->port] has disconnected unexpectedly");
			return false;
		}else{
			$data = @socket_read($this->socket, 65535, PHP_BINARY_READ);
			if($data != ""){
				$this->receiveBuffer .= $data;
			}
			if($this->sendBuffer != ""){
				socket_write($this->socket, $this->sendBuffer);
				$this->sendBuffer = "";
			}
			return true;
		}
	}

	public function getSocket(){
		return $this->socket;
	}

	public function close(){
		@socket_close($this->socket);
	}

	public function readPacket(){//TODO: check
		$len = Binary::readLInt(substr($this->receiveBuffer, 0, 4));
		if(strlen($this->receiveBuffer) >= ($len + 4)){
			$buffer = substr($this->receiveBuffer, 4, $len);
			$this->receiveBuffer = substr($this->receiveBuffer, $len + 1);
			return $buffer;
		}
		return null;
	}

	public function writePacket($data){
		$this->sendBuffer .= Binary::writeLInt(strlen($data)) . $data;
	}
}