CC = gcc

# DEV AUTOMATION

status:
	git status

add:
	git add README.md
	git commit -m "docs: updated README"

	git add showcase/image1.jpg
	git commit -m "docs: updated showcase image1"

	git add showcase/image2.jpg
	git commit -m "docs: updated showcase image2"

	git add showcase/image3.jpg
	git commit -m "docs: updated showcase image3"

	git add showcase/image4.jpg
	git commit -m "docs: updated showcase image4"

	git add showcase/image5.jpg
	git commit -m "docs: updated showcase image5"

	git add showcase/image6.jpg
	git commit -m "docs: updated showcase image6"

	git add showcase/image7.jpg
	git commit -m "docs: updated showcase image7"

	git add showcase/image8.jpg
	git commit -m "docs: updated showcase image8"

	git add showcase/image9.jpg
	git commit -m "docs: updated showcase image9"

	git add showcase/image10.jpg
	git commit -m "docs: added showcase image10"

	git add showcase/image11.jpg
	git commit -m "docs: added showcase image11"
	
	git add Makefile
	git commit -m "build: update repository automation"

push:
	git push origin $(branch)

pull:
	git pull origin $(branch)

merge:
	git merge $(branch)

switch:
	git checkout $(branch)

# RELEASE AUTOMATION

release:

# 	zyphor build package pkg/updater/zor
# 	mv pkg/updater/zor.deb zyphor-os-release.deb

# 	zyphor build package pkg/updater/zwn
# 	mv pkg/updater/zwn.deb zyphor-whats-new.deb

# 	zyphor build package pkg/updater/updates
# 	mv pkg/updater/updates.deb zyphor-updates.deb

	zyphor build package pkg/zou/grub-screensaver-1
	mv pkg/zou/grub-screensaver-1.deb grub-screensaver-1.deb

# 	---

	mv ./*.deb ../zyphor-os.github.io/pool/main/z --verbose

# BUILD AUTOMATION

copy-default-assets:
	cp -r assets-xfce4/default/fastfetch os-config/variant-xfce/includes.chroot/etc/skel/.config/

	cp -r assets-xfce4/default/banner-installer/logo_debian.png os-config/common/includes.installer/usr/share/graphics/logo_debian.png
	
	cp -r assets-xfce4/default/default-wallpapers/backgrounds.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds.png
	cp -r assets-xfce4/default/default-wallpapers/backgrounds2.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds2.png
	cp -r assets-xfce4/default/default-wallpapers/backgrounds3.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds3.png
	cp -r assets-xfce4/default/default-wallpapers/backgrounds4.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds4.png

	cp -r assets-xfce4/default/default-grub-bootloader-screensaver/grub-bootloader-screen-saver.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/zyphor-grub-bootloader-screensavers/grub-bootloader-screen-saver.png
	cp -r assets-xfce4/default/default-grub-bootloader-screensaver/zyphor-full.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/zyphor-grub-bootloader-screensavers/zyphor-full.png

	cp -r assets-xfce4/default/face-icon/face.png os-config/variant-xfce/includes.chroot/etc/skel/.face

	cp -r assets-xfce4/default/issue os-config/variant-xfce/includes.chroot/etc/issue

	cp -r assets-xfce4/default/os-release os-config/variant-xfce/includes.chroot/etc/os-release

	cp -r assets-xfce4/default/grub.cfg os-config/common/bootloaders/grub-pc/grub.cfg

	cp -r assets-xfce4/default/grub os-config/variant-xfce/includes.chroot/etc/default/grub

	cp -r assets-xfce4/default/menu.cfg os-config/common/bootloaders/syslinux_common/menu.cfg

	cp -r assets-xfce4/default/kali-finish-install os-config/common/includes.installer/kali-finish-install

	cp -r assets-xfce4/default/preseed.cfg os-config/common/includes.installer/preseed.cfg

	echo "\nAssets Copied!\n"

copy-horizon-assets:
	cp -r assets-xfce4/horizon/fastfetch os-config/variant-xfce/includes.chroot/etc/skel/.config/

	cp -r assets-xfce4/horizon/banner-installer/logo_debian.png os-config/common/includes.installer/usr/share/graphics/logo_debian.png
	
	cp -r assets-xfce4/horizon/default-wallpapers/backgrounds.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds.png
	cp -r assets-xfce4/horizon/default-wallpapers/backgrounds2.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds2.png
	cp -r assets-xfce4/horizon/default-wallpapers/backgrounds3.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds3.png
	cp -r assets-xfce4/horizon/default-wallpapers/backgrounds4.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds4.png
	cp -r assets-xfce4/horizon/default-wallpapers/backgrounds5.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/backgrounds5.png

	cp -r assets-xfce4/horizon/default-grub-bootloader-screensaver/grub-bootloader-screen-saver.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/zyphor-grub-bootloader-screensavers/grub-bootloader-screen-saver.png
	cp -r assets-xfce4/horizon/default-grub-bootloader-screensaver/zyphor-full.png os-config/variant-xfce/includes.chroot/usr/share/backgrounds/zyphor-grub-bootloader-screensavers/zyphor-full.png

	cp -r assets-xfce4/horizon/face-icon/face.png os-config/variant-xfce/includes.chroot/etc/skel/.face

	cp -r assets-xfce4/horizon/issue os-config/variant-xfce/includes.chroot/etc/issue

	cp -r assets-xfce4/horizon/os-release os-config/variant-xfce/includes.chroot/etc/os-release

	cp -r assets-xfce4/horizon/grub.cfg os-config/common/bootloaders/grub-pc/grub.cfg

	cp -r assets-xfce4/horizon/grub os-config/variant-xfce/includes.chroot/etc/default/grub

	cp -r assets-xfce4/horizon/menu.cfg os-config/common/bootloaders/syslinux_common/menu.cfg

	cp -r assets-xfce4/horizon/kali-finish-install os-config/common/includes.installer/kali-finish-install

	cp -r assets-xfce4/horizon/preseed.cfg os-config/common/includes.installer/preseed.cfg

	echo "\nAssets Copied!\n"

build:
	./build.sh --variant xfce --verbose

# BOOT AUTOMATION

vmInit:
	$(CC) vmInit.c \
	 playground/helpers/helperInput.c \
	 playground/helpers/helperString.c \
	 -o vmInit

bootHardDisk:
	$(CC) bootHardDisk.c \
	 playground/helpers/helperInput.c \
	 playground/helpers/helperString.c \
	 -o bootHardDisk

bootCDROM:
	$(CC) bootCDROM.c \
	 playground/helpers/helperInput.c \
	 playground/helpers/helperString.c \
	 -o bootCDROM

clean:
	rm -f vmInit bootHardDisk bootCDROM