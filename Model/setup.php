<?php

// Load env variables
function loadEnv($path) {
    if(!file_exists($path)) {
        return false;
    }

    $lines + file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $value)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

