<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Lib_mysql
{
    function connect($server, $username, $password)
    {
        if (!$server or !$username) {
            return FALSE;
        }

        if (@mysql_connect($server, $username, $password)) {
            return TRUE;
        }

        return FALSE;
    }

    function select_db($database)
    {
        if (@mysql_select_db($database)) {
            return TRUE;
        }

        return FALSE;
    }

    function query($sql)
    {
        $result = mysql_query($sql);

        return mysql_fetch_object($result);
    }

}
