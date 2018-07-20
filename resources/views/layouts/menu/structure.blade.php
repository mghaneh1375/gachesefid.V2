<header class="header" data-pinned="swingInX" data-unpinned="swingOutX" id="site-header" style="font-family: IRANSans_Light !important;">
    <div class="container">
        <div class="header-content-wrapper">

            @include('layouts.menu.logo')
            <nav class="primary-menu">
                <!-- menu-icon-wrapper -->

                <ul id="primary-menu" class="primary-menu-menu">

                    @yield('items')

                    @if(\Illuminate\Support\Facades\Auth::check())
                        @include('layouts.menu.message')
                        @include('layouts.menu.profile')
                        @include('layouts.menu.logout')
                    @endif

                </ul>
            </nav>
        </div>
    </div>
</header>
<div id="header-spacer" class="header-spacer"></div>
