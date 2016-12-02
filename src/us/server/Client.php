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

abstract class Client{
	const CLOSE_REASON_NONE = 0;
	const CLOSE_REASON_DISCONNECT = 1;

	/** @var SynapseInterface */
	private $interface;
	private $address;
	private $port;

	public function __construct(SynapseInterface $interface, $address, int $port){
		$this->interface = $interface;
		$this->address = $address;
		$this->port = $port;
	}

	public final function getHash() : string{
		return $this->address . ':' . $this->port;
	}

	public abstract function handleDataPacket(DataPacket $packet);

	public final function sendDataPacket(DataPacket $pk){
		$this->interface->putPacket($this, $pk);
	}

	public final function getAddress(){
		return $this->address;
	}

	public final function getPort() : int{
		return $this->port;
	}

	public abstract function close(int $reason = self::CLOSE_REASON_NONE);
}
