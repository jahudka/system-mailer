<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

$config = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/../etc/config.yaml');
$transport = Symfony\Component\Mailer\Transport::fromDsn(format_dsn($config));
$mailer = new Symfony\Component\Mailer\Mailer($transport);

$src = ZBateson\MailMimeParser\Message::from(STDIN, false);
$msg = new Symfony\Component\Mime\Email();
$msg->from($config['mail_from']);

foreach (parse_cli_args($_SERVER['argv']) as $to) {
  $msg->addTo($to);
}

convert_message($src, $msg);

$mailer->send($msg);
