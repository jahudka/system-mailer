<?php

declare(strict_types=1);


function format_dsn(array $config) : string {
  return sprintf(
    'smtp%s://%s:%s@%s:%s',
    @$config['smtp_secure'] === false ? '' : 's',
    rawurlencode($config['smtp_user']),
    rawurlencode($config['smtp_password']),
    rawurlencode($config['smtp_host']),
    rawurlencode((string) $config['smtp_port']),
  );
}


function parse_cli_args(array $argv) : array {
  for ($i = 1, $n = count($argv); $i < $n; ++$i) {
    if (preg_match('~^-[-BCDdFfhLNOopqQRrVX](.?)~', $argv[$i], $m)) {
      if (!$m[1]) {
        ++$i;
      }

      if ($argv[$i] === '--') {
        break;
      }
    } else if ($argv[$i][0] !== '-') {
      break;
    }
  }

  return array_slice($argv, $i);
}


function convert_message(ZBateson\MailMimeParser\Message $src, Symfony\Component\Mime\Email $msg) : void {
  $to = $src->getHeader('To');

  if ($to instanceof ZBateson\MailMimeParser\Header\AddressHeader) {
    foreach ($to->getAddresses() as $to) {
      $msg->addTo($to->getEmail());
    }
  }

  if ($subject = $src->getHeader('Subject')) {
    $msg->subject($subject->getValue());
  }

  if (($text = $src->getContent()) !== null) {
    $msg->text($text);
  }

  if (($html = $src->getHtmlContent()) !== null) {
    $msg->html($html);
  }

  foreach ($src->getAllAttachmentParts() as $attachment) {
    $msg->attach($attachment->getContent(), $attachment->getFilename(), $attachment->getContentType());
  }
}
