# SystemMailer

This is an extremely simplified `sendmail`-like utility to send e-mails
via SMTP from a system which doesn't have `sendmail`. It's written in PHP 8.

## Usage

```shell
git clone --depth 1 git@github.com:jahudka/system-mailer.git /opt/system-mailer
cd /opt/system-mailer
rm -rf .git
composer install
nano etc/config.yaml
ln -s $(pwd)/bin/sendmail /usr/local/bin/sendmail
```
