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
use us\server\util\Binary;

class UnlimitedSynapseServer extends Module{
	/** @var UnlimitedSynapseServer */
	private static $obj = null;

	/** @var ServerManager[] */
	private $managers;

	public function load(){
		@define("ENDIANNESS", (pack("d", 1) === "\77\360\0\0\0\0\0\0" ? Binary::BIG_ENDIAN : Binary::LITTLE_ENDIAN));
		if(self::$obj === null){
			self::$obj = $this;
		}
		Logger::info("UnlimitedSynapseServer is loaded.");
	}

	public function unload(){
		Logger::info("UnlimitedSynapseServer is unloaded.");
	}

	public static function getInstance() : UnlimitedSynapseServer{
		return self::$obj;
	}

	public function registerManager(ServerManager $manager){
		Logger::info("UnlimitedSynapse Interface [" . $manager->getName() . "] is listening on " . $manager->getAddress() . ":" . $manager->getPort());
		$this->managers[spl_object_hash($manager)] = $manager;
	}

	public function unregisterManager(ServerManager $manager){
		if(isset($this->managers[spl_object_hash($manager)])){
			Logger::info("Closing UnlimitedSynapse Interface [" . $manager->getName() . "] ...");
			$this->managers[spl_object_hash($manager)]->shutdown();
			unset($this->managers[spl_object_hash($manager)]);
		}
	}
}