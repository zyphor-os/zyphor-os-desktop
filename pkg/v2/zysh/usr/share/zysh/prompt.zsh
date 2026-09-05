git_branch() {
    local branch
    branch=$(git branch --show-current 2>/dev/null)
    [[ -n "$branch" ]] && echo " [$branch]"
}

configure_prompt() {
    prompt_symbol=㉿
    # Skull emoji for root terminal
    #[ "$EUID" -eq 0 ] && prompt_symbol=💀
    case "$PROMPT_ALTERNATIVE" in
        twoline)
            PROMPT=$'%F{blue}┌──${debian_chroot:+($debian_chroot)─}${VIRTUAL_ENV:+($(basename $VIRTUAL_ENV))─}(%B%F{cyan}%n%b%F{cyan}@%B%F{cyan}%m%b%F{blue})-[%B%F{yellow}%(6~.%-1~/…/%4~.%5~)%b%F{blue}]$(git_branch)\n└─%B%F{magenta}$%b%F{reset} '
            #RPROMPT=$'%(?.. %? %F{red}%B⨯%b%F{reset})%(1j. %j %F{yellow}%B⚙%b%F{reset}.)'
            ;;
        oneline)
            PROMPT=$'${debian_chroot:+($debian_chroot)}${VIRTUAL_ENV:+($(basename $VIRTUAL_ENV))}%B%F{%(#.red.blue)}%n@%m%b%F{reset}:%B%F{%(#.blue.green)}%~%b%F{reset}%(#.#.$) '
            RPROMPT=
            ;;
        backtrack)
            PROMPT=$'${debian_chroot:+($debian_chroot)}${VIRTUAL_ENV:+($(basename $VIRTUAL_ENV))}%B%F{red}%n@%m%b%F{reset}:%B%F{blue}%~%b%F{reset}%(#.#.$) '
            RPROMPT=
            ;;
    esac
    unset prompt_symbol
}

configure_prompt

OS_NAME="$(grep '^PRETTY_NAME=' /etc/os-release | cut -d= -f2- | tr -d '"')"
BUILD="2026.09.05-r47"

echo ""
figlet -f term "$OS_NAME" | lolcat
printf "\033[1;37m\n  Build: \033[1;36m%s\033[0m\n" "$BUILD"
printf "\033[1;37m  Update: \033[1;32msudo zyphor system upgrade\033[0m\n"
printf "\033[1;30m  Alternative: sudo zy system upgrade\033[0m\n"
echo ""