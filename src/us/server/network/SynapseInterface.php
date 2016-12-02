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
use sf\Framework;
use us\server\Client;
use us\server\network\packet\DataPacket;
use us\server\ClientManager;

class SynapseInterface{
	private $clientManager;
	private $address;
	private $port;
	/** @var Client[] */
	private $clients;
	/** @var DataPacket[] */
	private $packetPool = [];
	/** @var SynapseServer */
	private $interface;

	public function __construct(ClientManager $clientManager, $address, int $port){
		$this->clientManager = $clientManager;
		$this->address = $address;
		$this->port = $port;
		$this->packetPool = new \SplFixedArray(256);
		$this->interface = new SynapseServer($this, Framework::getInstance()->getLoader(), $port, $address);
	}

	public function getClientManager(){
		return $this->clientManager;
	}

	public function addClient($ip, $port){
		$this->clients[$ip . ":" . $port] = $this->clientManager->getNewClient($ip, $port);
	}

	public function removeClient(Client $client){
		$this->interface->addExternalClientCloseRequest($client->getHash());
		unset($this->clients[$client->getHash()]);
	}

	public function putPacket(Client $client, DataPacket $pk){
		if(!$pk->isEncoded){
			$pk->encode();
		}
		$this->interface->pushMainToThreadPacket($client->getHash() . "|" . $pk->buffer);
	}

	public function process(){
		while(strlen($data = $this->interface->getClientOpenRequest()) > 0){
			$tmp = explode(":", $data);
			$this->addClient($tmp[0], $tmp[1]);
		}
		while(strlen($data = $this->interface->readThreadToMainPacket()) > 0){
			$tmp = explode("|", $data, 2);
			if(count($tmp) == 2){
				$this->handlePacket($tmp[0], $tmp[1]);
			}
		}
		while(strlen($data = $this->interface->getInternalClientCloseRequest()) > 0){
			$this->clients[$data]->close(Client::CLOSE_REASON_DISCONNECT);
			$this->clientManager->removeClient($this->clients[$data]);
			unset($this->clients[$data]);
		}
	}

	/**
	 * @param $buffer
	 *
	 * @return DataPacket
	 */
	public function getPacket($buffer){
		$pid = ord($buffer{0});
		/** @var DataPacket $class */
		$class = $this->packetPool[$pid];
		if($class !== null){
			$pk = clone $class;
			$pk->setBuffer($buffer, 1);
			return $pk;
		}
		return null;
	}

	public function handlePacket($hash, $buffer){
		if(!isset($this->clients[$hash])){
			return;
		}

		$client = $this->clients[$hash];

		if(($pk = $this->getPacket($buffer)) != null){
			$pk->decode();
			$client->handleDataPacket($pk);
		}else{
			Logger::critical("Error packet: 0x" . dechex(ord($buffer{0})) . " $buffer");
		}
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public function registerPacket($id, $class){
		$this->packetPool[$id] = new $class;
	}
}