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

namespace us\server;

use us\server\network\packet\DataPacket;
use us\server\network\protocol\SynapseInterface;

class Client{
	/** @var SynapseInterface */
	private $interface;
	private $ip;
	private $port;

	public function __construct(SynapseInterface $interface, $ip, int $port){
		$this->interface = $interface;
		$this->ip = $ip;
		$this->port = $port;
	}

	public function getHash() : string{
		return $this->ip . ':' . $this->port;
	}

	public function handleDataPacket(DataPacket $packet){
	}

	public function sendDataPacket(DataPacket $pk){
		$this->interface->putPacket($this, $pk);
	}

	public function getIp(){
		return $this->ip;
	}

	public function getPort() : int{
		return $this->port;
	}
}
