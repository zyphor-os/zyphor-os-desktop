
#!/bin/sh

set -e

# Detect kernel version
KERNEL_FILE=$(find extract/boot -maxdepth 1 -name 'vmlinuz-*' -printf '%f\n' | sort -V | tail -n 1)
KERNEL_VERSION="${KERNEL_FILE#vmlinuz-}"

echo "Detected kernel: $KERNEL_VERSION"

# Copy GRUB and ISOLINUX configurations
cp core/v2/_iso_custom/grub.cfg iso/boot/grub/grub.cfg
cp core/v2/_iso_custom/live.cfg iso/isolinux/live.cfg
cp core/v2/_iso_custom/splash-small.png iso/isolinux/splash.png
cp core/v2/_iso_custom/splash.png iso/boot/grub/

# Remove old kernel files
rm -f iso/live/initrd.img iso/live/vmlinuz
rm -f iso/live/initrd.img-* iso/live/vmlinuz-*

# Copy kernel and initrd
cp "extract/boot/initrd.img-$KERNEL_VERSION" iso/live/initrd.img
cp "extract/boot/vmlinuz-$KERNEL_VERSION" iso/live/vmlinuz

# Copy versioned kernel and initrd
cp "extract/boot/initrd.img-$KERNEL_VERSION" "iso/live/initrd.img-$KERNEL_VERSION"
cp "extract/boot/vmlinuz-$KERNEL_VERSION" "iso/live/vmlinuz-$KERNEL_VERSION"

echo "Done: $KERNEL_VERSION"
