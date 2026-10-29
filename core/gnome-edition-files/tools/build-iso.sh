#!/bin/bash
# Description: Build one edition's live ISO from scratch with live-build.
# Author: Mark Jason Espelita <markjasonespelita@gmail.com>
# Date: 2026-10-01
# Assembles build/DESKTOP/ from the common config and the edition's, builds the
# packages, then runs lb config and lb build.
# Output: out/DISTRO_ID-VERSION-DESKTOP-amd64.iso, out/SHA256SUMS and
# build/DESKTOP/build.log.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

editions() { for d in desktops/*/desktop.conf; do basename "$(dirname "$d")"; done; }

usage() {
    cat <<EOF
Usage: tools/build-iso.sh (--desktop NAME | --NAME) [--purge | --config-only]

  --desktop NAME  Edition to build: $(editions | tr '\n' ' ')
  --NAME          Shorthand for --desktop NAME, e.g. --xfce or --gnome.
  --purge         Also delete that edition's download cache (slower, fully
                  fresh). Use for releases or when builds behave strangely.
  --config-only   Only assemble build/NAME and run lb config (no root), to
                  inspect the configuration an edition would be built with.
  -h, --help      Show this help.

Runs as a normal user and asks for sudo where live-build needs root.
When run as root (e.g. in CI), sudo is not used.
EOF
}

DESKTOP=""
PURGE=""
CONFIG_ONLY=0

# One edition per build: --gnome --xfce is a mistake, not "the last one wins".
set_desktop() {
    if [ -n "$DESKTOP" ] && [ "${DESKTOP,,}" != "${1,,}" ]; then
        echo "Only one edition per build (got '$DESKTOP' and '$1')." >&2
        exit 2
    fi
    DESKTOP="$1"
}

while [ $# -gt 0 ]; do
    case "$1" in
        --desktop) set_desktop "${2:?--desktop needs a name}"; shift ;;
        --desktop=*) set_desktop "${1#--desktop=}" ;;
        --purge) PURGE="--purge" ;;
        --config-only) CONFIG_ONLY=1 ;;
        -h|--help) usage; exit 0 ;;
        # --gnome, --xfce, --minimal, ...: any --NAME with a desktops/NAME/
        # edition. A new edition gets its flag by existing; nothing to add here.
        --?*)
            edition="${1#--}"
            if [ -f "desktops/${edition,,}/desktop.conf" ]; then
                set_desktop "$edition"
            else
                echo "Unknown option: $1" >&2; usage >&2; exit 2
            fi ;;
        *) echo "Unknown option: $1" >&2; usage >&2; exit 2 ;;
    esac
    shift
done

die() { echo "$*" >&2; exit 2; }

[ -n "$DESKTOP" ] || { usage >&2; exit 2; }
DESKTOP="${DESKTOP,,}"   # editions are lower case: --desktop XFCE works too
DESKTOP_CONF="desktops/$DESKTOP/desktop.conf"
[ -f "$DESKTOP_CONF" ] || die "No edition '$DESKTOP'. Editions: $(editions | tr '\n' ' ')"

# DISTRO_ID, SUITE, MIRROR, ARCHIVE_AREAS, LIVE_USER, LIVE_HOSTNAME
. ./distro.conf
RELEASE="packages/$DISTRO_ID-branding/usr/lib/$DISTRO_ID-release"
[ -f "$RELEASE" ] || die "Missing $RELEASE (DISTRO_ID=$DISTRO_ID in distro.conf)."
distro_id="$DISTRO_ID"
. "./$RELEASE"   # DISTRO_ID, DISTRO_NAME, DISTRO_VERSION
[ "$DISTRO_ID" = "$distro_id" ] || die "DISTRO_ID differs: $distro_id in distro.conf, $DISTRO_ID in $RELEASE."

META="" REQUIRED="" DESKTOP_NAME="" INSTALLER=no
. "./$DESKTOP_CONF"   # DESKTOP_NAME, META, INSTALLER, BOOT_OPTIONS, REQUIRED
[ -n "$META" ] || die "$DESKTOP_CONF sets no META."
META_PACKAGE="$DISTRO_ID-$META"
[ -f "packages/$META_PACKAGE/debian/control" ] || die "$DESKTOP_CONF: no package packages/$META_PACKAGE."
case "$INSTALLER" in
    yes) OVERLAYS="installer/config desktops/$DESKTOP/config"
         REQUIRED="$REQUIRED calamares $DISTRO_ID-installer" ;;
    no) OVERLAYS="desktops/$DESKTOP/config" ;;
    *) die "$DESKTOP_CONF: INSTALLER must be yes or no." ;;
esac

# ISO 9660 volume labels have at most 32 characters.
VOLUME="$DISTRO_NAME $DISTRO_VERSION $DESKTOP_NAME"
[ ${#VOLUME} -le 32 ] || die "ISO label '$VOLUME' is longer than 32 characters; shorten DESKTOP_NAME or DISTRO_NAME."

# live-build runs only executable hooks, silently skipping the others.
for hook in config/hooks/normal/*.hook.* installer/config/hooks/normal/*.hook.* \
    desktops/"$DESKTOP"/config/hooks/normal/*.hook.*; do
    [ -e "$hook" ] || continue
    [ -x "$hook" ] || die "Hook $hook is not executable (chmod +x)."
done

missing=()
command -v lb >/dev/null || missing+=(live-build)
command -v dpkg-buildpackage >/dev/null || missing+=(dpkg-dev)
command -v dh >/dev/null || missing+=(debhelper)
command -v xorriso >/dev/null || missing+=(xorriso)
command -v wget >/dev/null || missing+=(wget)
if [ ${#missing[@]} -gt 0 ]; then
    die "Missing tools. Install with: sudo apt install ${missing[*]}"
fi

if [ "$(id -u)" -eq 0 ] || [ "$CONFIG_ONLY" -eq 1 ]; then
    SUDO=()
else
    SUDO=(sudo)
fi

step() { printf '\n\033[1m==> %s\033[0m\n' "$*"; }

# Ask for the sudo password now, not 20 minutes in.
if [ ${#SUDO[@]} -gt 0 ]; then
    sudo -v
fi

BUILD="build/$DESKTOP"
ISO_NAME="$DISTRO_ID-$DISTRO_VERSION-$DESKTOP-amd64.iso"
start=$(date +%s)

step "Cleaning the previous $DESKTOP build ${PURGE:+(including cache)}"
if [ -x "$BUILD/auto/clean" ] && [ "$CONFIG_ONLY" -eq 0 ]; then
    (cd "$BUILD" && "${SUDO[@]}" lb clean $PURGE)
fi
# Everything but chroot/, binary/ and cache/ is assembled anew below; lb build
# can leave root-owned files in config/.
"${SUDO[@]}" rm -rf -- "$BUILD/auto" "$BUILD/config" "$BUILD"/*.hybrid.iso \
    || die "Can't remove the old $BUILD/config (owned by root?). Build without --config-only once."
mkdir -p "$BUILD" out

# auto/clean removed the old build.log; log everything from here on.
exec > >(tee "$BUILD/build.log") 2>&1

step "Assembling $BUILD: config/ + ${OVERLAYS// / + }"
cp -r auto config "$BUILD/"
for overlay in $OVERLAYS; do
    if [ -d "$overlay" ]; then
        cp -r "$overlay/." "$BUILD/config/"
    fi
done
cp distro.conf "$BUILD/distro.conf"
cp "$RELEASE" "$BUILD/release"
{ echo "DESKTOP=$DESKTOP"; cat "$DESKTOP_CONF"; } > "$BUILD/desktop.conf"
{
    echo "# Generated by tools/build-iso.sh from $DESKTOP_CONF (META)."
    echo "$META_PACKAGE"
} > "$BUILD/config/package-lists/edition.list.chroot"

# Hooks and package lists can't read the build settings, so fill them in.
while IFS= read -r -d '' file; do
    sed -i -e "s|@DISTRO_ID@|$DISTRO_ID|g" -e "s|@META_PACKAGE@|$META_PACKAGE|g" \
        -e "s|@REQUIRED@|$REQUIRED|g" "$file"
done < <(find "$BUILD/config/hooks" "$BUILD/config/package-lists" -type f -print0)
if leftover="$(grep -rlE '@[A-Z_]+@' "$BUILD/config/hooks" "$BUILD/config/package-lists")"; then
    die "Unknown @WORD@ left in: $leftover"
fi

step "Building $DISTRO_NAME $DISTRO_VERSION packages"
sh packages/build.sh "$ROOT/$BUILD/config/packages.chroot"

step "Generating live-build configuration"
(cd "$BUILD" && lb config)

if [ "$CONFIG_ONLY" -eq 1 ]; then
    step "Configuration ready in $BUILD (--config-only: not building)"
    exit 0
fi

step "Building the $DESKTOP_NAME ISO"
(cd "$BUILD" && "${SUDO[@]}" lb build)

built="$(ls -1t -- "$BUILD"/*.hybrid.iso 2>/dev/null | head -n1 || true)"
if [ -z "$built" ]; then
    echo "lb build finished but no *.hybrid.iso was produced. See $BUILD/build.log." >&2
    exit 1
fi

step "Naming ISO out/$ISO_NAME"
# Earlier versions of this edition.
rm -f -- out/"$DISTRO_ID"-*-"$DESKTOP"-amd64.iso
"${SUDO[@]}" mv -- "$built" "out/$ISO_NAME"
"${SUDO[@]}" chown "$(id -u):$(id -g)" "out/$ISO_NAME"

step "Writing checksums"
(cd out && sha256sum -- *.iso > SHA256SUMS && cat SHA256SUMS)

elapsed=$(( $(date +%s) - start ))
step "Done in $((elapsed / 60))m $((elapsed % 60))s"
ls -lh -- "out/$ISO_NAME"
