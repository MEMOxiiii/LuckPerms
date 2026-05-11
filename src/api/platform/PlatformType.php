<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\api\platform;

enum PlatformType: string
{
    case BUKKIT     = 'Bukkit';
    case BUNGEECORD = 'BungeeCord';
    case SPONGE     = 'Sponge';
    case NUKKIT     = 'Nukkit';
    case VELOCITY   = 'Velocity';
    case FABRIC     = 'Fabric';
    case NEOFORGE   = 'NeoForge';
    case FORGE      = 'Forge';
    case STANDALONE = 'Standalone';
    case HYTALE     = 'Hytale';

    public function getFriendlyName(): string
    {
        return $this->value;
    }
}
