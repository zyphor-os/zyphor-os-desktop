#!/usr/bin/env python3
# Description: Headless boot test for one edition's live ISO.
# Author: Mark Jason Espelita <markjasonespelita@gmail.com>
# Date: 2026-10-01
"""Headless boot test for one edition's live ISO.

Boots the ISO in QEMU without a window, logs in on the serial console and runs
a list of checks (checks() below) against the running live system.

The live boot menus wait for a keypress, so the test skips the bootloader:
it extracts the kernel, initrd and kernel command line from the ISO and boots
them directly, adding console=ttyS0. The bootloaders themselves are covered by
the manual tests in tools/test-iso.sh.

Exit codes: 0 all checks passed, 1 a check failed, 2 setup error,
3 the system did not reach a login prompt / login failed.
"""

import argparse
import json
import os
import re
import select
import shlex
import shutil
import socket
import subprocess
import sys
import tempfile
import time

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

LIVE_PASSWORD = "live"  # live-config's default


def read_conf(path):
    """KEY=VALUE lines of a shell-syntax settings file (no expansion)."""
    values = {}
    for line in open(path):
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, _, value = line.partition("=")
        try:
            words = shlex.split(value, comments=True)
        except ValueError:
            continue
        values[key.strip()] = " ".join(words)
    return values


DISTRO = read_conf(os.path.join(ROOT, "distro.conf"))
DISTRO_ID = DISTRO["DISTRO_ID"]
RELEASE = read_conf(os.path.join(ROOT, "packages", f"{DISTRO_ID}-branding", "usr", "lib",
                                 f"{DISTRO_ID}-release"))
LIVE_USER = DISTRO["LIVE_USER"]


# Checks of one edition only, run after its session came up. The serial
# console is logged in as the live user, so it shares the desktop session's
# D-Bus (dbus-user-session) and can ask xfconf, gsettings and so on.
EDITION_CHECKS = {
    "xfce": [
        # /etc/X11/Xsession.d/56DISTRO_ID-xfce put /etc/xdg/DISTRO_ID-xfce first.
        ("XFCE reads the distro's settings (Greybird theme)",
         "xfconf-query -c xsettings -p /Net/ThemeName | grep -qx Greybird", 90),
        ("XFCE panel is the distro's (Whisker menu first)",
         "xfconf-query -c xfce4-panel -p /plugins/plugin-1 | grep -qx whiskermenu", 90),
        # xfdesktop's default wallpaper is diverted to desktop-base's.
        ("Xfce wallpaper is desktop-base's",
         "readlink -f /usr/share/backgrounds/xfce/xfce-x.svg"
         " | grep -q '^/usr/share/desktop-base/.*-theme/wallpaper/'", 0),
        ("network applet running", "pgrep -u $(id -u) -x nm-applet", 60),
        ("password dialog agent running (polkit)",
         "pgrep -u $(id -u) -f polkit-mate-authentication-agent-1", 60),
    ],
    "kde": [
        # Plasma's window manager and the password dialog agent that the
        # installer's pkexec needs (KWin runs as kwin_wayland or kwin_x11).
        ("window manager running (KWin)",
         "pgrep -u $(id -u) -f 'kwin_(wayland|x11)'", 90),
        ("password dialog agent running (polkit)",
         "pgrep -u $(id -u) -f polkit-kde-authentication-agent-1", 90),
    ],
    "gnome": [
        # zyphor-os-settings-gnome's GSettings override (priority 90).
        ("GNOME wallpaper is desktop-base's",
         "gsettings get org.gnome.desktop.background picture-uri"
         " | grep -q '/usr/share/desktop-base/active-theme/wallpaper/'", 90),
        ("GNOME dash has the distro's favorites (Firefox)",
         "gsettings get org.gnome.shell favorite-apps | grep -q firefox-esr.desktop", 90),
    ],
}


