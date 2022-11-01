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


function parse_cli_args(array $argv, ?string $defaultRecipient = null) : iterable {
  for ($i = 1, $n = count($argv); $i < $n; ++$i) {
    if (preg_match('~^-(-|[BCDdFfhLNOopqQRrVX]|q[pG])$~', $argv[$i], $m)) {
      ++$i;

      if ($m[1] === '-') {
        break;
      }
    } else if ($argv[$i][0] !== '-') {
      break;
    }
  }

  foreach (array_slice($argv, $i) as $arg) {
    if (filter_var($arg, FILTER_VALIDATE_EMAIL)) {
      yield $arg;
    } else if ($defaultRecipient) {
      if (str_starts_with($defaultRecipient, '@')) {
        yield $arg . $defaultRecipient;
      } else if (str_contains($defaultRecipient, '%s')) {
        yield sprintf($defaultRecipient, $arg);
      } else {
        yield sprintf('%s <%s>', $arg, $defaultRecipient);
      }
    }
  }
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
