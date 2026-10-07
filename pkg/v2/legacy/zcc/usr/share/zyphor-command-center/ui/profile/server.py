import socket
import subprocess
import time

_laravel_process = None

HOST = "127.0.0.1"
PORT = 2437


def is_running():
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.settimeout(0.2)
        return s.connect_ex((HOST, PORT)) == 0


def start():
    global _laravel_process

    if is_running():
        return

    _laravel_process = subprocess.Popen(
        [
            "php",
            "-S",
            "127.0.0.1:2437"
        ],
        cwd="/usr/share/zyphor-command-center-web", #prod
        # cwd="/home/programmer/zyphor-os-desktop/pkg/v2/zcc-web/usr/share/zyphor-command-center-web", # dev
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL,
    )

    timeout = 10

    while timeout > 0:
        if is_running():
            return
        time.sleep(0.2)
        timeout -= 0.2

    raise RuntimeError("Laravel server failed to start.")


def stop():
    global _laravel_process

    if _laravel_process:
        _laravel_process.terminate()
        _laravel_process.wait()
        _laravel_process = None