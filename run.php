<?php

require 'file.php';

$file = new File();
$file->search_path = './DropsuiteTest';

echo $file->showDuplicatedContent();