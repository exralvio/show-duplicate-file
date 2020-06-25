<?php

function fetchAllFiles($dir, &$results = []){
    $files = scandir($dir);

    foreach($files as $key => $entry){
        $path = realpath($dir . DIRECTORY_SEPARATOR . $entry);
        if(!is_dir($path)){
            $results[] = $path;
        } else if ($entry != "." && $entry != ".."){
            fetchAllFiles($path, $results);
        }
    }

    return $results;
}

print_r(fetchAllFiles('./DropsuiteTest'));