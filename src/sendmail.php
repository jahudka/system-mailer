<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

try {
  $configFile = __DIR__ . '/../etc/config.yaml';
  $config = Symfony\Component\Yaml\Yaml::parseFile($configFile);
} catch (Throwable $e) {
  printf("Unable to load config file '%s': %s\n", $configFile, $e->getMessage());
  exit(1);
}

if ($missing = array_diff(['smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'mail_from'], array_keys(array_filter($config)))) {
  printf("Missing config key%s '%s'\n", count($missing) > 1 ? 's' : '', implode("', '", $missing));
  exit(1);
}

$transport = Symfony\Component\Mailer\Transport::fromDsn(format_dsn($config));
$mailer = new Symfony\Component\Mailer\Mailer($transport);

try {
  $src = ZBateson\MailMimeParser\Message::from(STDIN, false);
} catch (Throwable $e) {
  printf("Failed parsing input: %s\n", $e->getMessage());
  exit(1);
}

try {
  $msg = new Symfony\Component\Mime\Email();
  $msg->from($config['mail_from']);

  foreach (parse_cli_args($_SERVER['argv']) as $to) {
    $msg->addTo($to);
  }

  convert_message($src, $msg);
} catch (Throwable $e) {
  printf("Failed processing message: %s\n", $e->getMessage());
  exit(1);
}

try {
  $mailer->send($msg);
} catch (Throwable $e) {
  printf("Failed sending message: %s\n", $e->getMessage());
  exit(1);
}
