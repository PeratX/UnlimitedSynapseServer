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
			return true;
		}
	}

	public function getSocket(){
		return $this->socket;
	}

	public function close(){
		@socket_close($this->socket);
	}

	public function readPacket(){
		$packets = [];
		if($this->receiveBuffer !== "" && strlen($this->receiveBuffer) > 0){
			$len = strlen($this->receiveBuffer);
			$offset = 0;
			while($offset < $len){
				if($offset > $len - 4) break;
				$pkLen = Binary::readInt(substr($this->receiveBuffer, $offset, 4));
				$offset +=  4;

				if($pkLen <= ($len - $offset)) {
					$buf = substr($this->receiveBuffer, $offset, $pkLen);
					$offset += $pkLen;

					$packets[] = $buf;
				} else {
					$offset -= 4;
					break;
				}
			}
			if($offset < $len){
				$this->receiveBuffer = substr($this->receiveBuffer, $offset);
			}else{
				$this->receiveBuffer = "";
			}
		}

		return $packets;
	}

	public function writePacket($data){
		@socket_write($this->socket, Binary::writeInt(strlen($data)) . $data);
	}
}