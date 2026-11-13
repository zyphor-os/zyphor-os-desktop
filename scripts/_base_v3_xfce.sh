#!/bin/sh

# download: https://raw.githubusercontent.com/zyphor-os/zyphor-os-desktop/main/scripts/_base_v3_xfce.sh

apt install wget

wget https://github.com/zyphor-os/zyphor-os.github.io/raw/refs/heads/main/bethany-lts/bethany/pool/main/z/zyphor-archive-keyring.deb

wget https://github.com/zyphor-os/zyphor-os.github.io/raw/refs/heads/main/bethany-lts/bethany/pool/main/z/zyphor-repo-config.deb

apt install ./zyphor-archive-keyring.deb -y

apt install ./zyphor-repo-config.deb -y

# apps

apt install zyphor-cli -y

apt install zyphor-plymouth -y 

apt install zyphor-fastfetch-config -y

apt install zyphor-face-icon -y

apt install zyphor-desktop-environment -y

apt install zyphor-bashrc-config -y

apt install zycamera-launcher -y

# new preinstalled

apt install vim -y

rm -rf ./zyphor-archive-keyring.deb

rm -rf ./zyphor-repo-config.deb

zy system upgrade

zy system clean

# cleaning OS cache

#!/bin/bash
set -e

echo "Cleaning logs..."
find /var/log -type f -exec truncate -s 0 {} \;
rm -rf /var/log/journal/*

echo "Cleaning APT cache..."
apt clean
apt autoclean
rm -rf /var/lib/apt/lists/*

echo "Cleaning temporary files..."
rm -rf /tmp/*
rm -rf /var/tmp/*
rm -rf /root/.cache/*

echo "Cleaning shell history..."
rm -f /root/.bash_history
rm -f /root/.zsh_history
history -c 2>/dev/null || true

echo "Cleaning machine identity..."
truncate -s 0 /etc/machine-id
rm -f /var/lib/dbus/machine-id

echo "Stopping services and processes..."

# Stop services gracefully when systemd is available.
if command -v systemctl >/dev/null 2>&1; then
    systemctl stop cron.service 2>/dev/null || true
    systemctl stop rsyslog.service 2>/dev/null || true
fi

# This script should run inside the target chroot.
# Do not use pkill -9 -x bash or kill every host process.
# The build host and its shell must remain running.

echo "Cleanup complete."

rm -rf ./_base_v3_xfce.sh