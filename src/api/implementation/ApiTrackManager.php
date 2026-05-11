<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\implementation;

use jasonw4331\LuckPerms\api\track\Track as TrackInterface;
use jasonw4331\LuckPerms\api\track\TrackManager as TrackManagerInterface;
use jasonw4331\LuckPerms\LuckPerms;
use jasonw4331\LuckPerms\model\Track as InternalTrack;
use function array_map;
use function array_values;
use function strtolower;

class ApiTrackManager extends ApiAbstractManager implements TrackManagerInterface{
public function __construct(LuckPerms $plugin){
parent::__construct($plugin);
}

private function proxy(InternalTrack $track) : ApiTrack{
return new ApiTrack($this->plugin, $track);
}

public function createAndLoadTrack(string $name) : ?TrackInterface{
$track = $this->plugin->getTrackManager()->getOrMake(strtolower($name));
return $this->proxy($track);
}

public function loadTrack(string $name) : ?TrackInterface{
$track = $this->plugin->getTrackManager()->getIfLoaded(strtolower($name));
return $track !== null ? $this->proxy($track) : null;
}

public function saveTrack(TrackInterface $track) : void{
if($track instanceof ApiTrack){
$this->plugin->getStorage()->saveTrack($track->getInternalTrack());
}
}

public function deleteTrack(TrackInterface $track) : void{
if($track instanceof ApiTrack){
$this->plugin->getTrackManager()->unload($track->getName());
}
}

public function loadAllTracks() : void{
$this->plugin->getStorage()->loadAllTracks();
}

public function getTrack(string $name) : ?TrackInterface{
$track = $this->plugin->getTrackManager()->getIfLoaded(strtolower($name));
return $track !== null ? $this->proxy($track) : null;
}

public function isLoaded(string $name) : bool{
return $this->plugin->getTrackManager()->getIfLoaded(strtolower($name)) !== null;
}

public function getLoadedTracks() : array{
return array_values(array_map(
fn(InternalTrack $t) => $this->proxy($t),
$this->plugin->getTrackManager()->getAll()
));
}
}
