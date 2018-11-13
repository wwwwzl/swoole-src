--TEST--
swoole_coroutine: throw exception
--SKIPIF--
<?php require __DIR__ . '/../include/skipif.inc'; ?>
--FILE--
<?php
require __DIR__ . '/../include/bootstrap.php';
$pm = new ProcessManager;
$pm->parentFunc = function (int $pid) use ($pm) {
    echo curlGet("http://127.0.0.1:{$pm->getFreePort()}/");
};
$pm->childFunc = function () use ($pm) {
    $http = new swoole_http_server('127.0.0.1', $pm->getFreePort(), SWOOLE_BASE);
    $http->set(['worker_num' => 1]);
    $http->on('workerStart', function () use ($pm) {
        $pm->wakeup();
    });
    $http->on('request', function (swoole_http_request $request, swoole_http_response $response) {
        co::sleep(0.001);
        throw new Exception('whoops');
    });
    $http->start();
};
$pm->childFirst();
$pm->run();
?>
--EXPECTF--
Fatal error: Uncaught Exception: whoops in %s/swoole-src/tests/swoole_coroutine/exception.php:15
Stack trace:
#0 [internal function]: {closure}(Object(Swoole\Http\Request), Object(Swoole\Http\Response))
#1 %s/swoole-src/tests/swoole_coroutine/exception.php(17): Swoole\Server->start()
#2 %s/swoole-src/tests/include/functions.php(635): {closure}()
#3 %s/swoole-src/tests/include/functions.php(713): ProcessManager->runChildFunc()
#4 %s/swoole-src/tests/swoole_coroutine/exception.php(20): ProcessManager->run()
#5 {main}
  thrown in %s/swoole-src/tests/swoole_coroutine/exception.php on line 15
[%s]	ERROR	zm_deactivate_swoole (ERROR 503): Fatal error: Uncaught Exception: whoops in %s/swoole-src/tests/swoole_coroutine/exception.php:15
Stack trace:
#0 [internal function]: {closure}(Object(Swoole\Http\Request), Object(Swoole\Http\Response))
#1 %s/swoole-src/tests/swoole_coroutine/exception.php(17): Swoole\Server->start()
#2 %s/swoole-src/tests/include/functions.php(635): {closure}()
#3 %s/swoole-src/tests/include/functions.php(713): ProcessManager->runChildFunc()
#4 %s/swoole-src/tests/swoole_coroutine/exception.php(20): ProcessManager->run()
#5 {main}
  thrown in %s/swoole-src/tests/swoole_coroutine/exception.php on line 15.