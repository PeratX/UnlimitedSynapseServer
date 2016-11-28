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

namespace us\server\network\packet;

class Info{
	const CURRENT_PROTOCOL = 1;

	const AUTHORIZE_PACKET = 0x01;
	const HEARTBEAT_PACKET = 0x02;
	const CONNECT_PACKET = 0x03;
	const DISCONNECT_PACKET = 0x04;
}