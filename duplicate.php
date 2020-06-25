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

    // sort by highest count
    usort($duplicated_by_content, function($a, $b){
        return count($b) <=> count($a);
    });

    $result = "No duplicated files.";

    // make sure the array of file is exist
    if(isset($duplicated_by_content[0][0])){ 
        // count the duplicated content
        $group = $duplicated_by_content[0];
        $count = count($group);

        // get the file content
        $file = $group[0];
        $content = file_get_contents($file['path']);

        // string limit for content result
        $content = strlen($content) > 50 ? substr($content,0,50)."..." : $content;

        // concat the result
        $result =  $content.' '.$count;
    }

    return $result;
}

echo showDuplicate();