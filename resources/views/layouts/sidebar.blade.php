<nav class="w3-sidebar w3-bar-block w3-small w3-hide-small w3-center">
    <img src="{{ asset('images/unciv_logo.png') }}" style="width:100%">
    <a href="/" class="w3-bar-item w3-button w3-padding-large @if ($view_name == 'landing') w3-black @else w3-hover-black @endif ">
        <i class="fa fa-home w3-xxlarge"></i>
        <p>HOME</p>
    </a>
    <a href="/editor" class="w3-bar-item w3-button w3-padding-large @if ($view_name == 'editor') w3-black @else w3-hover-black @endif ">
        <i class="fa fa-user w3-xxlarge"></i>
        <p>EDITOR</p>
    </a>
    <a href="/mods" class="w3-bar-item w3-button w3-padding-large @if ($view_name == 'mods') w3-black @else w3-hover-black @endif ">
        <i class="fa fa-puzzle-piece w3-xxlarge"></i>
        <p>MODS</p>
    </a>
</nav>

<div class="w3-top w3-hide-large w3-hide-medium" id="myNavbar">
    <div class="w3-bar w3-black w3-opacity w3-hover-opacity-off w3-center w3-small">
        <a href="/" class="w3-bar-item w3-button fa fa-home" style="width:25% !important">&nbsp;&nbsp;&nbsp;HOME</a>
        <a href="/editor" class="w3-bar-item w3-button fa fa-user" style="width:25% !important">&nbsp;&nbsp;&nbsp;EDITOR</a>
        <a href="/mods" class="w3-bar-item w3-button fa fa-puzzle-piece" style="width:25% !important">&nbsp;&nbsp;&nbsp;MODS</a>
    </div>
</div>