<?php

declare(strict_types=1);

ini_set('display_errors', 0);

try {
    $clientIP = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER["HTTP_CF_CONNECTING_IP"] # when behind cloudflare
        ?? $_SERVER['HTTP_X_FORWARDED']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_FORWARDED']
        ?? $_SERVER['HTTP_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';

    $port = $_SERVER['REMOTE_PORT'];

    $message = "({$clientIP}:{$port}) ";
    $message .= $_SERVER['REQUEST_URI'] . ' ';
    $message .= $_SERVER['HTTP_USER_AGENT'];

    send_remote_syslog($message);

} catch (\Throwable $exception) {
    error_log($exception->getMessage());
}

function send_remote_syslog($message, $program = "web"): void
{

    if (!($sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP))) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);

        die("Couldn't create socket: [$errorcode] $errormsg \n");
    }

    $host = get_host();
    $port = get_port();
    $hostName = get_hostname();

    foreach (explode("\n", $message) as $line) {
        $syslog_message = "<22>" . date('M d H:i:s ') . $hostName . ' ' . $program . ': ' . $line;
        $response = socket_sendto(
            $sock,
            $syslog_message,
            strlen($syslog_message),
            0,
            $host,
            $port
        );

        error_log("{$host}:{$port} - message length: {$response}");
    }

    socket_close($sock);
}

function get_host(): string
{
    $env = parse_ini_file(__DIR__ . '/../.env');

    return $env['LOG_URL'];
}

function get_port(): int
{
    $env = parse_ini_file(__DIR__ . '/../.env');

    return (int) $env['LOG_PORT'];
}

function get_hostname(): string
{
    $env = parse_ini_file(__DIR__ . '/../.env');

    return $env['APP_NAME'];
}
