# Zyphor OS Bash configuration

# ---------------------------------
# Git branch
# ---------------------------------

git_branch() {
    local branch
    branch=$(git branch --show-current 2>/dev/null)

    if [ -n "$branch" ]; then
        echo " [$branch]"
    fi
}

# ---------------------------------
# Zyphor Prompt
# ---------------------------------

configure_prompt() {

    case "$PROMPT_ALTERNATIVE" in

        twoline)
            PS1='\[\e[1;34m\]┌──${debian_chroot:+($debian_chroot)─}${VIRTUAL_ENV:+($(basename "$VIRTUAL_ENV")─}(\[\e[1;36m\]\u@\h\[\e[1;34m\])-[\[\e[1;33m\]\w\[\e[1;34m\]]\n└─\[\e[1;35m\]\$\[\e[0m\] '
            ;;

        oneline)
            PS1='${debian_chroot:+($debian_chroot)}${VIRTUAL_ENV:+($(basename "$VIRTUAL_ENV"))}\u@\h:\w\$ '
            ;;

        backtrack)
            PS1='\[\e[1;31m\]\u@\h\[\e[0m\]:\[\e[1;34m\]\w\[\e[0m\]\$ '
            ;;

        *)
            PS1='\[\e[1;34m\]┌──(\[\e[1;36m\]\u@\h\[\e[1;34m\])-[\[\e[1;33m\]\w\[\e[1;34m\]]\n└─\[\e[1;35m\]\$\[\e[0m\] '
            ;;

    esac
}

configure_prompt

# ---------------------------------
# Zyphor OS Information
# ---------------------------------

OS_NAME="$(grep '^PRETTY_NAME=' /etc/os-release 2>/dev/null | cut -d= -f2- | tr -d '"')"
BUILD="2026.10.07-r1"

echo ""

if command -v figlet >/dev/null 2>&1 && command -v lolcat >/dev/null 2>&1; then
    figlet -f term "$OS_NAME" | lolcat
else
    printf "\033[1;36m%s\033[0m\n" "$OS_NAME"
fi

printf "\033[1;37m\n  Build: \033[1;36m%s\033[0m\n" "$BUILD"
printf "\033[1;37m  Update: \033[1;32msudo zyphor system upgrade\033[0m\n"
printf "\033[1;30m  Alternative: sudo zy system upgrade\033[0m\n"
echo ""