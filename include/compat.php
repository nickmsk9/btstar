<?php
if (!defined('IN_TRACKER') && !defined('IN_ANNOUNCE')) {
    die('Hacking attempt!');
}

if (!defined('MYSQL_ASSOC')) {
    define('MYSQL_ASSOC', MYSQLI_ASSOC);
}
if (!defined('MYSQL_NUM')) {
    define('MYSQL_NUM', MYSQLI_NUM);
}
if (!defined('MYSQL_BOTH')) {
    define('MYSQL_BOTH', MYSQLI_BOTH);
}

foreach (array(
    'none', 'type', 'value', 'id', 'name', 'username', 'class', 'cat', 'tags',
    'owner', 'image', 'text', 'msg', 'subject', 'body', 'added', 'enabled',
    'status', 'country', 'city', 'title', 'avatar', 'passkey', 'info_hash',
    'peer_id', 'port', 'downloaded', 'uploaded', 'left', 'ip', 'agent',
    'hours', 'minutes', 'seconds', 'yes', 'no', 'all', 'lastpagedefault',
) as $constantName) {
    if (!defined($constantName)) {
        define($constantName, $constantName);
    }
}

if (!function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime($new_setting)
    {
        return false;
    }
}

if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime()
    {
        return false;
    }
}

if (!function_exists('ereg')) {
    function ereg($pattern, $string, &$regs = null)
    {
        $result = preg_match('/' . str_replace('/', '\/', $pattern) . '/', $string, $matches);
        if ($regs !== null) {
            $regs = $matches;
        }
        return $result;
    }
}

if (!function_exists('eregi')) {
    function eregi($pattern, $string, &$regs = null)
    {
        $result = preg_match('/' . str_replace('/', '\/', $pattern) . '/i', $string, $matches);
        if ($regs !== null) {
            $regs = $matches;
        }
        return $result;
    }
}

if (!function_exists('split')) {
    function split($pattern, $string, $limit = -1)
    {
        return preg_split('/' . str_replace('/', '\/', $pattern) . '/', $string, $limit);
    }
}

if (!function_exists('each')) {
    function each(&$array)
    {
        $key = key($array);
        if ($key === null) {
            return false;
        }

        $value = current($array);
        next($array);

        return array(1 => $value, 'value' => $value, 0 => $key, 'key' => $key);
    }
}

if (!function_exists('mysql_connect')) {
    $GLOBALS['__mysql_compat_link'] = null;

    function mysql_connect($server = null, $username = null, $password = null, $new_link = false, $client_flags = 0)
    {
        $server = $server ?: ini_get('mysqli.default_host') ?: 'localhost';
        $username = $username ?: ini_get('mysqli.default_user');
        $password = $password ?? ini_get('mysqli.default_pw');

        mysqli_report(MYSQLI_REPORT_OFF);
        $link = mysqli_connect($server, $username, $password);
        if (!$link) {
            return false;
        }

        $GLOBALS['__mysql_compat_link'] = $link;
        return $link;
    }

    function mysql_pconnect($server = null, $username = null, $password = null, $client_flags = 0)
    {
        return mysql_connect($server, $username, $password, true, $client_flags);
    }

    function mysql_select_db($database_name, $link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_select_db($link, $database_name) : false;
    }

    function mysql_query($query, $link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_query($link, $query) : false;
    }

    function mysql_unbuffered_query($query, $link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_query($link, $query, MYSQLI_USE_RESULT) : false;
    }

    function mysql_fetch_array($result, $result_type = MYSQL_BOTH)
    {
        return $result instanceof mysqli_result ? mysqli_fetch_array($result, $result_type) : false;
    }

    function mysql_fetch_assoc($result)
    {
        return $result instanceof mysqli_result ? mysqli_fetch_assoc($result) : false;
    }

    function mysql_fetch_row($result)
    {
        return $result instanceof mysqli_result ? mysqli_fetch_row($result) : false;
    }

    function mysql_num_rows($result)
    {
        return $result instanceof mysqli_result ? mysqli_num_rows($result) : 0;
    }

    function mysql_numrows($result)
    {
        return mysql_num_rows($result);
    }

    function mysql_affected_rows($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_affected_rows($link) : -1;
    }

    function mysql_insert_id($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_insert_id($link) : 0;
    }

    function mysql_info($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_info($link) : false;
    }

    function mysql_real_escape_string($unescaped_string, $link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_real_escape_string($link, $unescaped_string) : addslashes($unescaped_string);
    }

    function mysql_escape_string($unescaped_string)
    {
        return mysql_real_escape_string($unescaped_string);
    }

    function mysql_error($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_error($link) : mysqli_connect_error();
    }

    function mysql_errno($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_errno($link) : mysqli_connect_errno();
    }

    function mysql_ping($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        return $link ? mysqli_ping($link) : false;
    }

    function mysql_close($link_identifier = null)
    {
        $link = $link_identifier ?: $GLOBALS['__mysql_compat_link'];
        if (!$link) {
            return false;
        }
        $closed = mysqli_close($link);
        if ($closed && $link === $GLOBALS['__mysql_compat_link']) {
            $GLOBALS['__mysql_compat_link'] = null;
        }
        return $closed;
    }

    function mysql_free_result($result)
    {
        if ($result instanceof mysqli_result) {
            mysqli_free_result($result);
            return true;
        }
        return false;
    }

    function mysql_result($result, $row, $field = 0)
    {
        if (!$result instanceof mysqli_result) {
            return false;
        }
        mysqli_data_seek($result, $row);
        $data = mysqli_fetch_array($result);
        return $data[$field] ?? false;
    }
}
