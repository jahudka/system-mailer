# SystemMailer

> This package is deprecated, because msmtp exists. Google it.

This is an extremely simplified `sendmail`-like utility to send e-mails
via SMTP from a system which doesn't have `sendmail`. It's written in PHP 8.

## Installation

```shell
curl -sSfL https://github.com/jahudka/system-mailer/raw/main/install.sh | bash -s <install dir>
```

Default installation directory is `/opt/sysmail`. The executable is located
at `<install dir>/bin/sendmail`; you can symlink it wherever you like.
