#!/bin/sh

# download: https://raw.githubusercontent.com/zyphor-os/zyphor-os-desktop/main/scripts/_base_v3_gnome.sh

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

# apt install zyphor-desktop-environment -y

apt install zyphor-bashrc-config -y

apt install zycamera-launcher -y

rm -rf ./zyphor-archive-keyring.deb

rm -rf ./zyphor-repo-config.deb

zy system upgrade

zy system clean

rm -rf ./_base_v3_gnome.sh