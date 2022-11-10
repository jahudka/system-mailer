#!/usr/bin/env bash

set -eu

install_dir="${1:-/opt/sysmail}"

if [[ -e "${install_dir}" ]]; then
	echo "Destination already exists, aborting."
	exit 1
fi

tmp="/tmp/sysmail-install-$( date +%s )"

if ! mkdir -m 0750 "${tmp}"; then
	echo "Cannot create temporary installation directory '${tmp}'"
	exit 1
fi

umask 027

cd "${tmp}"

echo "Downloading latest SystemMailer source..."
wget -O sysmail.zip "https://github.com/jahudka/system-mailer/archive/main.zip"

echo "Unpacking..."
unzip -oq sysmail.zip

cd system-mailer-main

echo "Installing dependencies..."
composer install --no-ansi --no-interaction --no-cache

echo "Moving installation into place..."
cd /
mv -fT "${tmp}/system-mailer-main" "${install_dir}"

echo "Cleaning up..."
rm -rf "${tmp}"

echo "All finished!"
echo "Please don't forget to edit the config file '${install_dir}/etc/config.yaml'!"
