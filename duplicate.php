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

function showDuplicate(){
    $filepaths = fetchAllFiles("./DropsuiteTest");
    $ignore = ['.DS_Store'];
    $files = [];

    // wrap the details of files
    foreach($filepaths as $filepath){
        if(filesize($filepath) == 0 || in_array(basename($filepath), $ignore)){
            continue;
        }

        $files[] = [
            "size" => filesize($filepath),
            "name" => basename($filepath),
            "path" => $filepath,
        ];
    }

    return $files;
}

print_r(showDuplicate());