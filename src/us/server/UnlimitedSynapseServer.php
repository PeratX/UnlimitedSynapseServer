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

class UnlimitedSynapseServer extends Module{

	public function load(){
		Logger::info("UnlimitedSynapseServer is loaded.");
	}

	public function unload(){
		Logger::info("UnlimitedSynapseServer is unloaded.");
	}

	public function registerManager(ServerManager $manager){
		//TODO
	}

	public function getLoader(){
		return $this->framework->getLoader();
	}
}