<?php
/**
 * Compatibility include for pages that load optional debugging helpers.
 * Keep this file silent so it never alters HTML or JSON responses.
 */
error_reporting(E_ALL);
ini_set("display_errors", "on");
function print_r_pre($var)
{
    print("<pre>");
    print_r($var);
    print("</pre>");
}
