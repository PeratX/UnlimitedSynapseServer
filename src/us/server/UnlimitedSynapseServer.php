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
use sf\module\Module;
use sf\util\Config;

class UnlimitedSynapseServer extends Module{
	/** @var Config */
	private $config;

	private $address;
	private $port;

	public function load(){
		@mkdir($this->getDataFolder());
		$this->config = new Config($this->getDataFolder() . "config.json", Config::JSON, [
			"address" => "0.0.0.0",
			"port" => "26666",
		]);
		$this->initConfig();
		Logger::info("UnlimitedSynapseServer is listening on " . $this->getAddress() . ":" . $this->getPort());
	}

	public function unload(){
		Logger::info("UnlimitedSynapseServer is unloaded.");
	}

	public function initConfig(){
		$this->address = $this->config->get("address", "0.0.0.0");
		$this->port = (int) $this->config->get("port", "26666");
	}

	public function getAddress() : string {
		return $this->address;
	}

	public function getPort() : int{
		return (int) $this->port;
	}
}