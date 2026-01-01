<?php
function safeRequire(string $path, string $className): bool
{
    if (! file_exists($path)) {
        error_log("[ServiceLoader] Missing file: {$path}");
        return false;
    }
    require_once $path;
    if (! class_exists($className)) {
        error_log("[ServiceLoader] Class not found: {$className}");
        return false;
    }
    return true;
}
