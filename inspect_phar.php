<?php
$pharPath = $argv[1] ?? '';
if($pharPath === '' || !file_exists($pharPath)){
    echo "PHAR_NOT_FOUND\n";
    exit(1);
}
$phar = new Phar($pharPath);
$needles = [
    'vendor/autoload.php',
    'vendor/cortexpe/commando/src/CortexPE/Commando/PacketHooker.php',
    'vendor/ramsey/uuid/src/Uuid.php',
];
$all = [];
foreach(new RecursiveIteratorIterator($phar) as $file){
    $path = str_replace('\\', '/', $file->getPathname());
    if(str_starts_with($path, 'phar://')){
        $parts = explode('.phar/', $path, 2);
        if(isset($parts[1])){
            $path = $parts[1];
        }
    }
    $all[$path] = true;
}
foreach($needles as $n){
    echo $n . ': ' . (isset($all[$n]) ? 'YES' : 'NO') . PHP_EOL;
}
