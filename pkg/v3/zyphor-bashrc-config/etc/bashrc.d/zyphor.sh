# Zyphor OS Bash configuration

# ---------------------------------
# Git branch (only shown inside a git repo, silent if git is missing)
# ---------------------------------

git_branch() {
    # No git installed (non-developer systems) -> print nothing
    command -v git >/dev/null 2>&1 || return 0

    local branch
    # Works from any subdirectory of a repo, not just the root
    branch=$(git symbolic-ref --short -q HEAD 2>/dev/null) \
        || branch=$(git rev-parse --short HEAD 2>/dev/null)   # detached HEAD -> short hash

    [ -n "$branch" ] && printf ' (%s)' "$branch"
}

# ---------------------------------
# Zyphor Prompt
# ---------------------------------

configure_prompt() {

    case "$PROMPT_ALTERNATIVE" in

        twoline)
            PS1='\[\e[1;34m\]┌──${debian_chroot:+($debian_chroot)─}${VIRTUAL_ENV:+($(basename "$VIRTUAL_ENV")─}(\[\e[1;36m\]\u@\h\[\e[1;34m\])-[\[\e[1;33m\]\w\[\e[1;34m\]]\[\e[1;32m\]$(git_branch)\[\e[1;34m\]\n└─\[\e[1;35m\]\$\[\e[0m\] '
            ;;

        oneline)
            PS1='${debian_chroot:+($debian_chroot)}${VIRTUAL_ENV:+($(basename "$VIRTUAL_ENV"))}\u@\h:\w\[\e[1;32m\]$(git_branch)\[\e[0m\]\$ '
            ;;

        backtrack)
            PS1='\[\e[1;31m\]\u@\h\[\e[0m\]:\[\e[1;34m\]\w\[\e[1;32m\]$(git_branch)\[\e[0m\]\$ '
            ;;

        *)
            PS1='\[\e[1;34m\]┌──(\[\e[1;36m\]\u@\h\[\e[1;34m\])-[\[\e[1;33m\]\w\[\e[1;34m\]]\[\e[1;32m\]$(git_branch)\[\e[1;34m\]\n└─\[\e[1;35m\]\$\[\e[0m\] '
            ;;

    esac
}

configure_prompt

# ---------------------------------
# Zyphor OS Information
# ---------------------------------

# Only show on interactive terminals
case $- in *i*) ;; *) return 2>/dev/null || exit 0 ;; esac
[ -t 1 ] || { return 2>/dev/null || exit 0; }

zyphor_banner() {
    os_name="Zyphor OS"
    [ -r /etc/os-release ] && . /etc/os-release && os_name="${PRETTY_NAME:-$os_name}"

    if command -v figlet >/dev/null 2>&1 && command -v lolcat >/dev/null 2>&1; then
        figlet -f term "$os_name" | lolcat
    else
        printf '\033[36m%s\033[0m\n' "$os_name"
    fi

    echo
    printf '\033[2mUpdate: \033[0m\033[32msudo zyphor system upgrade\033[0m\n'
    printf '\033[2mAlt:    sudo zy system upgrade\033[0m\n'
    printf '\033[2mClean and Optimize system: \033[0m\033[32msudo zy system clean\033[0m\n'
    echo
}

zyphor_banner
unset -f zyphor_banner