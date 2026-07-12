<?php
function debug(mixed $data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
