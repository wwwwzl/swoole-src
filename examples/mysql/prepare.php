<?php
$db = new swoole_mysql;
$server = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    'database' => 'test',
);

$db->on('close', function() use($db) {
    echo "mysql is closed.\n";
});

$r = $db->connect($server, function ($db, $result)
{
    if ($result === false)
    {
        var_dump($db->connect_errno, $db->connect_error);
        die;
    }
    echo "connect to mysql server sucess\n";
    $db->prepare('SELECT id, name FROM userinfo WHERE id=?', function (swoole_mysql $db, $r)
    {
        $db->execute(array(1), function ($db, $r){
            var_dump($r);
        });
    });
});
