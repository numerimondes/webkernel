<style>
    :root {
        --color-primary: rgba(5, 249, 255, 1);
        --transition: 0.4s;

        /* Light defaults */
        --surface-bg: oklch(0.985 0 0);
        --text-color: #0b0e14;
        --muted-text: oklch(0.3 0 0);

        /* Glass effects plus naturels et transparents */
        --glass-bg-1: oklch(0.98 0 0);
        --glass-bg-2: oklch(0.96 0 0);
        --glass-stroke: oklch(0.2 0 0);
        --glass-inner-stroke: oklch(0.1 0 0);
        --glass-shadow: 0 8px 32px rgba(16, 24, 40, 0.08),
                        0 4px 16px rgba(16, 24, 40, 0.04),
                        0 1px 4px rgba(16, 24, 40, 0.02);
        --glass-blur: 16px;
        --glass-saturate: 120%;
        --radius-xl: 18px;

        color-scheme: light dark;

        /* Header glass - encore plus subtil pour éviter l'effet "sale" */
        --header-glass-1: oklch(0.98 0 0);
        --header-glass-2: oklch(0.96 0 0);
        --header-stroke: oklch(0.2 0 0);
        --header-shadow: 0 4px 20px rgba(16, 24, 40, 0.06),
                         0 2px 8px rgba(16, 24, 40, 0.04);

        /* Sports items - même effet que Events */
        --sports-glass-1: rgba(255,255,255,0.15);
        --sports-glass-2: rgba(255,255,255,0.10);
        --sports-stroke: rgba(255,255,255,0.20);
        --sports-shadow: 0 6px 24px rgba(16, 24, 40, 0.08),
                         0 2px 8px rgba(16, 24, 40, 0.04);
        /* Desktop mega-menu opacity */
        --submenu-opacity: 0.96;
    }

    body {
        font-family: "Inter", sans-serif;
        color: var(--text-color);
    }

    /* prevent horizontal overflow while mega menu is open */
    body.mm-open {
        overflow: hidden;
    }

    .base-template__wrapper {
        /*min-height: calc(100dvh - 300px);
        padding-bottom: 450px;*/
        justify-content: flex-start;
    }

    .wrapper {}

    a {
        color: var(--text-color);
        text-decoration: none;
    }

    img {
        max-width: 100%;
    }

    .header {
        display: flex;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 11000;
        padding: 0 20px 0 40px;
        min-height: 82px;
        background: linear-gradient(135deg,
            var(--header-glass-1) 0%,
            var(--header-glass-2) 100%);
        backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturate));
        -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturate));
        border: 1px solid var(--header-stroke);
        box-shadow: var(--header-shadow);
        overflow: visible;
        isolation: isolate;
    }

    .header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg,
            rgba(255,255,255,0.02) 0%,
            transparent 50%,
            rgba(255,255,255,0.04) 100%);
        pointer-events: none;
        border-radius: inherit;
    }

    .header__logo {
        max-width: none !important;
        padding: 5px;
    }

    .header__wrapper {
        width: 100%;
        display: flex;
        align-items: center;
    }

    .header__navigation-wrapper {
        display: flex;
        width: 100%;
        padding-left: 50px;
        position: relative;
        z-index: 10001;
        align-items: center; /* centre verticalement */
    }

    .header__list {
        display: flex;
        align-items: center; /* centre les li */
        gap: 28px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .header__list-item {
        display: flex;
        align-items: center; /* centre le contenu de chaque li */
        gap: 8px;
        font-size: 16px;
        /* suppression des décalages verticaux inutiles */
        padding: 0;
        margin: 0;
    }

    .header__list-item > a {
        display: flex;
        align-items: center; /* centre texte + icône */
        gap: 8px;
        transition: var(--transition);
        text-decoration: none;
        color: var(--text-color);
    }

    .header__list-item > a svg path {
        transition: var(--transition);
    }

    .header__list-item .submenu-wrapper {
        position: absolute;
        top: 110%;
        left: 50%;
        z-index: 10000;
        width: 100vw;
        max-width: 100vw;
        transform: translateX(-50%);
        box-sizing: border-box;
        padding: 24px 24px 40px 24px;
        background: linear-gradient(180deg, var(--glass-bg-1), var(--glass-bg-2));
        backdrop-filter: saturate(var(--glass-saturate)) blur(var(--glass-blur));
        -webkit-backdrop-filter: saturate(var(--glass-saturate)) blur(var(--glass-blur));
        border: 1px solid var(--glass-stroke);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity var(--transition), visibility var(--transition);
        will-change: opacity;
        box-shadow: var(--glass-shadow);
    }

    /* Ensure open state is visible on desktop regardless of pointer/hover capabilities */
    @media screen and (min-width: 1026px) {
        .header__list-item.open .submenu-wrapper {
            opacity: var(--submenu-opacity);
            visibility: visible;
            pointer-events: auto;
            display: block;
        }
        /* when a menu is open, slightly lighten header to avoid double haze */
        body.mm-open .header {
            backdrop-filter: saturate(calc(var(--glass-saturate) * 0.85)) blur(calc(var(--glass-blur) * 0.6)) brightness(1.10) contrast(1.03);
            -webkit-backdrop-filter: saturate(calc(var(--glass-saturate) * 0.85)) blur(calc(var(--glass-blur) * 0.6)) brightness(1.10) contrast(1.03);
            box-shadow: 0 6px 18px rgba(16,24,40,0.10);
        }
    }

    .header__buttons-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-left: auto;
    }

    .header__button {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        width: max-content;
        padding: 6px 20px;
        border-radius: 100px;
        gap: 8px;
        font-size: 16px;
        font-weight: 400;
        transition: var(--transition);
        border: 1px solid var(--glass-stroke);
        color: var(--text-color);
        background: linear-gradient(180deg, var(--glass-bg-1), var(--glass-bg-2));
        backdrop-filter: saturate(var(--glass-saturate)) blur(calc(var(--glass-blur) * 0.6));
        -webkit-backdrop-filter: saturate(var(--glass-saturate)) blur(calc(var(--glass-blur) * 0.6));
        box-shadow: 0 2px 8px rgba(16, 24, 40, 0.12);
    }

    .submenu-list__title {
        width: max-content;
        margin-bottom: 25px;
        font-size: 12px;
        text-transform: uppercase;
        color: var(--muted-text);
    }

    .submenu-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-width: 295px;
        padding-left: 0;
    }

    .submenu-list__item {
        display: flex;
        padding-right: 100px;
        margin-right: -100px;
        cursor: pointer;
    }

    .submenu-list__item-wrapper {
        width: 100%;
        display: flex;
        align-items: center;
        padding: 6px 16px 6px 6px;
        gap: 16px;
        border-radius: 14px;
        border: 1px solid transparent;
        transition: var(--transition);
    }

    .submenu-list__item-wrapper > svg {
        margin-left: auto;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }

    .submenu-list__wrapper {
        position: relative;
    }

    /* Full mode for very large submenus */
    .submenu-wrapper.full {
        top: 0;
        left: 0;
        transform: none;
        position: fixed;
        inset: 0;
        z-index: 10050;
        width: 100%;
        height: 100dvh;
        overflow: auto;
        padding: 20px 0 40px;
        background: linear-gradient(180deg, var(--glass-bg-1), var(--glass-bg-2));
        backdrop-filter: saturate(160%) blur(24px);
        -webkit-backdrop-filter: saturate(160%) blur(24px);
        border: 1px solid var(--glass-stroke);
        opacity: var(--submenu-opacity);
    }

    .submenu-wrapper.full .submenu-list__wrapper {
        max-width: min(1280px, 100%);
        margin: 0 auto;
        padding: 0 24px 40px;
        box-sizing: border-box;
    }

    .submenu-wrapper.full .submenu-content {
        position: static;
        max-width: 100%;
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .submenu-wrapper.full .submenu-content__list,
    .submenu-wrapper.full .submenu-content__list.events {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    /* Toolbar inside full mode */
    .submenu-toolbar {
        position: sticky;
        top: 0;
        z-index: 10001;
        display: none;
        align-items: center;
        gap: 12px;
        padding: 12px 24px;
        background: rgba(25, 27, 36, 0.95);
        backdrop-filter: blur(6px);
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .submenu-toolbar__search {
        flex: 1 1 auto;
        max-width: 720px;
    }
    .submenu-toolbar__search input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.06);
        color: #fff;
        outline: none;
    }
    .submenu-toolbar__close {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.06);
        color: #fff;
        cursor: pointer;
    }

    .submenu-content {
        position: absolute;
        right: 0;
        top: 0;
        max-width: calc(100% - 365px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: var(--transition);
    }

    .submenu-list__item.has-submenu.active .submenu-content {
        opacity: 1;
        visibility: visible;
    }

    .submenu-list__item.has-submenu.active .submenu-list__item-wrapper {
        background-color: rgba(255, 255, 255, 0.04);
        border-color: var(--color-primary);
    }

    .submenu-list__item.has-submenu.active .submenu-list__item-wrapper > svg {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .submenu-list__item-link {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .submenu-list__item-title {
        font-size: 16px;
        font-weight: 500;
        color: var(--text-color);
    }

    .submenu-list__item-icon {
        display: flex;
    }

    .submenu-list__item-subtile {
        font-size: 12px;
        font-weight: 400;
        color: var(--muted-text);
    }

    .submenu-content__title {
        width: max-content;
        margin-bottom: 25px;
        font-size: 12px;
        text-transform: uppercase;
        color: rgba(160, 161, 165, 1);
    }

    .submenu-content__list:not(.events) {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 0;
    }

    .submenu-content__list.events {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        padding: 0;
    }

    .submenu-content__list-item {
        display: block;
        border-radius: 20px;
        background: transparent;
        cursor: auto;
    }

    .submenu-content__link {
        display: flex;
        flex-direction: column;
        border-radius: 20px;
        padding: 10px 10px 20px;
        border: 1px solid var(--glass-stroke);
        transition: var(--transition);
        background: linear-gradient(180deg, var(--glass-bg-1), var(--glass-bg-2));
        backdrop-filter: saturate(130%) blur(calc(var(--glass-blur) * 0.45)) brightness(1.03);
        -webkit-backdrop-filter: saturate(130%) blur(calc(var(--glass-blur) * 0.45)) brightness(1.03);
        box-shadow: 0 8px 20px rgba(16,24,40,0.14);
    }

    .submenu-content__link-img {
        margin-bottom: 20px;
        border-radius: 13px;
        overflow: hidden;
        transition: var(--transition);
        width: 100%;
        aspect-ratio: 16 / 9;
    }

    .submenu-content__link-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.4s ease-in;
    }

    .submenu-content__link-title {
        padding: 0 10px;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 12px;
    }

    .submenu-content__link-text {
        padding: 0 10px;
        font-size: 12px;
        color: var(--muted-text);
    }

    .submenu-content__link-wrapper {
        display: flex;
        gap: 20px;
        padding: 10px;
        border-radius: 20px;
        background: linear-gradient(180deg, var(--glass-bg-1), var(--glass-bg-2));
        border: 1px solid var(--glass-stroke);
        backdrop-filter: saturate(130%) blur(calc(var(--glass-blur) * 0.5));
        -webkit-backdrop-filter: saturate(130%) blur(calc(var(--glass-blur) * 0.5));
    }

    .submenu-content__list.events .submenu-content__link-img {
        width: 100%;
        max-width: clamp(180px, 24vw, 320px);
        flex: 1;
        border-radius: 13px;
        margin-bottom: 0;
        aspect-ratio: 16 / 9;
    }

    .submenu-content__info {
        display: flex;
        flex-direction: column;
        flex: 1 0;
    }

    .submenu-content__category {
        display: flex;
        align-items: center;
        gap: 10px;
        width: max-content;
        padding: 10px 20px;
        margin-bottom: 20px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.7);
    }

    .submenu-content__list.events .submenu-content__link-title,
    .submenu-content__list.events .submenu-content__link-text {
        padding: 0;
        margin-bottom: 12px;
    }

    .submenu-content__link-address,
    .submenu-content__link-date {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 12px;
        font-size: 12px;
        font-weight: 400;
        color: rgba(160, 161, 165, 1);
    }

    .submenu-content__link-address span,
    .submenu-content__link-date span {
        line-height: 0.9;
    }

    .submenu-content__url {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto;
        margin-bottom: 20px;
        color: var(--text-color);
        font-size: 14px;
        transition: var(--transition);

        svg,
        svg path {
            transition: var(--transition);
        }
    }

    .header__burger {
        display: none;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        width: 24px;
        margin-left: auto;
    }

    .header__burger i {
        width: 100%;
        height: 2px;
        background-color: var(--text-color);
        border-radius: 13px;
        transition: var(--transition);
    }

    .header__burger.active i:nth-child(1) {
        transform: rotate(45deg) translate(4px, 4px);
    }

    .header__burger.active i:nth-child(2) {
        opacity: 0;
    }

    .header__burger.active i:nth-child(3) {
        transform: rotate(-45deg) translate(4px, -5px);
    }

    @media (hover: hover) and (pointer: fine) {
        /* Remove auto-open on hover; only show when .open is present */
        .header__list-item.open .submenu-wrapper {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Keep visible if explicitly opened/pinned */
        .header__list-item.open .submenu-wrapper {
            display: block;
        }

        .header__list-item:hover > a,
        .header__list-item:hover > a svg path,
        .header__list-item.open > a,
        .header__list-item.open > a svg path {
            color: var(--color-primary);
            fill: var(--color-primary);
            stroke: var(--color-primary);
        }

        .header__button:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .submenu-list__item.has-submenu:hover .submenu-list__item-wrapper {
            background-color: rgba(255, 255, 255, 0.04);
        }

        /* Inner items: only active/pinned shows content to avoid stacking */
        .submenu-list__item.has-submenu:hover .submenu-content,
        .submenu-list__item.has-submenu:hover .submenu-list__item-wrapper > svg { }
        .submenu-list__item.has-submenu.active .submenu-content,
        .submenu-list__item.has-submenu.active .submenu-list__item-wrapper > svg {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Keep visible in full mode regardless of hover */
        .submenu-wrapper.full .submenu-list__item .submenu-content,
        .submenu-wrapper.full .submenu-list__item .submenu-list__item-wrapper > svg {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .submenu-content__list-item:hover .submenu-content__link {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .submenu-content__list-item:hover .submenu-content__link-img img {
            transform: scale(1.05);
        }

        .submenu-content__url:hover,
        .submenu-content__url:hover svg path {
            color: var(--color-primary);
            stroke: var(--color-primary);
        }

        .header__button:hover {
            box-shadow: 0 6px 16px rgba(16,24,40,0.16);
            transform: translateY(-1px);
        }

        .submenu-content__url:hover svg {
            transform: translateX(5px);
        }
    }

    @media screen and (max-width: 1280px) {
        .header__navigation-wrapper {
            padding-left: 25px;
        }

        .submenu-list {
            max-width: 250px;
        }

        .submenu-content {
            max-width: calc(100% - 270px);
        }

        .submenu-content__url {
            margin-bottom: 0;
        }
    }

    @media screen and (max-width: 1024px) {
        .base-template__wrapper {
            /*min-height: 105vh;*/
        }

        .header {
            min-height: 64px;
        }

        .header__burger {
            display: flex;
        }

        .header__navigation-wrapper {
            flex-direction: column;
            align-items: center;
            position: absolute;
            top: 110%;
            left: 0;
            padding: 20px;
            border-radius: var(--radius-xl);
            background: linear-gradient(180deg, var(--glass-bg-1), var(--glass-bg-2));
            backdrop-filter: saturate(var(--glass-saturate)) blur(var(--glass-blur));
            -webkit-backdrop-filter: saturate(var(--glass-saturate)) blur(var(--glass-blur));
            border: 1px solid var(--glass-stroke);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            box-shadow: var(--glass-shadow);
        }

        .header__navigation-wrapper.open {
            opacity: 1;
            visibility: visible;
        }

        .header__list {
            flex-direction: column;
            gap: 30px;
        }

        .header__buttons-wrapper {
            flex-direction: column;
            margin-left: unset;
            margin-top: 50px;
            gap: 8px;
        }

        .header__navigation,
        .header__list {
            width: 100%;
        }

        .header__list-item {
            flex-direction: column;
            width: 100%;
            padding: 0;
            gap: 0;
            margin: 0;
        }

        .header__list-item.active a,
        .header__list-item.active a > svg path {
            fill: var(--color-primary);
            color: var(--color-primary);
        }

        .header__list-item .submenu-wrapper {
            position: static;
            padding: 0;
            max-height: 0;
            border-radius: 33px !important;
            opacity: 1;
            visibility: visible;
            pointer-events: all;
            overflow: hidden;
            transition: max-height var(--transition);
        }

        .submenu-list {
            width: 100%;
            max-width: 100%;
            gap: 5px;
        }

        .submenu-list__wrapper {
            margin-top: 30px;
        }

        .submenu-list__item {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .submenu-list__item:active .submenu-list__item-wrapper {
            background-color: rgba(255, 255, 255, 0.04);
        }

        .submenu-list__item:active .submenu-list__item-wrapper > svg {
            opacity: 1;
            visibility: visible;
        }

        .submenu-list__title {
            display: none;
        }

        .submenu-content {
            display: none;
        }

        .header__button {
            border: 1px solid var(--glass-stroke);
        }
    }

    @media screen and (max-width: 767.9px) {
        .header__buttons-wrapper,
        .header__button {
            width: 100%;
        }
    }

    .submenu-wrapper.full .submenu-toolbar { display: flex !important; }

    /* Make non-full mega menu span the viewport safely */
    .submenu-wrapper.viewport-span {
        position: fixed;
        left: 0;
        right: 0;
        width: auto;
        max-width: 100vw;
        box-sizing: border-box;
        overflow-x: clip;
    }

    .header__list-item > a .menu-icon { display: inline-flex; align-items: center; color: var(--muted-text); }
    .header__list-item > a .menu-icon--open,
    .header__list-item > a .menu-icon--close { display: none; }
    .header__list-item.open:not(.pinned) > a .menu-icon--chevron { display: none; }
    .header__list-item.open:not(.pinned) > a .menu-icon--open { display: inline-flex; }
    .header__list-item.pinned > a .menu-icon--chevron,
    .header__list-item.pinned > a .menu-icon--open { display: none; }
    .header__list-item.pinned > a .menu-icon--close { display: inline-flex; }

    .base-template {
        background: var(--surface-bg);
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --surface-bg: oklch(0.141 0.005 285.823);
            --text-color: oklch(0.92 0 0);
            --muted-text: oklch(0.5 0 0);
            --glass-bg-1: oklch(0.12 0 0);
            --glass-bg-2: oklch(0.08 0 0);
            --glass-stroke: oklch(0.2 0 0);
            --glass-inner-stroke: oklch(0.1 0 0);
            --glass-shadow: 0 12px 40px rgba(0,0,0,0.45), 0 2px 12px rgba(0,0,0,0.35);
            --glass-blur: 22px;
            --glass-saturate: 140%;
            /* header dark tuning: avoid muddy grey */
            --header-glass-1: oklch(0.12 0 0);
            --header-glass-2: oklch(0.08 0 0);
            --header-stroke: oklch(0.2 0 0);
            --header-shadow: 0 10px 32px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.02) inset;
        }
    }

    /* Sports and Events glass mapping on existing selectors */
    /* Sports cards: non-events submenu item cards */
    .submenu-content__list:not(.events) .submenu-content__link {
        background: linear-gradient(135deg, var(--sports-glass-1) 0%, var(--sports-glass-2) 100%);
        backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturate));
        -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturate));
        border: 1px solid var(--sports-stroke);
        box-shadow: var(--sports-shadow);
    }

    /* Events cards: keep optimized base glass */
    .submenu-content__list.events .submenu-content__link {
        background: linear-gradient(135deg, var(--glass-bg-1) 0%, var(--glass-bg-2) 100%);
        backdrop-filter: blur(14px) saturate(110%);
        -webkit-backdrop-filter: blur(14px) saturate(110%);
        border: 1px solid var(--glass-stroke);
        box-shadow: var(--glass-shadow);
    }
