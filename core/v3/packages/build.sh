#!/bin/sh
# Build the packages under packages/ and put the .debs in OUT (default:
# packages/.build/debs/). tools/build-iso.sh passes the edition's
# build/DESKTOP/config/packages.chroot/, where live-build installs them from.
#
# Usage: packages/build.sh [OUT [SKIP]]
#   SKIP  space-separated package names to leave out. live-build installs
#         EVERY .deb in packages.chroot, so an edition must not get the
#         packages of other editions (a KDE ISO would also boot GNOME).
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="${1:-$ROOT/packages/.build/debs}"
SKIP=" ${2:-} "
WORK="$ROOT/packages/.build/work"

rm -rf "$WORK"
mkdir -p "$WORK" "$OUT"

for src in "$ROOT"/packages/*/debian; do
    pkg="$(basename "$(dirname "$src")")"
    case "$SKIP" in
        *" $pkg "*) echo "==> Skipping $pkg (not part of this edition)"; continue ;;
    esac
    echo "==> Building $pkg"
    # Build from a copy so build artifacts stay out of the source tree.
    cp -r "$ROOT/packages/$pkg" "$WORK/$pkg"
    (cd "$WORK/$pkg" && dpkg-buildpackage -us -uc -b)
done

# Two packages with the same file fail only deep inside lb build (dpkg:
# "trying to overwrite"). Catch it here.
overlap="$(for deb in "$WORK"/*.deb; do
    dpkg-deb -c "$deb" | awk -v p="$(basename "$deb")" '$1 !~ /^d/ { sub(/^\.\//, "", $6); print $6 "\t" p }'
done | sort | awk -F'\t' '$1 == last { print $1 ": " lastp ", " $2 } { last = $1; lastp = $2 }')"
if [ -n "$overlap" ]; then
    echo "Files in more than one package:" >&2
    echo "$overlap" >&2
    exit 1
fi

rm -f "$OUT"/*.deb
cp "$WORK"/*.deb "$OUT"/
echo "==> Built into $OUT:"
ls -1 "$OUT"
