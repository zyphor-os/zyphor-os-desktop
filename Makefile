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
	@mountpoint -q extract/dev/pts && sudo umount extract/dev/pts || true
	@mountpoint -q extract/dev     && sudo umount extract/dev     || true
	@mountpoint -q extract/proc    && sudo umount extract/proc    || true
	@mountpoint -q extract/sys     && sudo umount extract/sys     || true
	@mountpoint -q extract/run     && sudo umount extract/run     || true
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

	git add Makefile
	git commit -m "build: update repository automation"

	git add pkg/v2/zyphor-grub-theme/DEBIAN/control
	git commit -m "chore: update zyphor grub theme package control"

	git add pkg/v2/zyphor-grub-theme/DEBIAN/postinst
	git commit -m "chore: update zyphor grub theme postinst"

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

# 	zyphor build package pkg/v2/updater/updates
# 	mv pkg/v2/updater/updates.deb zyphor-updates.deb

# 	zyphor build package pkg/v2/updater/zor
# 	mv pkg/v2/updater/zor.deb zyphor-os-release.deb

# 	zyphor build package pkg/v2/updater/zwn
# 	mv pkg/v2/updater/zwn.deb zyphor-whats-new.deb

# 	zyphor build package pkg/v2/zywin/zywin
# 	mv pkg/v2/zywin/zywin.deb zywin.deb

# 	zyphor build package pkg/v2/zywin-ui/zywin-ui
# 	mv pkg/v2/zywin-ui/zywin-ui.deb zywin-ui.deb

# 	zyphor build package pkg/v2/zywelcome/zywelcome
# 	mv pkg/v2/zywelcome/zywelcome.deb zywelcome.deb

# 	zyphor build package pkg/v2/zylearn/zylearn
# 	mv pkg/v2/zylearn/zylearn.deb zylearn.deb

# 	zyphor build package pkg/v2/updater/updates
# 	mv pkg/v2/updater/updates.deb zyphor-updates.deb

# 	zyphor build package pkg/v2/zcli
# 	mv pkg/v2/zcli.deb zyphor-cli.deb

# 	zyphor build package pkg/v2/zrc
# 	mv pkg/v2/zrc.deb zyphor-repo-config.deb

# 	zyphor build package pkg/v2/zyphor-archive-keyring
# 	mv pkg/v2/zyphor-archive-keyring.deb zyphor-archive-keyring.deb

# 	zyphor build package pkg/v2/zysh
# 	mv pkg/v2/zysh.deb zysh.deb

# 	zyphor build package pkg/v2/zcc-web
# 	mv pkg/v2/zcc-web.deb zyphor-command-center-web.deb
		
# 	zyphor build package pkg/v2/zycamera-launcher/zycamera-launcher
# 	mv pkg/v2/zycamera-launcher/zycamera-launcher.deb zycamera-launcher.deb

# 	zyphor build package pkg/v2/zou/grub-screensaver-1
# 	mv pkg/v2/zou/grub-screensaver-1.deb grub-screensaver-1.deb

	zyphor build package pkg/v2/zyphor-grub-theme
	mv pkg/v2/zyphor-grub-theme.deb zyphor-grub-theme.deb

# 	---

	mv ./*.deb ../zyphor-os.github.io/ada-lovelace-lts/pool/main/z --verbose

# 	mv ./*.deb pkg/staging --verbose

# BUILD AUTOMATION

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