def checks(desktop):
    """(description, shell check, seconds to keep retrying) for an edition."""
    conf = read_conf(os.path.join(ROOT, "desktops", desktop, "desktop.conf"))
    meta = f"{DISTRO_ID}-{conf['META']}"
    name = RELEASE["DISTRO_NAME"]
    result = [
        ("hostname is " + DISTRO["LIVE_HOSTNAME"], f'test "$(hostname)" = {DISTRO["LIVE_HOSTNAME"]}', 0),
        (f"os-release names {name}",
         f"grep -qx 'VARIANT_ID={DISTRO_ID}' /etc/os-release"
         f" && grep -q '^PRETTY_NAME=\"{name} ' /etc/os-release", 0),
        (f"{meta} installed", f"dpkg-query -W -f='${{db:Status-Abbrev}}' {meta} | grep -q '^ii'", 0),
        # A Recommends picked by 0050-recommends (every edition has zyphor-os-base).
        ("base Recommends installed", "command -v curl && command -v nano", 0),
        # apt-get check takes dpkg's lock, so it needs root (the live user has sudo).
        ("no broken packages", "sudo -n apt-get check -q </dev/null", 0),
        # 0900-apt-recommends: installed systems install Recommends like Debian.
        ("Recommends on for installed systems",
         "! apt-config dump APT::Install-Recommends | grep -q '\"false\"'", 0),
        ("automatic security updates on",
         "apt-config dump APT::Periodic::Unattended-Upgrade | grep -q '\"1\"'", 0),
        ("NetworkManager connected", "nmcli -t -f STATE general | grep -q '^connected'", 60),
    ]
    if conf.get("INSTALLER") == "yes":
        pool = "/run/live/medium/dists/*/main/binary-amd64/Packages.gz"
        result += [
            # The installer package names the live user (live-config.conf.d).
            (f"live user is \"{name} live user\"",
             f"getent passwd {LIVE_USER} | cut -d: -f5 | grep -q '^{name} live user'", 0),
            # Calamares' password check fails every password without it.
            ("password dictionary for the installer (cracklib)",
             "test -e /var/cache/cracklib/cracklib_dict.pwd", 0),
            ("installer ready (launcher, Calamares branding)",
             f"test -x /usr/bin/{DISTRO_ID}-install"
             f" && test -s /etc/calamares/branding/{DISTRO_ID}/branding.desc", 0),
            # The installer installs GRUB from the ISO's pool, without a network.
            ("bootloader packages in the ISO's pool",
             f"zcat {pool} | grep -qx 'Package: shim-signed'"
             f" && zcat {pool} | grep -qx 'Package: grub-pc'", 0),
        ]
    # Editions with the artwork package: desktop-base shows the distro's theme.
    result.append(("distro's desktop-base theme active (editions with artwork)",
                   f"! dpkg-query -W {DISTRO_ID}-artwork >/dev/null 2>&1"
                   f" || test \"$(readlink -f /usr/share/desktop-base/active-theme)\""
                   f" = /usr/share/desktop-base/{DISTRO_ID}-theme", 0))
    dm = conf.get("DISPLAY_MANAGER")
    if dm:
        result.append((f"{dm} running", f"systemctl is-active -q {dm}", 60))
    processes = conf.get("SESSION_PROCESSES", "").split()
    if processes:
        # Autologin: the live user's session came up without a password.
        for process in processes:
            result.append((f"live user logged in automatically ({process} running)",
                           f"pgrep -u {LIVE_USER} -x {shlex.quote(process)}", 120))
    else:
        result.append(("live user logged in automatically on tty1",
                       f"pgrep -u {LIVE_USER} -t tty1 -x bash", 60))
    return result + EDITION_CHECKS.get(desktop, [])


IN_GITHUB = os.environ.get("GITHUB_ACTIONS") == "true"


class Timeout(Exception):
    pass


