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

class SynapseSocket{
	private $socket;

	public function __construct($port = 26666, $interface = "0.0.0.0"){
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(@socket_bind($this->socket, $interface, $port) !== true){
			Logger::critical("Cannot listen on " . $interface . ":" . $port . "!");
			Logger::critical("Perhaps a server is already running on that port?");
			exit(1);
		}
		socket_listen($this->socket);
		Logger::info("Synapse is running on $interface:$port");
		socket_set_nonblock($this->socket);
	}

	public function getClient(){
		return socket_accept($this->socket);
	}

	public function getSocket(){
		return $this->socket;
	}

	public function close(){
		socket_close($this->socket);
	}
}