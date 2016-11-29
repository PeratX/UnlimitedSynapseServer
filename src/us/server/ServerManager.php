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

use sf\console\Logger;
use us\server\network\protocol\SynapseInterface;

abstract class ServerManager{
	/** @var Client[] */
	protected $clients;

	/** @var SynapseInterface */
	protected $interface;

	protected $address;
	protected $port;

	public function __construct($address, int $port, $clientClass){
		if(!is_a($clientClass, Client::class)){
			Logger::error($clientClass . " is not extended from " . Client::class);
			return;
		}
		$this->address = $address;
		$this->port = $port;
		$this->interface = new SynapseInterface($this, $address, $port, $clientClass);
	}

	public final function getInterface() : SynapseInterface{
		return $this->interface;
	}

	public final function addClient(Client $client){
		$this->clients[$client->getHash()] = $client;
	}

	public final function removeClient(Client $client){
		if(isset($this->clients[$client->getHash()])){
			unset($this->clients[$client->getHash()]);
		}
	}

	public final function getClients(){
		return $this->clients;
	}

	public final function getAddress(){
		return $this->address;
	}

	public final function getPort() : int{
		return $this->port;
	}

	public abstract function shutdown();

	public abstract function getName() : string;
}