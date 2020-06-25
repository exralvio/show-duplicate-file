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
    $ignored = ['.DS_Store'];
    $files = [];

    // wrap the details of files
    foreach($filepaths as $filepath){
        if(filesize($filepath) == 0 || in_array(basename($filepath), $ignored)){
            continue;
        }

        $files[] = [
            "size" => filesize($filepath),
            "name" => basename($filepath),
            "path" => $filepath,
        ];
    }

    // grouping duplicated files by size
    $size_of_files = array_count_values(array_column($files, 'size'));
    $duplicated_by_size = [];

    foreach($size_of_files as $size => $count){
        // ignore not duplicated
        if($count <= 1){
            continue;
        }

        // get all files with current size
        $arr = array_filter($files, function($ar) use($size) {
            return ($ar['size'] == $size);
        });

        // prevent double assign
        if(!isset($duplicated_by_size[$size])){
            $duplicate_by_size[$size] = $arr;
        }
    }

    return $duplicate_by_size;
}

print_r(showDuplicate());