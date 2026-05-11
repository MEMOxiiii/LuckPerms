<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\dependencies;

enum DependencyRegistry : string{
case HIKARI = 'hikari';
case SLF4J_SIMPLE = 'slf4j-simple';
case SLF4J_API = 'slf4j-api';
case MYSQL_DRIVER = 'mysql-driver';
case MARIADB_DRIVER = 'mariadb-driver';
case POSTGRESQL_DRIVER = 'postgresql-driver';
case H2_DRIVER = 'h2-driver';
case SQLITE_DRIVER = 'sqlite-driver';
case MONGODB_DRIVER = 'mongodb-driver';
case JEDIS = 'jedis';
case CAFFEINE = 'caffeine';
case OKHTTP = 'okhttp';
case BYTEBUDDY = 'bytebuddy';
case COMMODORE = 'commodore';
case ADVENTURE = 'adventure';
case GSON = 'gson';

public function getMavenRepoPath() : string{
return $this->value;
}
}
