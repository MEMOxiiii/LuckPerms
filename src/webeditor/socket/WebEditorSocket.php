<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\webeditor\socket;

/**
 * Stub: WebSocket-based live editor connection.
 *
 * The Java implementation uses bytesocks WebSocket to enable real-time
 * two-way communication between the server and the web editor. PocketMine-MP
 * does not include a WebSocket client library, so this feature is not available
 * in the PocketMine port. The standard HTTP-based workflow (upload payload via
 * bytebin → edit in browser → apply with /lp applyedits) works fully without it.
 */
class WebEditorSocket{

	/** No-op: socket connections are not supported in this port. */
	public function appendDetailToRequest(mixed $request) : void{ }

	/** No-op. */
	public function scheduleCleanupIfUnused() : void{ }
}
