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

# ---------------------------------
# ble.sh (Kali-style gray ghost-text suggestions)
# Optional: loads only if installed.
# Search order: user install first, then the zyphor-blesh package.
# Settings: the user's ~/.blerc wins, otherwise /etc/zyphor/blerc.
# ---------------------------------
_zy_ble=""
for _zy_p in "$HOME/.local/share/blesh/ble.sh" /usr/share/blesh/ble.sh; do
    if [ -f "$_zy_p" ]; then
        _zy_ble="$_zy_p"
        break
    fi
done
if [ -n "$_zy_ble" ]; then
    if [ -f "$HOME/.blerc" ] || [ ! -f /etc/zyphor/blerc ]; then
        source "$_zy_ble" --noattach
    else
        source "$_zy_ble" --noattach --rcfile /etc/zyphor/blerc
    fi
fi
unset _zy_ble _zy_p
# ---------------------------------
# History & autocomplete
# ---------------------------------
HISTFILE="$HOME/.bash_history"
HISTSIZE=50000
HISTFILESIZE=100000
HISTCONTROL=ignoreboth:erasedups   # skip duplicates and commands starting with a space
shopt -s histappend                # append instead of overwriting on exit
shopt -s cmdhist                   # keep multi-line commands as one entry
# Save each command immediately so all open terminals share history
# (guarded so re-sourcing this file doesn't add it twice)
case "$PROMPT_COMMAND" in
    *"history -a"*) ;;
    *) PROMPT_COMMAND="history -a; history -n${PROMPT_COMMAND:+; $PROMPT_COMMAND}" ;;
esac
# Nicer tab completion
bind 'set show-all-if-ambiguous on'
bind 'set completion-ignore-case on'
# Up/Down arrows search history by what you've already typed
# (skipped when ble.sh is active, since it handles this itself)
if [ -z "${BLE_VERSION-}" ]; then
    bind '"\e[A": history-search-backward'
    bind '"\e[B": history-search-forward'
    bind '"\eOA": history-search-backward'
    bind '"\eOB": history-search-forward'
fi
# Optional: fuzzy history search with Ctrl+R (needs fzf installed)
[ -f /usr/share/doc/fzf/examples/key-bindings.bash ] && \
    source /usr/share/doc/fzf/examples/key-bindings.bash

# ---------------------------------
# Zyphor OS Information
# ---------------------------------

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

# ---------------------------------
# ble.sh attach (must be the very last line)
# ---------------------------------
[[ ${BLE_VERSION-} ]] && ble-attach