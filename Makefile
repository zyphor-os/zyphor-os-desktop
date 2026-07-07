CC = gcc

# DEV AUTOMATION

# start...

deldev:
	@if mountpoint -q mount; then \
		echo "Unmounting mount..."; \
		sudo umount mount; \
	else \
		echo "mount is not mounted. Skipping unmount."; \
	fi
	@echo "Removing mount directory..."
	
	sudo rm -rf mount --verbose
	sudo rm -rf iso --verbose
	sudo rm -rf extract --verbose
	sudo rm -rf new-iso --verbose
	sudo rm -rf initrd --verbose
	sudo rm -rf initrd-gtk --verbose

restoredev:
	mkdir -p mount
	mkdir -p iso
	mkdir -p extract
	mkdir -p new-iso
	mkdir -p initrd
	mkdir -p initrd-gtk

	touch mount/README
	touch iso/README
	touch extract/README
	touch new-iso/README
	touch initrd/README
	touch initrd-gtk/README

	echo "iso mounting point" > mount/README
	echo "writable iso dir" > iso/README
	echo "extracted iso writable (workspace)" > extract/README
	echo "new iso dir." > new-iso/README
	echo "initial ram disk for installer" > initrd/README
	echo "initial ram disk for installer gtk" > initrd-gtk/README

vmcreate:
	rm -rf new-iso/*.qcow2 --verbose
# 	rm -rf new-iso/*.iso --verbose
	sudo qemu-img create -f qcow2 new-iso/zyphor.qcow2 15G

install-cdrom:
	sudo qemu-system-x86_64 --enable-kvm --cdrom new-iso/*.iso -m 4028 --hda new-iso/zyphor.qcow2 --boot d

run-hdd:
	sudo qemu-system-x86_64 --enable-kvm -m 4028 --hda new-iso/zyphor.qcow2 --boot c

# end...

status:
	git status

add:
	git add pkg/v2/zylearn/zylearn/DEBIAN/control
	git commit -m "chore: updated v2 zylearn control"

	git add pkg/v2/zylearn/zylearn/DEBIAN/postinst
	git commit -m "chore: added v2 zylearn post-install script"

	git add pkg/v2/zysh/DEBIAN/control
	git commit -m "chore: updated v2 zysh control"

	git add pkg/v2/zysh/DEBIAN/postinst
	git commit -m "chore: updated v2 zysh post-install script"
	
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

# 	zyphor build package pkg/v2/updater/zor
# 	mv pkg/v2/updater/zor.deb zyphor-os-release.deb

# 	zyphor build package pkg/v2/updater/zwn
# 	mv pkg/v2/updater/zwn.deb zyphor-whats-new.deb

# 	zyphor build package pkg/updater/updates
# 	mv pkg/updater/updates.deb zyphor-updates.deb

	zyphor build package pkg/v2/zylearn/zylearn
	mv pkg/v2/zylearn/zylearn.deb zylearn.deb

	zyphor build package pkg/v2/zysh
	mv pkg/v2/zysh.deb zysh.deb

# 	---

	mv ./*.deb ../zyphor-os.github.io/v2-ada-lovelace/pool/main/z --verbose

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