<?php

    $SourceDirectory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'src' . DIRECTORY_SEPARATOR;
    include_once($SourceDirectory . 'pwc' . DIRECTORY_SEPARATOR . 'pwc.php');

    $pwc = new \pwc\pwc();
    var_dump($pwc->checkPassword('ExamplePassword'));