class Console:
    """Reads and writes the guest serial console through QEMU's stdio."""

    def __init__(self, proc, log_path, echo):
        self.proc = proc
        self.buf = b""
        self.log = open(log_path, "wb")
        self.echo = echo

    def _read(self, timeout):
        ready, _, _ = select.select([self.proc.stdout], [], [], timeout)
        if not ready:
            return
        data = os.read(self.proc.stdout.fileno(), 65536)
        if not data:
            raise Timeout("QEMU exited")
        self.buf += data
        self.log.write(data)
        self.log.flush()
        if self.echo:
            sys.stdout.buffer.write(data)
            sys.stdout.flush()

    def expect(self, pattern, timeout):
        """Wait for a regex (bytes) in the output. Consumes output up to the match."""
        regex = re.compile(pattern)
        deadline = time.monotonic() + timeout
        while True:
            match = regex.search(self.buf)
            if match:
                self.buf = self.buf[match.end():]
                return match
            remaining = deadline - time.monotonic()
            if remaining <= 0:
                raise Timeout(f"timed out waiting for {pattern!r}")
            if self.proc.poll() is not None:
                raise Timeout("QEMU exited")
            self._read(min(remaining, 1.0))

    def send(self, text):
        self.proc.stdin.write(text.encode())
        self.proc.stdin.flush()

    def run(self, command, timeout=60):
        """Run a shell command in the logged-in session. Returns (exit code, output)."""
        self.buf = b""
        # Quote-split markers so the echoed command line never matches.
        self.send(f'echo "@@BE""GIN@@"; {command}; echo "@@R""C=$?@@"\n')
        self.expect(rb"@@BEGIN@@\r?\n", timeout)
        match = self.expect(rb"(?s)(.*?)@@RC=(\d+)@@", timeout)
        output = match.group(1).decode(errors="replace").replace("\r", "").strip()
        return int(match.group(2)), output


def fail_setup(message):
    print(f"ERROR: {message}", file=sys.stderr)
    sys.exit(2)


def annotate(level, message):
    if IN_GITHUB:
        print(f"::{level}::{message}")


def xorriso(iso, *args):
    result = subprocess.run(
        ["xorriso", "-osirrox", "on", "-indev", iso, *args],
        capture_output=True, text=True,
    )
    return result.returncode, result.stdout


def extract_boot_files(iso, workdir):
    """Return (kernel path, initrd path, kernel command line) from the ISO."""
    entry = None
    for cfg in ("/isolinux/live.cfg", "/boot/grub/grub.cfg"):
        local = os.path.join(workdir, os.path.basename(cfg))
        rc, _ = xorriso(iso, "-extract", cfg, local)
        if rc != 0 or not os.path.exists(local):
            continue
        text = open(local, errors="replace").read()
        if cfg.endswith("live.cfg"):
            # First syslinux entry: linux / initrd / append lines.
            linux = re.search(r"^\s*linux\s+(\S+)", text, re.M)
            initrd = re.search(r"^\s*initrd\s+(\S+)", text, re.M)
            append = re.search(r"^\s*append\s+(.*)$", text, re.M)
            if linux and initrd and append:
                entry = (linux.group(1), initrd.group(1), append.group(1))
        else:
            # First GRUB entry: "linux KERNEL ARGS" and "initrd INITRD".
            linux = re.search(r"^\s*linux\s+(\S+)\s+(.*)$", text, re.M)
            initrd = re.search(r"^\s*initrd\s+(\S+)", text, re.M)
            if linux and initrd:
                entry = (linux.group(1), initrd.group(1), linux.group(2))
        if entry:
            break

    if not entry:
        fail_setup("could not find a live boot entry in /isolinux/live.cfg or /boot/grub/grub.cfg")

    kernel_iso, initrd_iso, cmdline = entry
    kernel = os.path.join(workdir, "vmlinuz")
    initrd = os.path.join(workdir, "initrd.img")
    for src, dst in ((kernel_iso, kernel), (initrd_iso, initrd)):
        rc, _ = xorriso(iso, "-extract", src, dst)
        if rc != 0 or not os.path.exists(dst):
            fail_setup(f"could not extract {src} from {iso}")

    # Drop options that hide boot output or only make sense from the menu.
    drop = {"quiet", "splash"}
    args = [a for a in cmdline.split() if a not in drop and not a.startswith("findiso=")]
    # The last console= becomes /dev/console, so systemd's output goes to serial too.
    args += ["console=tty0", "console=ttyS0,115200"]
    return kernel, initrd, " ".join(args)


def qmp_screendump(sock_path, path):
    """Save a PNG of the guest screen through QMP. Returns True on success."""
    try:
        with socket.socket(socket.AF_UNIX, socket.SOCK_STREAM) as sock:
            sock.settimeout(10)
            sock.connect(sock_path)
            stream = sock.makefile("rwb")

            def call(command, **arguments):
                message = {"execute": command}
                if arguments:
                    message["arguments"] = arguments
                stream.write(json.dumps(message).encode() + b"\n")
                stream.flush()
                while True:
                    reply = json.loads(stream.readline())
                    if "return" in reply or "error" in reply:
                        return reply

            stream.readline()  # greeting
            call("qmp_capabilities")
            reply = call("screendump", filename=path, format="png")
            return "return" in reply
    except (OSError, ValueError):
        return False


