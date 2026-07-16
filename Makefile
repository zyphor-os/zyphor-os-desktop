CC = gcc

# DEV AUTOMATION

# start...

deldev:
	@echo "\n--- CLEANING DEVELOPMENT ENVIRONMENT ---\n"

	@for dir in \
		extract/dev/pts \
		extract/dev \
		extract/proc \
		extract/sys \
		extract/run \
		mount; do \
		if mountpoint -q $$dir; then \
			echo "Unmounting $$dir..."; \
			sudo umount $$dir || exit 1; \
		else \
			echo "$$dir is not mounted. Skipping."; \
		fi; \
	done

	@echo "Removing directories..."

	sudo rm -rf mount iso extract new-iso initrd initrd-gtk --verbose

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

devinit:
	@echo "\n--- INITIALIZING DEV ENVIRONMENT ---\n";
	make deldev
	make restoredev
	sudo rm -rf mount/README
	sudo rm -rf iso/README
	sudo rm -rf extract/README
	sudo rm -rf new-iso/README
	sudo rm -rf initrd/README
	sudo rm -rf initrd-gtk/README
	@echo "\n--- MOUNTING ISO FILE READ ONLY MODE ---\n";
	sudo mount -o loop $(iso) mount
	@echo "\n--- Copy ISO contents to writable directory ---\n";
	rsync -aH mount/ iso/ --verbose
	@echo "\n--- Extract squashfs filesystem ---\n";
	sudo unsquashfs -d extract mount/live/filesystem.squashfs
	@echo "\n--- EXTRACTING INSTALLER ---\n";
	@(cd initrd && gzip -dc ../iso/install/initrd.gz | cpio -idmv)
	@(cd initrd-gtk && gzip -dc ../iso/install/gtk/initrd.gz | cpio -idmv)
	@echo "\n--- MOUNTING CHROOT ENVIRONMENT ---\n";
	sudo mount --bind /dev extract/dev
	sudo mount --bind /proc extract/proc
	sudo mount --bind /sys extract/sys
	sudo mount --bind /run extract/run
	sudo mount -t devpts devpts extract/dev/pts
	@echo "\n--- FIX DNS ---\n";
	sudo cp /etc/resolv.conf extract/etc/resolv.conf
	@echo "\n--- ENTER CHROOT ---\n";
	sudo chroot extract /bin/zsh

devbuild:
	@echo "\n--- UNMOUNTING ISO FILE READ ONLY MODE ---\n";
	sudo umount extract/dev/pts
	sudo umount extract/dev
	sudo umount extract/proc
	sudo umount extract/sys
	sudo umount extract/run
	@echo "\n--- REMOVING OLD SQUASHFS FILE ---\n";
	sudo rm -rf iso/live/filesystem.squashfs --verbose
	@echo "\n--- REPACKAGING THE INITRD INSTALLERS ---\n";
	@(cd initrd && find . | cpio --create --format=newc | gzip -9 | sudo tee ../iso/install/initrd.gz > /dev/null)
	@(cd initrd-gtk && find . | cpio --create --format=newc | gzip -9 | sudo tee ../iso/install/gtk/initrd.gz > /dev/null)
	@echo "\n--- REBUILDING NEW SQUASHFS FILE ---\n";
	sudo mksquashfs extract iso/live/filesystem.squashfs -comp xz
	@echo "\n--- REBUILDING NEW ISO FILE ---\n";
	@(cd iso && \
	sudo xorriso -as mkisofs \
	  -iso-level 3 \
	  -full-iso9660-filenames \
	  -volid "Kali Live" \
	  -o ../new-iso/zyphor-custom.iso \
	  -eltorito-boot isolinux/isolinux.bin \
	    -eltorito-catalog isolinux/boot.cat \
	    -no-emul-boot \
	    -boot-load-size 4 \
	    -boot-info-table \
	  -isohybrid-mbr /usr/lib/ISOLINUX/isohdpfx.bin \
	  -eltorito-alt-boot \
	    -e boot/grub/efi.img \
	    -no-emul-boot \
	  -isohybrid-gpt-basdat \
	  .)

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
	git add pkg/v2/zcc/DEBIAN/control
	git commit -m "chore: updated command center control"

	git add pkg/v2/zcc/usr/share/zyphor-command-center/ui/profile/profile.py
	git commit -m "chore: updated profile module"

	git add pkg/v2/zcc/usr/share/zyphor-command-center/ui/profile/server.py
	git commit -m "chore: updated profile server"
	
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

	zyphor build package pkg/v2/zcc
	mv pkg/v2/zcc.deb zyphor-command-center.deb

	zyphor build package pkg/v2/zcc-web
	mv pkg/v2/zcc-web.deb zyphor-command-center-web.deb

# 	zyphor build package pkg/updater/updates
# 	mv pkg/updater/updates.deb zyphor-updates.deb

# 	zyphor build package pkg/v2/zcli
# 	mv pkg/v2/zcli.deb zyphor-cli.deb

# 	zyphor build package pkg/v2/zysh
# 	mv pkg/v2/zysh.deb zysh.deb

# 	---

# 	mv ./*.deb ../zyphor-os.github.io/v2-ada-lovelace/pool/main/z --verbose

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