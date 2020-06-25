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
            "md5"  => md5_file($filepath),
        ];
    }
    
    // grouping by duplicated contents
    $content_of_files = array_count_values(array_column($files, 'md5'));
    $duplicated_by_content = [];

    foreach($content_of_files as $md5 => $count){
        // ignore not duplicated
        if($count <= 1){
            continue;
        }

        // get all files with current size
        $arr = array_filter($files, function($ar) use($md5) {
            return ($ar['md5'] == $md5);
        });

        // prevent double assign
        if(!isset($duplicated_by_content[$md5])){
            $duplicated_by_content[$md5] = $arr;
        }
    }

    return $duplicated_by_content;
}

print_r(showDuplicate());