def editions():
    return sorted(d for d in os.listdir(os.path.join(ROOT, "desktops"))
                  if os.path.exists(os.path.join(ROOT, "desktops", d, "desktop.conf")))


def main():
    parser = argparse.ArgumentParser(description="Headless boot test for one edition's live ISO.")
    parser.add_argument("--desktop", required=True, type=str.lower, choices=editions(),
                        help="edition to test")
    parser.add_argument("--iso", help="ISO to test (default: newest out/DISTRO_ID-*-DESKTOP-amd64.iso)")
    parser.add_argument("--out", help="directory for serial.log and screen.png (default: boot-test/DESKTOP/)")
    parser.add_argument("--timeout", type=int,
                        help="seconds to wait for the login prompt (default: 300 with KVM, 1200 without)")
    parser.add_argument("--mem", default="4G", help="guest RAM (default: 4G)")
    parser.add_argument("--strict", action="store_true",
                        help="fail if any systemd unit failed (default: warn)")
    parser.add_argument("--verbose", "-v", action="store_true",
                        help="print the serial console live")
    opts = parser.parse_args()
    out = opts.out or os.path.join(ROOT, "boot-test", opts.desktop)
    check_list = checks(opts.desktop)

    for tool, package in (("qemu-system-x86_64", "qemu-system-x86"), ("xorriso", "xorriso")):
        if not shutil.which(tool):
            fail_setup(f"{tool} not found. Install: sudo apt install {package}")

    iso = opts.iso
    if not iso:
        pattern = re.compile(rf"^{re.escape(DISTRO_ID)}-.+-{re.escape(opts.desktop)}-amd64\.iso$")
        out_dir = os.path.join(ROOT, "out")
        isos = sorted(
            (os.path.join(out_dir, f) for f in (os.listdir(out_dir) if os.path.isdir(out_dir) else [])
             if pattern.match(f)),
            key=os.path.getmtime,
        )
        if not isos:
            fail_setup(f"no ISO found. Build one with tools/build-iso.sh --desktop {opts.desktop}"
                       " or pass --iso FILE.")
        iso = isos[-1]
    iso = os.path.abspath(iso)
    if not os.access(iso, os.R_OK):
        fail_setup(f"cannot read {iso}")

    kvm = os.access("/dev/kvm", os.R_OK | os.W_OK)
    login_timeout = opts.timeout or (300 if kvm else 1200)
    # Everything is slower without KVM; scale per-check waits too.
    slow = 1 if kvm else 4

    os.makedirs(out, exist_ok=True)
    log_path = os.path.join(out, "serial.log")
    screen_path = os.path.join(out, "screen.png")
    for stale in (log_path, screen_path):
        if os.path.exists(stale):
            os.remove(stale)

    workdir = tempfile.mkdtemp(prefix="boot-test-")
    qmp_path = os.path.join(workdir, "qmp.sock")
    proc = None
    try:
        print(f"ISO: {iso}")
        kernel, initrd, cmdline = extract_boot_files(iso, workdir)
        print(f"Kernel command line: {cmdline}")
        if not kvm:
            print("Warning: /dev/kvm not usable, running without acceleration (slow).")
            print("         Try: sudo modprobe kvm_intel (AMD: kvm_amd); a blacklist in"
                  " /etc/modprobe.d/ only blocks auto-loading.")
            annotate("warning", "Boot test running without KVM")

        command = [
            "qemu-system-x86_64",
            "-m", opts.mem, "-smp", "2",
            "-kernel", kernel, "-initrd", initrd, "-append", cmdline,
            "-cdrom", iso,
            "-device", "virtio-vga", "-display", "none",
            "-nic", "user,model=virtio-net-pci",
            "-chardev", "stdio,id=ser0,signal=off", "-serial", "chardev:ser0",
            "-monitor", "none",
            "-qmp", f"unix:{qmp_path},server=on,wait=off",
        ]
        if kvm:
            command += ["-enable-kvm", "-cpu", "host"]

        proc = subprocess.Popen(command, stdin=subprocess.PIPE, stdout=subprocess.PIPE,
                                stderr=subprocess.STDOUT)
        console = Console(proc, log_path, opts.verbose)
        started = time.monotonic()

        # --- Log in ---------------------------------------------------------
        try:
            console.expect(rb"login: ", login_timeout)
            boot_seconds = time.monotonic() - started
            print(f"Login prompt after {boot_seconds:.0f}s")
            console.send(f"{LIVE_USER}\n")
            console.expect(rb"Password: ", 60 * slow)
            console.send(f"{LIVE_PASSWORD}\n")
            # Input typed before the shell is up can be flushed by login, so
            # wait for the bash prompt (user@host:~$).
            console.expect(rb"\$ $", 60 * slow)
            console.buf = b""
            # Quiet the session: no echo, no colors, no pagers, no kernel spam.
            # sudo reads pending terminal input (use_pty), hence </dev/null.
            # A user's PATH has no sbin.
            console.send(
                "stty -echo; export PS1= TERM=dumb SYSTEMD_COLORS=0 SYSTEMD_PAGER=cat PAGER=cat"
                " PATH=\"$PATH:/usr/sbin:/sbin\"; "
                "sudo -n dmesg -n 1 </dev/null 2>/dev/null; echo \"@@LOGGED\"\"_IN@@\"\n"
            )
            console.expect(rb"@@LOGGED_IN@@", 60 * slow)
        except Timeout as error:
            reason = "login failed" if b"Login incorrect" in console.buf else str(error)
            print(f"FAIL boot: {reason}. See {log_path}")
            annotate("error", f"Boot test: {reason}")
            qmp_screendump(qmp_path, screen_path)
            return 3
        print("PASS logged in on serial console")

        failures = 0

        # --- systemd state -------------------------------------------------
        rc, state = console.run("systemctl is-system-running --wait", timeout=300 * slow)
        if state.endswith("running"):
            print("PASS systemd: running")
        else:
            _, failed = console.run("systemctl --failed --no-legend --plain")
            detail = failed or "(no failed units listed)"
            if opts.strict:
                failures += 1
                print(f"FAIL systemd: {state}\n{detail}")
                annotate("error", f"Boot test: systemd {state}: {detail}")
            else:
                print(f"WARN systemd: {state}\n{detail}")
                annotate("warning", f"Boot test: systemd {state}: {detail}")

        # --- Checks ----------------------------------------------------------
        for description, check, wait in check_list:
            deadline = time.monotonic() + wait * slow
            while True:
                rc, output = console.run(check)
                if rc == 0 or time.monotonic() >= deadline:
                    break
                time.sleep(3)
            if rc == 0:
                print(f"PASS {description}")
            else:
                failures += 1
                print(f"FAIL {description} (exit {rc}){': ' + output if output else ''}")
                annotate("error", f"Boot test: {description} failed")

        # Give a desktop a moment to finish drawing before the screenshot.
        time.sleep(5)
        if qmp_screendump(qmp_path, screen_path):
            print(f"Screenshot: {screen_path}")

        # --- Memory report ----------------------------------------------------
        # "In use" = MemTotal - MemAvailable. The live session runs from RAM
        # (writes go to a tmpfs overlay), so an installed system uses less.
        rc, meminfo = console.run(
            "awk '/^MemTotal:/ {t=$2} /^MemAvailable:/ {a=$2} END {print int(t/1024), int((t-a)/1024)}' /proc/meminfo")
        if rc == 0 and len(meminfo.split()) == 2:
            mem_total, mem_used = (int(x) for x in meminfo.split())
            print(f"\nMEM  {mem_used} MB in use of {mem_total} MB (live session, idle)")

        total = len(check_list) + 1
        print(f"\n{total - failures}/{total} checks passed in {time.monotonic() - started:.0f}s. "
              f"Serial log: {log_path}")
        return 1 if failures else 0

    finally:
        if proc and proc.poll() is None:
            proc.terminate()
            try:
                proc.wait(timeout=10)
            except subprocess.TimeoutExpired:
                proc.kill()
        shutil.rmtree(workdir, ignore_errors=True)


if __name__ == "__main__":
    sys.exit(main())
