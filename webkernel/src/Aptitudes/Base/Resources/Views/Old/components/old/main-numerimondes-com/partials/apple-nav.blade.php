<header id="navbar">
    <nav>
        <ul>
            <li>
                <a href="#home-page">
                    <img src="https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png"
                        id="numerimondes-logo">
                </a>
            </li>
            <li><a href="https://www.numerimondes.com/mir-server/">Mir&gt;Server</a></li>
            <li><a href="https://www.numerimondes.com/ipad/">iPad</a></li>
            <li><a href="https://www.numerimondes.com/iphone/">iPhone</a></li>
            <li><a href="https://www.numerimondes.com/watch/">Watch</a></li>
            <li><a href="https://www.numerimondes.com/tv/">TV</a></li>
            <li><a href="https://www.numerimondes.com/music/">Music</a></li>
            <li><a href="https://support.numerimondes.com/">Support</a></li>
            <li>
                <x-filament::icon icon="heroicon-o-magnifying-glass"
                    class="w-5 h-5 text-gray-300 hover:text-white cursor-pointer" />
            </li>
            <li>
                <x-filament::icon icon="heroicon-o-shopping-bag"
                    class="w-5 h-5 text-gray-300 hover:text-white cursor-pointer" />
            </li>
        </ul>
    </nav>
</header>

<style>
    #navbar {
        width: 100%;
        background-color: #000;
        position: fixed;
        top: 0;
        font-family: "SF Pro Display", -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-weight: 400;
        z-index: 1000;
    }

    @font-face {
        font-family: "SF Pro Display";
        src: url("https://cdn.fontcdn.ir/Fonts/SFProDisplay/5bc1142d5fc993d2ec21a8fa93a17718818e8172dffc649b7d8a3ab459cfbf9c.woff2") format("woff2");
        font-weight: 400;
        font-style: normal;
    }

    nav ul {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 40px;
        margin: 0;
        padding: 0;
    }

    nav li {
        list-style: none;
        margin: 0 25px;
    }

    nav a {
        text-decoration: none;
        color: #c3c3c3;
    }

    nav a:hover {
        color: #fff;
    }

    #numerimondes-logo {
        width: 28px;
        display: block;
    }

    body {
        margin: 0;
    }
</style>