</style>

@php
    $menus = [
        [
            'title' => 'Products',
            'type' => 'sports',
            'categories' => [
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#2BBE22" fill-opacity="0.1" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M22 25.2969C25.866 25.2969 29 22.1629 29 18.2969C29 14.4309 25.866 11.2969 22 11.2969C18.134 11.2969 15 14.4309 15 18.2969C15 22.1629 18.134 25.2969 22 25.2969ZM17.9789 26.398C18.1885 26.1242 18.5747 26.0599 18.8621 26.2496L18.8772 26.259C18.8923 26.2683 18.9181 26.2837 18.9541 26.3039C19.0262 26.3443 19.1391 26.4035 19.29 26.4709C19.592 26.6057 20.0444 26.7721 20.6255 26.8852C21.0249 26.963 21.4852 27.0157 22 27.0157C22.7233 27.0157 23.3394 26.9117 23.8299 26.78C24.267 26.6628 24.6041 26.5235 24.8273 26.4167C24.9388 26.3633 25.0215 26.3182 25.0736 26.2882C25.0997 26.2732 25.1181 26.262 25.1286 26.2555L25.1377 26.2498C25.1373 26.25 25.1368 26.2503 25.1364 26.2506L25.1377 26.2497L25.1377 26.2498C25.4252 26.0593 25.8113 26.124 26.0211 26.398C26.2312 26.6725 26.1922 27.0631 25.9322 27.2908L25.931 27.2918L25.9249 27.2972L25.8986 27.3206C25.875 27.3418 25.8395 27.3739 25.7939 27.4158C25.7025 27.4998 25.5708 27.6232 25.4123 27.7785C25.0943 28.0898 24.6722 28.5253 24.252 29.0243C23.8298 29.5256 23.4214 30.0772 23.1214 30.6208C22.8157 31.1749 22.6561 31.6612 22.6561 32.0469C22.6561 32.4094 22.3623 32.7032 21.9998 32.7032C21.6374 32.7031 21.3436 32.4093 21.3436 32.0469C21.3436 31.6612 21.1841 31.1748 20.8785 30.6208C20.5786 30.0772 20.1701 29.5256 19.748 29.0243C19.3278 28.5253 18.9057 28.0898 18.5878 27.7785C18.4292 27.6232 18.2975 27.4998 18.2062 27.4158C18.1606 27.3739 18.1251 27.3418 18.1014 27.3206L18.0751 27.2972L18.069 27.2918L18.0677 27.2907C17.8076 27.063 17.7688 26.6725 17.9789 26.398Z" fill="#2BBE22" />
                    </svg>',
                    'title' => 'Golf',
                    'subtitle' => 'Precision and patience',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/golf_1.png',
                            'alt' => 'Golf',
                            'title' => 'A Moment of Hope',
                            'text' => 'A dramatic finish at the Masters as a golfer sinks a crucial putt, securing victory in a nail-biting final round.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/golf_2.png',
                            'alt' => 'Golf',
                            'title' => 'Perfect Shot',
                            'text' => 'With precision and control, the rising star of golf delivers a flawless drive, setting a new course record.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/golf_3.png',
                            'alt' => 'Golf',
                            'title' => 'Championship Course',
                            'text' => 'This year\'s tournament takes place on one of the most stunning golf courses, challenging players with its intricate design.',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#47A9D6" fill-opacity="0.1" />
                        <g clip-path="url(#clip0_850_7774)">
                            <path d="M21.951 22.0515C24.0989 24.1994 27.8979 23.8828 30.4363 21.3444C32.9747 18.806 33.2913 15.007 31.1434 12.8591C28.9955 10.7112 25.1965 11.0278 22.6581 13.5662C20.1197 16.1046 19.8031 19.9036 21.951 22.0515ZM21.951 22.0515L17.3549 26.6472M24.435 12.4964L31.5061 19.5674M21.6066 15.3248L28.6776 22.3959M31.6825 14.7942L23.9044 22.5724M29.2077 12.3193L21.4295 20.0975M16.0439 26.8266L15.5489 26.8974C15.1205 26.9586 14.7235 27.157 14.4175 27.463L11.707 30.1736C11.3164 30.5641 11.3164 31.1973 11.707 31.5878L12.4141 32.2949C12.8046 32.6855 13.4378 32.6855 13.8283 32.2949L16.5389 29.5844C16.8448 29.2784 17.0433 28.8814 17.1045 28.453L17.1752 27.958C17.2695 27.298 16.7038 26.7324 16.0439 26.8266Z" stroke="#47A9D6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_850_7774">
                                <rect width="24" height="24" fill="white" transform="translate(10 10)" />
                            </clipPath>
                        </defs>
                    </svg>',
                    'title' => 'Tennis',
                    'subtitle' => 'Speed and power',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/tennis_1.png',
                            'alt' => 'Tennis',
                            'title' => 'Grand Slam Glory',
                            'text' => 'A breathtaking final sees a young tennis prodigy outlast a seasoned champion in an intense five-set thriller.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/tennis_2.png',
                            'alt' => 'Tennis',
                            'title' => 'Unstoppable Serve',
                            'text' => 'A record-breaking serve clocks in at an astonishing speed, leaving the opponent no chance to react.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/tennis_3.png',
                            'alt' => 'Tennis',
                            'title' => 'Clay Court Battle',
                            'text' => 'A grueling baseline rally on the red clay pushes both players to their limits in a showcase of endurance and skill.',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#FD742E" fill-opacity="0.1" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M19.0059 12.0213C18.9847 11.9768 18.9342 11.9545 18.8871 11.9691C17.0996 12.5232 15.5165 13.542 14.2789 14.8841C14.2389 14.9276 14.2458 14.9959 14.2928 15.0317C15.5181 15.9639 16.4902 17.2119 17.0876 18.6544C17.106 18.6988 17.1532 18.7243 17.2004 18.7151C18.3855 18.4822 19.601 18.3339 20.8404 18.2767C20.9002 18.2739 20.9445 18.2197 20.9346 18.1606C20.5754 15.9988 19.9179 13.9378 19.0059 12.0213ZM13.223 16.2347C13.2542 16.1873 13.3189 16.1759 13.3642 16.2102C14.3153 16.9282 15.0839 17.8752 15.588 18.9692C15.6137 19.025 15.5824 19.0901 15.5231 19.1061C14.2262 19.4568 12.9715 19.9103 11.7684 20.4573C11.6963 20.49 11.6162 20.4305 11.6286 20.3523C11.8653 18.8502 12.4206 17.4539 13.223 16.2347ZM16.1167 20.6093C16.1058 20.5531 16.0496 20.5184 15.9943 20.5331C14.4465 20.9458 12.9622 21.5142 11.5592 22.2205C11.5248 22.2379 11.5035 22.2736 11.5046 22.3121C11.5633 24.3227 12.1872 26.1916 13.223 27.7653C13.2542 27.8127 13.3189 27.824 13.3642 27.7898C15.1171 26.4665 16.25 24.3655 16.25 22C16.25 21.5243 16.2042 21.0594 16.1167 20.6093ZM30.4777 15.9672C30.5223 15.9374 30.5823 15.9488 30.6129 15.9927C31.5039 17.2678 32.1189 18.7498 32.3715 20.3523C32.3838 20.4305 32.3038 20.49 32.2317 20.4573C30.8919 19.8482 29.4882 19.355 28.0334 18.9906C27.9724 18.9753 27.9401 18.9083 27.9674 18.8516C28.533 17.6803 29.4043 16.6843 30.4777 15.9672ZM29.5148 14.6667C29.558 14.7109 29.5502 14.7833 29.4995 14.8187C28.1613 15.7522 27.0963 17.0503 26.4484 18.5688C26.4299 18.612 26.3842 18.6371 26.3379 18.6289C25.1053 18.411 23.8413 18.2843 22.5529 18.2561C22.5042 18.255 22.4632 18.2191 22.4557 18.1709C22.1044 15.9088 21.4468 13.7481 20.5265 11.7324C20.4987 11.6715 20.537 11.6009 20.6033 11.5921C21.0602 11.5314 21.5265 11.5 22 11.5C24.9457 11.5 27.608 12.713 29.5148 14.6667ZM30.4777 28.0328C30.5222 28.0626 30.5823 28.0512 30.6129 28.0073C31.7468 26.3847 32.4338 24.4268 32.4955 22.3121C32.4966 22.2736 32.4753 22.2379 32.4409 22.2205C30.8943 21.4419 29.2488 20.8309 27.5283 20.4112C27.4742 20.3981 27.4198 20.4318 27.4083 20.4863C27.3046 20.9745 27.25 21.4809 27.25 22C27.25 24.516 28.5317 26.7327 30.4777 28.0328ZM25.8564 20.0683C25.9128 20.0778 25.9495 20.1326 25.9377 20.1886C25.8147 20.773 25.75 21.379 25.75 22C25.75 24.9732 27.2329 27.6001 29.4995 29.1813C29.5502 29.2167 29.558 29.2891 29.5148 29.3333C27.608 31.287 24.9457 32.5 22 32.5C21.5265 32.5 21.0602 32.4686 20.6033 32.4079C20.537 32.3991 20.4987 32.3285 20.5265 32.2677C21.9545 29.1399 22.7502 25.6629 22.7502 22C22.7502 21.2828 22.7196 20.5727 22.6599 19.8709C22.6548 19.8113 22.7026 19.7603 22.7624 19.7623C23.8129 19.7961 24.8457 19.8996 25.8564 20.0683ZM21.2502 22C21.2502 21.2783 21.2173 20.5643 21.1529 19.8593C21.1481 19.8066 21.1028 19.7669 21.0499 19.7691C19.896 19.8154 18.7638 19.9459 17.6591 20.1545C17.6042 20.1649 17.5687 20.2185 17.5797 20.2734C17.6914 20.8316 17.75 21.4089 17.75 22C17.75 24.8434 16.3937 27.3701 14.2928 28.9683C14.2458 29.0041 14.2389 29.0724 14.2789 29.1159C15.5165 30.458 17.0996 31.4768 18.8871 32.0309C18.9342 32.0455 18.9847 32.0232 19.0059 31.9787C20.4448 28.955 21.2502 25.5716 21.2502 22Z" fill="#FD742E" />
                    </svg>',
                    'title' => 'Basketball',
                    'subtitle' => 'Fast and dynamic',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/basketball_1.png',
                            'alt' => 'Baseball', // Note: alt is 'Baseball' in original, but should be Basketball, fixing to Basketball
                            'title' => 'Buzzer-Beater Madness',
                            'text' => 'A last-second three-pointer stuns the crowd, sealing an unforgettable victory in the championship game.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/basketball_2.png',
                            'alt' => 'Basketball',
                            'title' => 'Ankle-Breaking Crossover',
                            'text' => 'A lightning-fast dribble move leaves the defender stumbling as the player drives to the basket with style.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/basketball_3.png',
                            'alt' => 'Basketball',
                            'title' => 'Dynasty in the Making',
                            'text' => 'A powerhouse team dominates the league, proving their chemistry and talent with an impressive winning streak.',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#D1F561" fill-opacity="0.1" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.8844 28.5595L20.2808 25.3365L16.5271 23.1692L16.0752 24.198L14.9903 26.852C15.4957 26.9572 16.0127 27.1506 16.5313 27.45C17.0829 27.7685 17.5297 28.1454 17.8844 28.5595ZM16.952 29.8135C16.6761 29.4098 16.2922 29.044 15.7813 28.749C15.3164 28.4807 14.8533 28.3327 14.4064 28.2802L14.4061 28.2809C13.1144 28.1295 11.9577 28.7758 11.2827 29.619C11.1102 29.8346 11.1943 30.1428 11.4334 30.2809L16.6296 33.2809C16.8687 33.419 17.1776 33.3377 17.2781 33.0805C17.6575 32.1085 17.653 30.8391 16.9518 29.8137L16.952 29.8135ZM27.182 16.0551L21.1819 24.1247L17.1339 21.7876L21.1834 12.5678C22.0197 10.6637 24.3248 9.90827 26.1259 10.9481C27.9342 11.9922 28.4279 14.3795 27.182 16.0551ZM32.0002 28.5C32.0002 30.433 30.4332 32 28.5002 32C26.5672 32 25.0002 30.433 25.0002 28.5C25.0002 26.567 26.5672 25 28.5002 25C30.4332 25 32.0002 26.567 32.0002 28.5Z" fill="#D1F561" />
                    </svg>',
                    'title' => 'Baseball',
                    'subtitle' => 'Hits and strategy',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/baseball_1.png',
                            'alt' => 'Baseball',
                            'title' => 'Walk-Off Home Run',
                            'text' => 'With the game on the line, a clutch swing sends the ball soaring into the stands, securing a dramatic victory.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/baseball_2.png',
                            'alt' => 'Baseball',
                            'title' => 'Golden Glove Play',
                            'text' => 'A fielder makes an impossible diving catch, robbing the batter of a sure hit and igniting the crowd.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/baseball_3.png',
                            'alt' => 'Baseball',
                            'title' => 'Rookie Sensation',
                            'text' => 'young star makes history with a record-breaking debut, proving to be the future of the sport.',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#F31D1D" fill-opacity="0.1" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9998 25.9996C15.0729 24.8434 12.4639 25.9111 11.9764 28.1048L11.5407 30.0657C11.2631 31.3148 12.2126 32.4996 13.4921 32.4996H16.9998C18.9402 32.4996 20.4196 32.1922 21.3527 31.9183C21.7325 31.8068 22.0695 31.6114 22.3605 31.3579C22.1289 30.8705 21.9993 30.3253 21.9993 29.7499C21.9993 27.7286 23.5984 26.0808 25.6006 26.0028L28.4596 20.9345C28.4036 20.9181 28.3486 20.8947 28.2959 20.8643L24.8473 18.8734L21.0581 25.3992C21.0367 25.436 21.0167 25.4734 20.9967 25.5108C20.9804 25.5413 20.964 25.5719 20.9469 25.6021C20.7061 26.0282 19.6099 27.5657 16.9998 25.9996ZM23.5001 29.7263L23.4994 29.7276C23.5071 28.928 23.932 28.2281 24.5669 27.8352L24.5664 27.8361C24.9102 27.623 25.3157 27.5 25.75 27.5H30.25C31.4926 27.5 32.5 28.5074 32.5 29.75C32.5 30.9926 31.4926 32 30.25 32H25.75C24.5074 32 23.5 30.9926 23.5 29.75C23.5 29.7421 23.5 29.7342 23.5001 29.7263ZM25.6005 17.5762L29.0458 19.5652C29.0941 19.5931 29.1381 19.6255 29.1776 19.6617L30.752 16.8707C30.7116 16.856 30.6719 16.8375 30.6334 16.8153L27.1949 14.8303L25.6005 17.5762ZM27.9481 13.5331L31.3833 15.5162C31.4174 15.5359 31.4494 15.5578 31.4791 15.5817L32.5226 13.7319C33.0632 12.7736 32.7282 11.5587 31.7729 11.0128L31.7243 10.985C30.77 10.4397 29.5544 10.7667 29.0025 11.7172L27.9481 13.5331Z" fill="#F31D1D" />
                    </svg>',
                    'title' => 'Hockey',
                    'subtitle' => 'Speed and impact',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/hockey_1.png',
                            'alt' => 'Hockey',
                            'title' => 'Overtime Thriller',
                            'text' => 'A heart-pounding sudden-death goal secures a crucial playoff victory in a game filled with intensity and grit.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/hockey_2.png',
                            'alt' => 'Hockey',
                            'title' => 'Glove Save Perfection',
                            'text' => 'A goalie pulls off an unbelievable reflex save, keeping their team alive in a high-stakes showdown.',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/sports/hockey_3.png',
                            'alt' => 'Hockey',
                            'title' => 'Rivalry on Ice',
                            'text' => 'Two long-time foes clash in an aggressive, fast-paced game that keeps fans on the edge of their seats.',
                        ],
                    ],
                ],
            ],
        ],
        [
            'title' => 'Opportunities',
            'type' => 'events',
            'categories' => [
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#D1F561" fill-opacity="0.1" />
                        <path d="M21.25 28.25H19C17.9 28.25 17 29.15 17 30.25V30.5H16C15.59 30.5 15.25 30.84 15.25 31.25C15.25 31.66 15.59 32 16 32H28C28.41 32 28.75 31.66 28.75 31.25C28.75 30.84 28.41 30.5 28 30.5H27V30.25C27 29.15 26.1 28.25 25 28.25H22.75V25.96C22.5 25.99 22.25 26 22 26C21.75 26 21.5 25.99 21.25 25.96V28.25Z" fill="#D1F561" />
                        <path d="M28.4798 21.64C29.1398 21.39 29.7198 20.98 30.1798 20.52C31.1098 19.49 31.7198 18.26 31.7198 16.82C31.7198 15.38 30.5898 14.25 29.1498 14.25H28.5898C27.9398 12.92 26.5798 12 24.9998 12H18.9998C17.4198 12 16.0598 12.92 15.4098 14.25H14.8498C13.4098 14.25 12.2798 15.38 12.2798 16.82C12.2798 18.26 12.8898 19.49 13.8198 20.52C14.2798 20.98 14.8598 21.39 15.5198 21.64C16.5598 24.2 19.0598 26 21.9998 26C24.9398 26 27.4398 24.2 28.4798 21.64ZM24.8398 18.45L24.2198 19.21C24.1198 19.32 24.0498 19.54 24.0598 19.69L24.1198 20.67C24.1598 21.27 23.7298 21.58 23.1698 21.36L22.2598 21C22.1198 20.95 21.8798 20.95 21.7398 21L20.8298 21.36C20.2698 21.58 19.8398 21.27 19.8798 20.67L19.9398 19.69C19.9498 19.54 19.8798 19.32 19.7798 19.21L19.1598 18.45C18.7698 17.99 18.9398 17.48 19.5198 17.33L20.4698 17.09C20.6198 17.05 20.7998 16.91 20.8798 16.78L21.4098 15.96C21.7398 15.45 22.2598 15.45 22.5898 15.96L23.1198 16.78C23.1998 16.91 23.3798 17.05 23.5298 17.09L24.4798 17.33C25.0598 17.48 25.2298 17.99 24.8398 18.45Z" fill="#D1F561" />
                    </svg>',
                    'title' => 'Tournaments',
                    'subtitle' => 'Top competitions ahead',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/basketball.png',
                            'alt' => 'Basketball',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.00294 1.01064C3.99235 0.988403 3.96708 0.97727 3.94356 0.984561C3.04982 1.26161 2.25826 1.77098 1.63946 2.44207C1.61944 2.46378 1.62288 2.49797 1.64638 2.51584C2.25907 2.98193 2.74511 3.60594 3.0438 4.32718C3.05301 4.34941 3.07658 4.36217 3.10018 4.35753C3.69277 4.24108 4.30052 4.16693 4.92018 4.13835C4.9501 4.13697 4.97223 4.10986 4.96732 4.08031C4.7877 2.99939 4.45896 1.96888 4.00294 1.01064ZM1.1115 3.11736C1.12712 3.09363 1.15944 3.08797 1.18211 3.10509C1.65766 3.46411 2.04196 3.93761 2.29401 4.4846C2.30687 4.51249 2.29121 4.54504 2.26156 4.55305C1.61311 4.72841 0.985769 4.95514 0.384181 5.22863C0.348136 5.24501 0.308123 5.21525 0.314288 5.17614C0.432663 4.42508 0.710288 3.72693 1.1115 3.11736ZM2.55837 5.30463C2.55292 5.27656 2.52479 5.25918 2.49717 5.26655C1.72324 5.4729 0.98108 5.75711 0.279595 6.11027C0.262393 6.11893 0.25173 6.13678 0.252292 6.15603C0.281636 7.16135 0.593604 8.09579 1.1115 8.88264C1.12712 8.90637 1.15945 8.91202 1.18212 8.8949C2.05854 8.23323 2.625 7.18277 2.625 6C2.625 5.76216 2.6021 5.52968 2.55837 5.30463ZM9.73887 2.98359C9.76113 2.96871 9.79113 2.97442 9.80647 2.99637C10.2519 3.63388 10.5595 4.3749 10.6857 5.17614C10.6919 5.21525 10.6519 5.24501 10.6159 5.22863C9.94593 4.92408 9.24408 4.6775 8.5167 4.4953C8.48619 4.48765 8.47004 4.45415 8.48371 4.42582C8.76649 3.84016 9.20214 3.34215 9.73887 2.98359ZM9.25742 2.33333C9.27902 2.35546 9.27512 2.39165 9.24976 2.40935C8.58067 2.87612 8.04816 3.52513 7.72418 4.28442C7.71496 4.30602 7.69209 4.31853 7.66896 4.31444C7.05267 4.20549 6.42063 4.14215 5.77646 4.12803C5.7521 4.12749 5.73161 4.10954 5.72787 4.08546C5.55221 2.95442 5.22338 1.87403 4.76326 0.866178C4.74937 0.835761 4.76849 0.800451 4.80163 0.796045C5.03011 0.765675 5.26323 0.75 5.50002 0.75C6.97284 0.75 8.30401 1.35648 9.25742 2.33333ZM9.73886 9.01641C9.76112 9.03128 9.79113 9.02558 9.80647 9.00363C10.3734 8.19234 10.7169 7.21339 10.7477 6.15603C10.7483 6.13678 10.7376 6.11893 10.7204 6.11027C9.94713 5.72095 9.1244 5.41543 8.26416 5.20562C8.23711 5.19903 8.20991 5.21591 8.20413 5.24314C8.15228 5.48725 8.125 5.74044 8.125 6C8.125 7.25802 8.76583 8.36636 9.73886 9.01641ZM7.42819 5.03417C7.45639 5.03888 7.47475 5.06631 7.46887 5.09429C7.40735 5.38651 7.375 5.68948 7.375 6C7.375 7.48661 8.11646 8.80004 9.24975 9.59065C9.27511 9.60834 9.27902 9.64454 9.25742 9.66667C8.30401 10.6435 6.97284 11.25 5.50002 11.25C5.26323 11.25 5.03011 11.2343 4.80163 11.204C4.76849 11.1995 4.74936 11.1642 4.76325 11.1338C5.47723 9.56995 5.87508 7.83144 5.87508 6C5.87508 5.64139 5.85982 5.28633 5.82993 4.93546C5.82739 4.90564 5.85131 4.88017 5.88122 4.88113C6.40643 4.89805 6.92283 4.94981 7.42819 5.03417ZM5.12508 6C5.12508 5.63914 5.10863 5.28213 5.07646 4.92966C5.07405 4.9033 5.05142 4.88347 5.02497 4.88453C4.44802 4.90772 3.88189 4.97296 3.32957 5.07727C3.30208 5.08246 3.28435 5.10926 3.28984 5.13669C3.3457 5.41578 3.375 5.70447 3.375 6C3.375 7.42172 2.69685 8.68504 1.64639 9.48415C1.62289 9.50203 1.61944 9.53622 1.63946 9.55793C2.25826 10.229 3.04982 10.7384 3.94356 11.0154C3.96708 11.0227 3.99235 11.0116 4.00293 10.9894C4.72242 9.47752 5.12508 7.78578 5.12508 6Z" fill="#FD742E" />
                            </svg>',
                            'category_name' => 'Basketball',
                            'title' => 'Slam Dunk Showdown',
                            'text' => 'An electrifying one-on-one battle, featuring high-flying dunks and precision shots, as top players compete for the championship title.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/tennis.png',
                            'alt' => 'Tennis',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 13 12" fill="none">
                                <path d="M6.47551 6.02572C7.54945 7.09966 9.44895 6.94137 10.7182 5.67217C11.9874 4.40297 12.1456 2.50347 11.0717 1.42953C9.99776 0.355587 8.09827 0.513878 6.82906 1.78308C5.55986 3.05229 5.40157 4.95178 6.47551 6.02572ZM6.47551 6.02572L4.17745 8.32361M7.71749 1.24817L11.253 4.7837M6.30328 2.66238L9.83881 6.19792M11.3413 2.39709L7.45218 6.28618M10.1038 1.15966L6.21475 5.04874M3.52194 8.41331L3.27445 8.44866C3.06026 8.47926 2.86176 8.57851 2.70877 8.7315L1.35348 10.0868C1.15822 10.2821 1.15822 10.5986 1.35348 10.7939L1.70703 11.1475C1.90229 11.3427 2.21888 11.3427 2.41414 11.1475L3.76943 9.79216C3.92242 9.63917 4.02167 9.44068 4.05227 9.22648L4.08762 8.97899C4.13476 8.64901 3.85192 8.36617 3.52194 8.41331Z" stroke="#47A9D6" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>',
                            'category_name' => 'Tennis',
                            'title' => 'Grand Slam Preparation',
                            'text' => 'Aspiring champions undergo intensive training, focusing on strategy, endurance, and match-play scenarios to compete at the highest level.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#05D670" fill-opacity="0.1" />
                        <path d="M22 25C25.7279 25 28.75 22.0899 28.75 18.5C28.75 14.9101 25.7279 12 22 12C18.2721 12 15.25 14.9101 15.25 18.5C15.25 22.0899 18.2721 25 22 25Z" fill="#05D670" />
                        <path d="M25.79 25.61C26.12 25.44 26.5 25.69 26.5 26.06V30.91C26.5 31.81 25.87 32.25 25.09 31.88L22.41 30.61C22.18 30.51 21.82 30.51 21.59 30.61L18.91 31.88C18.13 32.24 17.5 31.8 17.5 30.9L17.52 26.06C17.52 25.69 17.91 25.45 18.23 25.61C19.36 26.18 20.64 26.5 22 26.5C23.36 26.5 24.65 26.18 25.79 25.61Z" fill="#05D670" />
                    </svg>',
                    'title' => 'Training Camps',
                    'subtitle' => 'Train with the best',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/basketball_2.png',
                            'alt' => 'Basketball',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.00294 1.01064C3.99235 0.988403 3.96708 0.97727 3.94356 0.984561C3.04982 1.26161 2.25826 1.77098 1.63946 2.44207C1.61944 2.46378 1.62288 2.49797 1.64638 2.51584C2.25907 2.98193 2.74511 3.60594 3.0438 4.32718C3.05301 4.34941 3.07658 4.36217 3.10018 4.35753C3.69277 4.24108 4.30052 4.16693 4.92018 4.13835C4.9501 4.13697 4.97223 4.10986 4.96732 4.08031C4.7877 2.99939 4.45896 1.96888 4.00294 1.01064ZM1.1115 3.11736C1.12712 3.09363 1.15944 3.08797 1.18211 3.10509C1.65766 3.46411 2.04196 3.93761 2.29401 4.4846C2.30687 4.51249 2.29121 4.54504 2.26156 4.55305C1.61311 4.72841 0.985769 4.95514 0.384181 5.22863C0.348136 5.24501 0.308123 5.21525 0.314288 5.17614C0.432663 4.42508 0.710288 3.72693 1.1115 3.11736ZM2.55837 5.30463C2.55292 5.27656 2.52479 5.25918 2.49717 5.26655C1.72324 5.4729 0.98108 5.75711 0.279595 6.11027C0.262393 6.11893 0.25173 6.13678 0.252292 6.15603C0.281636 7.16135 0.593604 8.09579 1.1115 8.88264C1.12712 8.90637 1.15945 8.91202 1.18212 8.8949C2.05854 8.23323 2.625 7.18277 2.625 6C2.625 5.76216 2.6021 5.52968 2.55837 5.30463ZM9.73887 2.98359C9.76113 2.96871 9.79113 2.97442 9.80647 2.99637C10.2519 3.63388 10.5595 4.3749 10.6857 5.17614C10.6919 5.21525 10.6519 5.24501 10.6159 5.22863C9.94593 4.92408 9.24408 4.6775 8.5167 4.4953C8.48619 4.48765 8.47004 4.45415 8.48371 4.42582C8.76649 3.84016 9.20214 3.34215 9.73887 2.98359ZM9.25742 2.33333C9.27902 2.35546 9.27512 2.39165 9.24976 2.40935C8.58067 2.87612 8.04816 3.52513 7.72418 4.28442C7.71496 4.30602 7.69209 4.31853 7.66896 4.31444C7.05267 4.20549 6.42063 4.14215 5.77646 4.12803C5.7521 4.12749 5.73161 4.10954 5.72787 4.08546C5.55221 2.95442 5.22338 1.87403 4.76326 0.866178C4.74937 0.835761 4.76849 0.800451 4.80163 0.796045C5.03011 0.765675 5.26323 0.75 5.50002 0.75C6.97284 0.75 8.30401 1.35648 9.25742 2.33333ZM9.73886 9.01641C9.76112 9.03128 9.79113 9.02558 9.80647 9.00363C10.3734 8.19234 10.7169 7.21339 10.7477 6.15603C10.7483 6.13678 10.7376 6.11893 10.7204 6.11027C9.94713 5.72095 9.1244 5.41543 8.26416 5.20562C8.23711 5.19903 8.20991 5.21591 8.20413 5.24314C8.15228 5.48725 8.125 5.74044 8.125 6C8.125 7.25802 8.76583 8.36636 9.73886 9.01641ZM7.42819 5.03417C7.45639 5.03888 7.47475 5.06631 7.46887 5.09429C7.40735 5.38651 7.375 5.68948 7.375 6C7.375 7.48661 8.11646 8.80004 9.24975 9.59065C9.27511 9.60834 9.27902 9.64454 9.25742 9.66667C8.30401 10.6435 6.97284 11.25 5.50002 11.25C5.26323 11.25 5.03011 11.2343 4.80163 11.204C4.76849 11.1995 4.74936 11.1642 4.76325 11.1338C5.47723 9.56995 5.87508 7.83144 5.87508 6C5.87508 5.64139 5.85982 5.28633 5.82993 4.93546C5.82739 4.90564 5.85131 4.88017 5.88122 4.88113C6.40643 4.89805 6.92283 4.94981 7.42819 5.03417ZM5.12508 6C5.12508 5.63914 5.10863 5.28213 5.07646 4.92966C5.07405 4.9033 5.05142 4.88347 5.02497 4.88453C4.44802 4.90772 3.88189 4.97296 3.32957 5.07727C3.30208 5.08246 3.28435 5.10926 3.28984 5.13669C3.3457 5.41578 3.375 5.70447 3.375 6C3.375 7.42172 2.69685 8.68504 1.64639 9.48415C1.62289 9.50203 1.61944 9.53622 1.63946 9.55793C2.25826 10.229 3.04982 10.7384 3.94356 11.0154C3.96708 11.0227 3.99235 11.0116 4.00293 10.9894C4.72242 9.47752 5.12508 7.78578 5.12508 6Z" fill="#FD742E" />
                            </svg>',
                            'category_name' => 'Basketball',
                            'title' => 'Elite Skills Camp',
                            'text' => 'Future stars sharpen their shooting, dribbling, and defensive skills under the guidance of top coaches.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/tennis_2.png',
                            'alt' => 'Tennis',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 13 12" fill="none">
                                <path d="M6.47551 6.02572C7.54945 7.09966 9.44895 6.94137 10.7182 5.67217C11.9874 4.40297 12.1456 2.50347 11.0717 1.42953C9.99776 0.355587 8.09827 0.513878 6.82906 1.78308C5.55986 3.05229 5.40157 4.95178 6.47551 6.02572ZM6.47551 6.02572L4.17745 8.32361M7.71749 1.24817L11.253 4.7837M6.30328 2.66238L9.83881 6.19792M11.3413 2.39709L7.45218 6.28618M10.1038 1.15966L6.21475 5.04874M3.52194 8.41331L3.27445 8.44866C3.06026 8.47926 2.86176 8.57851 2.70877 8.7315L1.35348 10.0868C1.15822 10.2821 1.15822 10.5986 1.35348 10.7939L1.70703 11.1475C1.90229 11.3427 2.21888 11.3427 2.41414 11.1475L3.76943 9.79216C3.92242 9.63917 4.02167 9.44068 4.05227 9.22648L4.08762 8.97899C4.13476 8.64901 3.85192 8.36617 3.52194 8.41331Z" stroke="#47A9D6" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>',
                            'category_name' => 'Tennis',
                            'title' => 'Pro-Level Training',
                            'text' => 'Players refine their strokes, footwork, and mental game with expert drills designed to elevate their performance.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#FD742E" fill-opacity="0.1" />
                        <path d="M27.53 17.77C27.46 17.76 27.39 17.76 27.32 17.77C25.77 17.72 24.54 16.45 24.54 14.89C24.54 13.3 25.83 12 27.43 12C29.02 12 30.32 13.29 30.32 14.89C30.31 16.45 29.08 17.72 27.53 17.77Z" fill="#FD742E" />
                        <path d="M30.7901 24.7C29.6701 25.45 28.1001 25.73 26.6501 25.54C27.0301 24.72 27.2301 23.81 27.2401 22.85C27.2401 21.85 27.0201 20.9 26.6001 20.07C28.0801 19.87 29.6501 20.15 30.7801 20.9C32.3601 21.94 32.3601 23.65 30.7901 24.7Z" fill="#FD742E" />
                        <path d="M16.4402 17.77C16.5102 17.76 16.5802 17.76 16.6502 17.77C18.2002 17.72 19.4302 16.45 19.4302 14.89C19.4302 13.29 18.1402 12 16.5402 12C14.9502 12 13.6602 13.29 13.6602 14.89C13.6602 16.45 14.8902 17.72 16.4402 17.77Z" fill="#FD742E" />
                        <path d="M16.5501 22.85C16.5501 23.82 16.7601 24.74 17.1401 25.57C15.7301 25.72 14.2601 25.42 13.1801 24.71C11.6001 23.66 11.6001 21.95 13.1801 20.9C14.2501 20.18 15.7601 19.89 17.1801 20.05C16.7701 20.89 16.5501 21.84 16.5501 22.85Z" fill="#FD742E" />
                        <path d="M22.1198 25.87C22.0398 25.86 21.9498 25.86 21.8598 25.87C20.0198 25.81 18.5498 24.3 18.5498 22.44C18.5598 20.54 20.0898 19 21.9998 19C23.8998 19 25.4398 20.54 25.4398 22.44C25.4298 24.3 23.9698 25.81 22.1198 25.87Z" fill="#FD742E" />
                        <path d="M18.8698 27.94C17.3598 28.95 17.3598 30.61 18.8698 31.61C20.5898 32.76 23.4098 32.76 25.1298 31.61C26.6398 30.6 26.6398 28.94 25.1298 27.94C23.4198 26.79 20.5998 26.79 18.8698 27.94Z" fill="#FD742E" />
                    </svg>',
                    'title' => 'Meetups',
                    'subtitle' => 'Join the sports community',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/basketball_3.png',
                            'alt' => 'Basketball',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.00294 1.01064C3.99235 0.988403 3.96708 0.97727 3.94356 0.984561C3.04982 1.26161 2.25826 1.77098 1.63946 2.44207C1.61944 2.46378 1.62288 2.49797 1.64638 2.51584C2.25907 2.98193 2.74511 3.60594 3.0438 4.32718C3.05301 4.34941 3.07658 4.36217 3.10018 4.35753C3.69277 4.24108 4.30052 4.16693 4.92018 4.13835C4.9501 4.13697 4.97223 4.10986 4.96732 4.08031C4.7877 2.99939 4.45896 1.96888 4.00294 1.01064ZM1.1115 3.11736C1.12712 3.09363 1.15944 3.08797 1.18211 3.10509C1.65766 3.46411 2.04196 3.93761 2.29401 4.4846C2.30687 4.51249 2.29121 4.54504 2.26156 4.55305C1.61311 4.72841 0.985769 4.95514 0.384181 5.22863C0.348136 5.24501 0.308123 5.21525 0.314288 5.17614C0.432663 4.42508 0.710288 3.72693 1.1115 3.11736ZM2.55837 5.30463C2.55292 5.27656 2.52479 5.25918 2.49717 5.26655C1.72324 5.4729 0.98108 5.75711 0.279595 6.11027C0.262393 6.11893 0.25173 6.13678 0.252292 6.15603C0.281636 7.16135 0.593604 8.09579 1.1115 8.88264C1.12712 8.90637 1.15945 8.91202 1.18212 8.8949C2.05854 8.23323 2.625 7.18277 2.625 6C2.625 5.76216 2.6021 5.52968 2.55837 5.30463ZM9.73887 2.98359C9.76113 2.96871 9.79113 2.97442 9.80647 2.99637C10.2519 3.63388 10.5595 4.3749 10.6857 5.17614C10.6919 5.21525 10.6519 5.24501 10.6159 5.22863C9.94593 4.92408 9.24408 4.6775 8.5167 4.4953C8.48619 4.48765 8.47004 4.45415 8.48371 4.42582C8.76649 3.84016 9.20214 3.34215 9.73887 2.98359ZM9.25742 2.33333C9.27902 2.35546 9.27512 2.39165 9.24976 2.40935C8.58067 2.87612 8.04816 3.52513 7.72418 4.28442C7.71496 4.30602 7.69209 4.31853 7.66896 4.31444C7.05267 4.20549 6.42063 4.14215 5.77646 4.12803C5.7521 4.12749 5.73161 4.10954 5.72787 4.08546C5.55221 2.95442 5.22338 1.87403 4.76326 0.866178C4.74937 0.835761 4.76849 0.800451 4.80163 0.796045C5.03011 0.765675 5.26323 0.75 5.50002 0.75C6.97284 0.75 8.30401 1.35648 9.25742 2.33333ZM9.73886 9.01641C9.76112 9.03128 9.79113 9.02558 9.80647 9.00363C10.3734 8.19234 10.7169 7.21339 10.7477 6.15603C10.7483 6.13678 10.7376 6.11893 10.7204 6.11027C9.94713 5.72095 9.1244 5.41543 8.26416 5.20562C8.23711 5.19903 8.20991 5.21591 8.20413 5.24314C8.15228 5.48725 8.125 5.74044 8.125 6C8.125 7.25802 8.76583 8.36636 9.73886 9.01641ZM7.42819 5.03417C7.45639 5.03888 7.47475 5.06631 7.46887 5.09429C7.40735 5.38651 7.375 5.68948 7.375 6C7.375 7.48661 8.11646 8.80004 9.24975 9.59065C9.27511 9.60834 9.27902 9.64454 9.25742 9.66667C8.30401 10.6435 6.97284 11.25 5.50002 11.25C5.26323 11.25 5.03011 11.2343 4.80163 11.204C4.76849 11.1995 4.74936 11.1642 4.76325 11.1338C5.47723 9.56995 5.87508 7.83144 5.87508 6C5.87508 5.64139 5.85982 5.28633 5.82993 4.93546C5.82739 4.90564 5.85131 4.88017 5.88122 4.88113C6.40643 4.89805 6.92283 4.94981 7.42819 5.03417ZM5.12508 6C5.12508 5.63914 5.10863 5.28213 5.07646 4.92966C5.07405 4.9033 5.05142 4.88347 5.02497 4.88453C4.44802 4.90772 3.88189 4.97296 3.32957 5.07727C3.30208 5.08246 3.28435 5.10926 3.28984 5.13669C3.3457 5.41578 3.375 5.70447 3.375 6C3.375 7.42172 2.69685 8.68504 1.64639 9.48415C1.62289 9.50203 1.61944 9.53622 1.63946 9.55793C2.25826 10.229 3.04982 10.7384 3.94356 11.0154C3.96708 11.0227 3.99235 11.0116 4.00293 10.9894C4.72242 9.47752 5.12508 7.78578 5.12508 6Z" fill="#FD742E" />
                            </svg>',
                            'category_name' => 'Basketball',
                            'title' => 'Community Hoops Night',
                            'text' => 'A casual yet competitive gathering where players of all skill levels come together for friendly matchups.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/tennis_3.png',
                            'alt' => 'Tennis',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 13 12" fill="none">
                                <path d="M6.47551 6.02572C7.54945 7.09966 9.44895 6.94137 10.7182 5.67217C11.9874 4.40297 12.1456 2.50347 11.0717 1.42953C9.99776 0.355587 8.09827 0.513878 6.82906 1.78308C5.55986 3.05229 5.40157 4.95178 6.47551 6.02572ZM6.47551 6.02572L4.17745 8.32361M7.71749 1.24817L11.253 4.7837M6.30328 2.66238L9.83881 6.19792M11.3413 2.39709L7.45218 6.28618M10.1038 1.15966L6.21475 5.04874M3.52194 8.41331L3.27445 8.44866C3.06026 8.47926 2.86176 8.57851 2.70877 8.7315L1.35348 10.0868C1.15822 10.2821 1.15822 10.5986 1.35348 10.7939L1.70703 11.1475C1.90229 11.3427 2.21888 11.3427 2.41414 11.1475L3.76943 9.79216C3.92242 9.63917 4.02167 9.44068 4.05227 9.22648L4.08762 8.97899C4.13476 8.64901 3.85192 8.36617 3.52194 8.41331Z" stroke="#47A9D6" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>',
                            'category_name' => 'Tennis',
                            'title' => 'Rally & Social',
                            'text' => 'Tennis enthusiasts meet for engaging matches and networking, building connections through a shared passion for the sport.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                    ],
                ],
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                        <rect width="44" height="44" rx="10" fill="#EF396A" fill-opacity="0.1" />
                        <path d="M26.44 13.1C24.63 13.1 23.01 13.98 22 15.33C20.99 13.98 19.37 13.1 17.56 13.1C14.49 13.1 12 15.6 12 18.69C12 19.88 12.19 20.98 12.52 22C14.1 27 18.97 29.99 21.38 30.81C21.72 30.93 22.28 30.93 22.62 30.81C25.03 29.99 29.9 27 31.48 22C31.81 20.98 32 19.88 32 18.69C32 15.6 29.51 13.1 26.44 13.1Z" fill="#EF396A" />
                    </svg>',
                    'title' => 'Charity Matches',
                    'subtitle' => 'Play for a cause',
                    'items' => [
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/basketball_4.png',
                            'alt' => 'Basketball',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.00294 1.01064C3.99235 0.988403 3.96708 0.97727 3.94356 0.984561C3.04982 1.26161 2.25826 1.77098 1.63946 2.44207C1.61944 2.46378 1.62288 2.49797 1.64638 2.51584C2.25907 2.98193 2.74511 3.60594 3.0438 4.32718C3.05301 4.34941 3.07658 4.36217 3.10018 4.35753C3.69277 4.24108 4.30052 4.16693 4.92018 4.13835C4.9501 4.13697 4.97223 4.10986 4.96732 4.08031C4.7877 2.99939 4.45896 1.96888 4.00294 1.01064ZM1.1115 3.11736C1.12712 3.09363 1.15944 3.08797 1.18211 3.10509C1.65766 3.46411 2.04196 3.93761 2.29401 4.4846C2.30687 4.51249 2.29121 4.54504 2.26156 4.55305C1.61311 4.72841 0.985769 4.95514 0.384181 5.22863C0.348136 5.24501 0.308123 5.21525 0.314288 5.17614C0.432663 4.42508 0.710288 3.72693 1.1115 3.11736ZM2.55837 5.30463C2.55292 5.27656 2.52479 5.25918 2.49717 5.26655C1.72324 5.4729 0.98108 5.75711 0.279595 6.11027C0.262393 6.11893 0.25173 6.13678 0.252292 6.15603C0.281636 7.16135 0.593604 8.09579 1.1115 8.88264C1.12712 8.90637 1.15945 8.91202 1.18212 8.8949C2.05854 8.23323 2.625 7.18277 2.625 6C2.625 5.76216 2.6021 5.52968 2.55837 5.30463ZM9.73887 2.98359C9.76113 2.96871 9.79113 2.97442 9.80647 2.99637C10.2519 3.63388 10.5595 4.3749 10.6857 5.17614C10.6919 5.21525 10.6519 5.24501 10.6159 5.22863C9.94593 4.92408 9.24408 4.6775 8.5167 4.4953C8.48619 4.48765 8.47004 4.45415 8.48371 4.42582C8.76649 3.84016 9.20214 3.34215 9.73887 2.98359ZM9.25742 2.33333C9.27902 2.35546 9.27512 2.39165 9.24976 2.40935C8.58067 2.87612 8.04816 3.52513 7.72418 4.28442C7.71496 4.30602 7.69209 4.31853 7.66896 4.31444C7.05267 4.20549 6.42063 4.14215 5.77646 4.12803C5.7521 4.12749 5.73161 4.10954 5.72787 4.08546C5.55221 2.95442 5.22338 1.87403 4.76326 0.866178C4.74937 0.835761 4.76849 0.800451 4.80163 0.796045C5.03011 0.765675 5.26323 0.75 5.50002 0.75C6.97284 0.75 8.30401 1.35648 9.25742 2.33333ZM9.73886 9.01641C9.76112 9.03128 9.79113 9.02558 9.80647 9.00363C10.3734 8.19234 10.7169 7.21339 10.7477 6.15603C10.7483 6.13678 10.7376 6.11893 10.7204 6.11027C9.94713 5.72095 9.1244 5.41543 8.26416 5.20562C8.23711 5.19903 8.20991 5.21591 8.20413 5.24314C8.15228 5.48725 8.125 5.74044 8.125 6C8.125 7.25802 8.76583 8.36636 9.73886 9.01641ZM7.42819 5.03417C7.45639 5.03888 7.47475 5.06631 7.46887 5.09429C7.40735 5.38651 7.375 5.68948 7.375 6C7.375 7.48661 8.11646 8.80004 9.24975 9.59065C9.27511 9.60834 9.27902 9.64454 9.25742 9.66667C8.30401 10.6435 6.97284 11.25 5.50002 11.25C5.26323 11.25 5.03011 11.2343 4.80163 11.204C4.76849 11.1995 4.74936 11.1642 4.76325 11.1338C5.47723 9.56995 5.87508 7.83144 5.87508 6C5.87508 5.64139 5.85982 5.28633 5.82993 4.93546C5.82739 4.90564 5.85131 4.88017 5.88122 4.88113C6.40643 4.89805 6.92283 4.94981 7.42819 5.03417ZM5.12508 6C5.12508 5.63914 5.10863 5.28213 5.07646 4.92966C5.07405 4.9033 5.05142 4.88347 5.02497 4.88453C4.44802 4.90772 3.88189 4.97296 3.32957 5.07727C3.30208 5.08246 3.28435 5.10926 3.28984 5.13669C3.3457 5.41578 3.375 5.70447 3.375 6C3.375 7.42172 2.69685 8.68504 1.64639 9.48415C1.62289 9.50203 1.61944 9.53622 1.63946 9.55793C2.25826 10.229 3.04982 10.7384 3.94356 11.0154C3.96708 11.0227 3.99235 11.0116 4.00293 10.9894C4.72242 9.47752 5.12508 7.78578 5.12508 6Z" fill="#FD742E" />
                            </svg>',
                            'category_name' => 'Basketball',
                            'title' => 'Hoops for Hope',
                            'text' => 'A star-studded charity game brings fans together to support a great cause, featuring thrilling dunks and fast-paced action.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                        [
                            'img' => 'https://bato-web-agency.github.io/bato-shared/img/mega-menu/events/tennis_4.png',
                            'alt' => 'Tennis',
                            'category_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 13 12" fill="none">
                                <path d="M6.47551 6.02572C7.54945 7.09966 9.44895 6.94137 10.7182 5.67217C11.9874 4.40297 12.1456 2.50347 11.0717 1.42953C9.99776 0.355587 8.09827 0.513878 6.82906 1.78308C5.55986 3.05229 5.40157 4.95178 6.47551 6.02572ZM6.47551 6.02572L4.17745 8.32361M7.71749 1.24817L11.253 4.7837M6.30328 2.66238L9.83881 6.19792M11.3413 2.39709L7.45218 6.28618M10.1038 1.15966L6.21475 5.04874M3.52194 8.41331L3.27445 8.44866C3.06026 8.47926 2.86176 8.57851 2.70877 8.7315L1.35348 10.0868C1.15822 10.2821 1.15822 10.5986 1.35348 10.7939L1.70703 11.1475C1.90229 11.3427 2.21888 11.3427 2.41414 11.1475L3.76943 9.79216C3.92242 9.63917 4.02167 9.44068 4.05227 9.22648L4.08762 8.97899C4.13476 8.64901 3.85192 8.36617 3.52194 8.41331Z" stroke="#47A9D6" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>',
                            'category_name' => 'Tennis',
                            'title' => 'Aces for Awareness',
                            'text' => 'Tennis pros take the court in a special exhibition match, raising funds and awareness for an important cause.',
                            'address' => 'Downtown Sports Arena',
                            'date' => 'March 15, 2025 | 6:30 PM',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $otherLinks = [
        'Shop',
        'Fondateur',
        'Expertise',
        'Contact',
    ];
@endphp

<section class="base-template">
    <div class="wrapper base-template__wrapper">
        <div class="base-template__content">
            <header class="header">
                <div class="header__wrapper" style="display: flex; align-items: center; min-width: 0;">
                    <a href="/" target="_blank" class="header__logo" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; white-space: nowrap;">
                        <img loading="lazy" src="https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png" alt="Logo" style="height: 28px; flex-shrink: 0;">
                        <span style="font-family: 'Poppins', sans-serif; font-weight: 500; font-size: 18px; line-height: 1;">
                            Numerimondes
                        </span>
                    </a>
                    <div class="header__navigation-wrapper">
                        <nav class="header__navigation">
                            <ul class="header__list">
                                @foreach ($menus as $menu)
                                    <li class="header__list-item has-submenu">
                                        <a href="#">
                                            <span>{{ $menu['title'] }}</span>
                                            <span class="menu-icon menu-icon--chevron"><x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4 flex-shrink-0" /></span>
                                            <span class="menu-icon menu-icon--open"><x-filament::icon icon="heroicon-m-chevron-up-down" class="w-4 h-4 flex-shrink-0" /></span>
                                            <span class="menu-icon menu-icon--close"><x-filament::icon icon="heroicon-m-x-mark" class="w-4 h-4 flex-shrink-0" /></span>
                                        </a>
                                        <div class="submenu-wrapper">
                                            <div class="submenu-list__wrapper">
                                                <div class="submenu-list__title">Categories</div>
                                                <ul class="submenu-list">
                                                    @foreach ($menu['categories'] as $index => $category)
                                                        <li class="submenu-list__item has-submenu {{ $index === 0 ? 'active' : '' }}">
                                                            <div class="submenu-list__item-wrapper">
                                                                <div class="submenu-list__item-icon">
                                                                    {!! $category['icon'] !!}
                                                                </div>
                                                                <a href="#" class="submenu-list__item-link">
                                                                    <span class="submenu-list__item-title">{{ $category['title'] }}</span>
                                                                    <span class="submenu-list__item-subtile">{{ $category['subtitle'] }}</span>
                                                                </a>
                                                                <x-filament::icon icon="heroicon-m-arrow-right" class="w-4 h-4" />
                                                            </div>
                                                            <div class="submenu-content">
                                                                <div class="submenu-content__title">{{ $menu['type'] === 'sports' ? 'Latest News' : 'Future Events' }}</div>
                                                                <ul class="submenu-content__list {{ $menu['type'] === 'events' ? 'events' : '' }}">
                                                                    @foreach ($category['items'] as $item)
                                                                        <li class="submenu-content__list-item">
                                                                            @if ($menu['type'] === 'sports')
                                                                                <a href="#" class="submenu-content__link">
                                                                                    <div class="submenu-content__link-img">
                                                                                        <img loading="lazy" src="{{ $item['img'] }}" alt="{{ $item['alt'] }}">
                                                                                    </div>
                                                                                    <div class="submenu-content__link-title">{{ $item['title'] }}</div>
                                                                                    <div class="submenu-content__link-text">
                                                                                        {{ $item['text'] }}
                                                                                    </div>
                                                                                </a>
                                                                            @else
                                                                                <div class="submenu-content__link-wrapper">
                                                                                    <div class="submenu-content__link-img">
                                                                                        <img loading="lazy" src="{{ $item['img'] }}" alt="{{ $item['alt'] }}">
                                                                                    </div>
                                                                                    <div class="submenu-content__info">
                                                                                        <div class="submenu-content__category">
                                                                                            {!! $item['category_icon'] !!}
                                                                                            <span>{{ $item['category_name'] }}</span>
                                                                                        </div>
                                                                                        <div class="submenu-content__link-title">{{ $item['title'] }}</div>
                                                                                        <div class="submenu-content__link-text">
                                                                                            {{ $item['text'] }}
                                                                                        </div>
                                                                                        <div class="submenu-content__link-address">
                                                                                            <img loading="lazy" src="https://bato-web-agency.github.io/bato-shared/img/mega-menu/location.svg" alt="Location">
                                                                                            <span>{{ $item['address'] }}</span>
                                                                                        </div>
                                                                                        <div class="submenu-content__link-date">
                                                                                            <img loading="lazy" src="https://bato-web-agency.github.io/bato-shared/img/mega-menu/calendar.svg" alt="Calendar">
                                                                                            <span>{{ $item['date'] }}</span>
                                                                                        </div>
                                                                                        <a href="#" class="submenu-content__url">
                                                                                            <span>Explore More</span>
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                                                                                                <path d="M0.5 6.99996H15.5M15.5 6.99996L9.66667 1.16663M15.5 6.99996L9.66667 12.8333" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                                                                            </svg>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <span style="text-muted"></span>
                                        </div>
                                    </li>
                                @endforeach
                                @foreach ($otherLinks as $link)
                                    <li class="header__list-item">
                                        <a href="#">{{ $link }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                        <div class="header__buttons-wrapper">
                            <a href="#" class="header__button" style="display: flex; align-items: center; justify-content: center; min-height: 42px; width: max-content; padding: 6px 20px; border-radius: 100px; gap: 8px; font-size: 16px; font-weight: 400; transition: 0.4s;">
                                <span>Get a Demo</span>
                                <x-filament::icon icon="heroicon-o-arrow-right" class="h-5 w-5 align-middle" style="vertical-align: middle;" />
                            </a>
                            <a href="#" class="header__button" style="display: flex; align-items: center; justify-content: center; min-height: 42px; width: max-content; padding: 6px 20px; border-radius: 100px; gap: 8px; font-size: 16px; font-weight: 400; transition: 0.4s;" onmouseover="this.querySelector('svg').style.transform='rotate(-40deg';" onmouseout="this.querySelector('svg').style.transform='rotate(0deg);">
                                <span>Join a Sport</span>
                                <span style="display: inline-block; vertical-align: middle; transition: transform 0.3s ease;">
                                    <x-filament::icon icon="heroicon-o-arrow-right" class="h-5 w-5 align-middle" style="vertical-align: middle; transition: transform 0.3s ease;" />
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="header__burger"><i></i><i></i><i></i></div>
                </div>
            </header>
        </div>
    </div>
</section>
<script>
    window.addEventListener("DOMContentLoaded", () => {
        const burger = document.querySelector(".header__burger");
        const navWrapper = document.querySelector(".header__navigation-wrapper");

        if (burger && navWrapper) {
            burger.addEventListener("click", () => {
                burger.classList.toggle("active");
                navWrapper.classList.toggle("open");
            });
        }

        // Layout multiple open mega menus under the sticky header
        const layoutOpenMegaMenus = () => {
            if (window.innerWidth <= 1025) {
                // Reset inline styles on mobile
                document.querySelectorAll('.submenu-wrapper').forEach(wrapper => {
                    wrapper.style.position = '';
                    wrapper.style.left = '';
                    wrapper.style.right = '';
                    wrapper.style.top = '';
                    wrapper.style.width = '';
                    wrapper.style.transform = '';
                });
                return;
            }
            const headerEl = document.querySelector('.header');
            if (!headerEl) return;
            const baseTop = Math.round(headerEl.getBoundingClientRect().bottom);
            let yOffset = 0;
            const openWrappers = document.querySelectorAll('.header__list-item.has-submenu.open .submenu-wrapper:not(.full)');
            openWrappers.forEach(wrapper => {
                wrapper.style.position = 'fixed';
                wrapper.style.left = '50%';
                wrapper.style.right = '';
                wrapper.style.transform = 'translateX(-50%)';
                wrapper.style.width = '100vw';
                wrapper.style.top = `${baseTop + yOffset}px`;
                const h = Math.round(wrapper.getBoundingClientRect().height || 0);
                yOffset += h;
            });
        };

        document.querySelectorAll(".header__list-item.has-submenu").forEach((item) => {
            const submenuWrapper = item.querySelector(".submenu-wrapper");
            const trigger = item.querySelector(':scope > a');
            let closeTimeout;
            let hoverOpenTimeout;

            const open = () => {
                // Close other top-level mega menus before opening this one
                document.querySelectorAll('.header__list-item.has-submenu.open').forEach(i => {
                    if (i !== item) {
                        i.classList.remove('open','pinned');
                    }
                });
                item.classList.add("open");
                document.body.classList.toggle('mm-open', !!document.querySelector('.header__list-item.open'));
                layoutOpenMegaMenus();
            };

            const cancelClose = () => {
                if (closeTimeout) {
                    clearTimeout(closeTimeout);
                    closeTimeout = null;
                }
            };

            const scheduleClose = () => {
                cancelClose();
                closeTimeout = setTimeout(() => {
                    if (!item.classList.contains("pinned")) {
                        item.classList.remove("open");
                        layoutOpenMegaMenus();
                        if (!document.querySelector('.header__list-item.open')) {
                            document.body.classList.remove('mm-open');
                        }
                    }
                }, 200);
            };

            if (trigger) {
                // Desktop: only open when hovering the trigger area
                trigger.addEventListener("mouseenter", () => {
                    if (window.innerWidth > 1025) {
                        cancelClose();
                        clearTimeout(hoverOpenTimeout);
                        hoverOpenTimeout = setTimeout(() => {
                            if (!item.classList.contains("pinned")) open();
                        }, 200);
                    }
                });

                trigger.addEventListener("mouseleave", () => {
                    if (window.innerWidth > 1025) {
                        clearTimeout(hoverOpenTimeout);
                        scheduleClose();
                    }
                });

                // Desktop: single-click to pin if open (hover-open) or open+pin; single-click to unpin+close if pinned
                trigger.addEventListener("click", (e) => {
                    if (window.innerWidth > 1025) {
                        e.preventDefault();
                        // If pinned -> unpin and close
                        if (item.classList.contains('pinned')) {
                            item.classList.remove('open','pinned');
                            document.body.classList.toggle('mm-open', !!document.querySelector('.header__list-item.open'));
                            layoutOpenMegaMenus();
                            return;
                        }
                        // If already open (hovered) but not pinned -> close others then pin this one
                        if (item.classList.contains('open')) {
                            document.querySelectorAll('.header__list-item.has-submenu.open').forEach(i => {
                                if (i !== item) i.classList.remove('open','pinned');
                            });
                            item.classList.add('pinned');
                            document.body.classList.toggle('mm-open', !!document.querySelector('.header__list-item.open'));
                            layoutOpenMegaMenus();
                            return;
                        }
                        // Otherwise: close others then open+pin
                        document.querySelectorAll('.header__list-item.has-submenu.open').forEach(i => {
                            if (i !== item) i.classList.remove('open','pinned');
                        });
                        item.classList.add('open','pinned');
                        document.body.classList.toggle('mm-open', !!document.querySelector('.header__list-item.open'));
                        layoutOpenMegaMenus();
                        return;
                    }

                    // Mobile behavior
                    e.preventDefault();
                    item.classList.toggle("active");
                    if (submenuWrapper) {
                        if (submenuWrapper.style.maxHeight) {
                            submenuWrapper.style.maxHeight = null;
                        } else {
                            submenuWrapper.style.maxHeight = `${submenuWrapper.scrollHeight}px`;
                        }
                    }
                });
            }

            if (submenuWrapper) {
                // Keep open when pointer moves into submenu; close on leave
                submenuWrapper.addEventListener("mouseenter", () => {
                    if (window.innerWidth > 1025) {
                        cancelClose();
                        // do not auto-open when hovering the submenu area
                    }
                });
                submenuWrapper.addEventListener("mouseleave", () => {
                    if (window.innerWidth > 1025) {
                        scheduleClose();
                    }
                });
            }
        });

        window.addEventListener("resize", () => {
            if (window.innerWidth <= 1025) {
                document
                    .querySelectorAll(".header__list-item.has-submenu.active")
                    .forEach((item) => {
                        const submenuWrapper = item.querySelector(".submenu-wrapper");

                        if (submenuWrapper) {
                            submenuWrapper.style.maxHeight = `${submenuWrapper.scrollHeight}px`;
                        }
                    });

                // Ensure desktop-open state is cleared when switching to mobile
                document
                    .querySelectorAll(".header__list-item.has-submenu.open")
                    .forEach((i) => i.classList.remove("open"));
            } else {
                document.querySelectorAll(".submenu-wrapper").forEach((wrapper) => {
                    wrapper.style.maxHeight = null;
                });
                layoutOpenMegaMenus();
            }
        });

        document.querySelectorAll('a[href="#"]').forEach((link) => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
            });
        });

        const submenuWrappers = document.querySelectorAll(".submenu-wrapper");

        submenuWrappers.forEach((submenuWrapper) => {
            const submenuItems = submenuWrapper.querySelectorAll(
                ".submenu-list__item.has-submenu"
            );
            const defaultActiveItem = submenuWrapper.querySelector(
                ".submenu-list__item.has-submenu.active"
            );

            let returnTimeout;

            submenuItems.forEach((item) => {
                // click to activate/pin inner category
                item.addEventListener("click", (e) => {
                    if (window.innerWidth > 1025) {
                        e.preventDefault();
                        submenuItems.forEach((i) => i.classList.remove("active"));
                        item.classList.add("active");
                    }
                });
            });

            submenuWrapper.addEventListener("mouseleave", () => {
                // keep the current active (clicked) item; fallback to default if none
                if (!submenuWrapper.querySelector('.submenu-list__item.has-submenu.active')) {
                    returnTimeout = setTimeout(() => {
                        if (defaultActiveItem) {
                            defaultActiveItem.classList.add("active");
                        }
                    }, 300);
                }
            });
        });

        // Keep open mega menus stuck under the sticky header while scrolling (desktop only)
        let _layoutScheduled = false;
        const scheduleStickyLayout = () => {
            if (_layoutScheduled) return;
            _layoutScheduled = true;
            requestAnimationFrame(() => {
                _layoutScheduled = false;
                if (window.innerWidth > 1025 && document.querySelector('.header__list-item.open')) {
                    layoutOpenMegaMenus();
                }
            });
        };
        window.addEventListener('scroll', scheduleStickyLayout, { passive: true });
    });

    window.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.submenu-toolbar__close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const wrapper = e.target.closest('.submenu-wrapper');
                if (!wrapper) return;
                wrapper.classList.remove('full');
                const parentItem = wrapper.closest('.header__list-item');
                if (parentItem) parentItem.classList.remove('open','pinned');
            });
        });

        document.querySelectorAll('[data-submenu-filter]').forEach(input => {
            input.addEventListener('input', (e) => {
                const wrapper = e.target.closest('.submenu-wrapper');
                if (!wrapper) return;
                const term = e.target.value.trim().toLowerCase();
                wrapper.querySelectorAll('.submenu-content__list-item').forEach(card => {
                    const text = (card.textContent || '').toLowerCase();
                    card.style.display = term && !text.includes(term) ? 'none' : '';
                });
            });
        });

        // Auto-activate full mode for very large submenus
        document.querySelectorAll('.submenu-wrapper').forEach(wrapper => {
            const itemCount = wrapper.querySelectorAll('.submenu-content__list-item').length;
            if (itemCount >= 500) {
                wrapper.classList.add('full');
                // Inject toolbar if missing
                if (!wrapper.querySelector('.submenu-toolbar')) {
                    const toolbar = document.createElement('div');
                    toolbar.className = 'submenu-toolbar';
                    toolbar.innerHTML = `
                        <div class="submenu-toolbar__search">
                            <input type="search" placeholder="Search..." aria-label="Filter submenu" data-submenu-filter>
                        </div>
                        <button type="button" class="submenu-toolbar__close" data-submenu-close>Exit</button>
                    `;
                    wrapper.prepend(toolbar);
                }
            }
        });

        // Close all open/pinned menus helper
        const closeAllMenus = () => {
            document
                .querySelectorAll('.header__list-item.has-submenu.open, .header__list-item.has-submenu.pinned')
                .forEach(i => i.classList.remove('open','pinned'));
            document.body.classList.toggle('mm-open', !!document.querySelector('.header__list-item.open'));
            layoutOpenMegaMenus();
        };

        // Click outside to unpin/close (desktop only)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1025) return;
            const insideHeader = e.target.closest('.header');
            const insideMenu = e.target.closest('.submenu-wrapper');
            if (!insideHeader && !insideMenu) {
                closeAllMenus();
            }
        }, { capture: false });

        // Escape to unpin/close
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAllMenus();
            }
        });
    });
</script>
