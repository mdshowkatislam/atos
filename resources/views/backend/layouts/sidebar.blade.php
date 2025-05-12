{{-- Menu Initialized from $nav_menu Array --}}

@php
    $prefix = Request::route()->getPrefix();
    $route = Route::current()->getName();
    $user_role_array = Auth::user()->user_role;
    // dd($user_role_array);
    if (isset($user_role_array) && count($user_role_array) > 0) {
        foreach ($user_role_array as $rolee) {
            $user_role[] = $rolee->role_id;
        }
    } else {
        $user_role = [];
    }
    // dd($user_role);
    $parentroutearray = explode('.', $route);
    $parentroute = $parentroutearray[0];
    $childroute = $parentroute . '.' . @$parentroutearray[1];
    $nav_menu = [];
@endphp



@if (Auth::user()->id == '1')
    @php
        $grand_parents = App\Models\Menu::where('parent', 0)
            ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->get();
        foreach ($grand_parents as $grand_parent) {
            $nav_menu[$grand_parent->id]['grand_parent'] = $grand_parent->name;
            $nav_menu[$grand_parent->id]['grand_parent_route'] = $grand_parent->route;
            $nav_menu[$grand_parent->id]['grand_parent_icon'] = $grand_parent->icon;
            $parents = App\Models\Menu::where('parent', $grand_parent->id)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
            foreach ($parents as $parent) {
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_id'] = $parent->id;
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_name'] = $parent->name;
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_route'] = $parent->route;
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_icon'] = $parent->icon;
                $childs = App\Models\Menu::where('parent', $parent->id)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc')
                    ->get();
                foreach ($childs as $child) {
                    $nav_menu[$grand_parent->id]['is_child'] = 'yes';
                    $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_id'] = $child->id;
                    $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_name'] = $child->name;
                    $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_route'] = $child->route;
                }
            }
        }
    @endphp
@else
    @php
        $grand_parents = App\Models\Menu::where('parent', 0)
            ->where('status', 1)
            ->where('id', '!=', 1)
            ->orderBy('sort', 'asc')
            ->get();
        // dd($grand_parents);
        foreach ($grand_parents as $grand_parent) {
            $parents = App\Models\Menu::where('parent', $grand_parent->id)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
            // dd($parents);
            foreach ($parents as $parent) {
                $check_parent = App\Models\MenuPermission::where('menu_id', $parent->id)
                    ->whereIn('role_id', @$user_role)
                    ->first();
                // dd($check_parent);
                if ($check_parent) {
                    $permission = App\Models\MenuPermission::where('permitted_route', $parent->route)
                        ->whereIn('role_id', @$user_role)
                        ->first();
                    // dd($permission);
                    if ($permission) {
                        $nav_menu[$grand_parent->id]['grand_parent'] = $grand_parent->name;
                        $nav_menu[$grand_parent->id]['grand_parent_route'] = $grand_parent->route;
                        $nav_menu[$grand_parent->id]['grand_parent_icon'] = $grand_parent->icon;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_id'] = $parent->id;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_name'] = $parent->name;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_route'] = $parent->route;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_icon'] = $parent->icon;
                    }
                }
        
                $childs = App\Models\Menu::where('parent', $parent->id)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc')
                    ->get();
                if (count($childs) > 0) {
                    foreach ($childs as $child) {
                        $permission = App\Models\MenuPermission::where('permitted_route', $child->route)
                            ->whereIn('role_id', @$user_role)
                            ->first();
                        if ($permission) {
                            $nav_menu[$grand_parent->id]['is_child'] = 'yes';
                            $nav_menu[$grand_parent->id]['grand_parent'] = $grand_parent->name;
                            $nav_menu[$grand_parent->id]['grand_parent_route'] = $grand_parent->route;
                            $nav_menu[$grand_parent->id]['grand_parent_icon'] = $grand_parent->icon;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_name'] = $parent->name;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_route'] = $parent->route;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_icon'] = $parent->icon;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_id'] = $child->id;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_name'] = $child->name;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_route'] = $child->route;
                        }
                    }
                }
            }
        }
        // dd($nav_menu);
    @endphp
@endif
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: #222d32;">
    <a href="{{ route('index') }}" target="_blank" class="brand-link">
        <img src="{{ asset('public/images/logo/bup_logo.png') }}" alt="Admin Dashboard"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light" style="font-size: 15px;font-weight: bold;">BUP</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('public/images/avatar.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="true">
                {{-- @dd($nav_menu) --}}
                @foreach ($nav_menu as $grand_menu)
                    <li class="nav-item has-treeview ">
                        <a href="#" class="nav-link d-flex align-items-center">
                            <i class="nav-icon {{ $grand_menu['grand_parent_icon'] ? $grand_menu['grand_parent_icon'] : 'fas fa-tools' }}"
                                {!! $grand_menu['grand_parent_icon'] ? 'style="font-size: 1.4rem;"' : '' !!}>
                            </i>
                            <p>
                                {{ $grand_menu['grand_parent'] }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        @if ($grand_menu['parent'])
                            <ul class="nav nav-treeview" style="display:none">
                                @foreach ($grand_menu['parent'] as $parent_menu)
                                    @if (!empty($parent_menu['child']))
                                    @else
                                        <li class="nav-item">
                                            <a href="{{ route($parent_menu['parent_route']) }}"
                                                class="nav-link d-flex align-items-center">
                                                <i class="nav-icon {{ $parent_menu['parent_icon'] ? $parent_menu['parent_icon'] : 'ion-ionic' }}"></i>
                                                <p>{{ $parent_menu['parent_name'] }}</p>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>

<script type="text/javascript">
    $(document).ready(function() {
        var mylist = [];
        var list_counter = 1;
        $(".sidebar .nav-item a").each(function() {
            if ($(this).attr('href') != '#') {
                mylist.push({
                    id: list_counter++,
                    name: $(this).attr('href')
                });
            }
        });
        navigationMenu(mylist);

        function navigationMenu(menu_array = null) {
            var url;
            var url_path = window.location.pathname;

            if (menu_array == null) {
                url = window.location.href;
            } else {
                if (menu_array.some(item => item.name === url_path)) {
                    url = window.location.href;
                } else {
                    url = highlight_navigation(menu_array);
                }
            }

        
            var patterns = [{
                    pattern: /^\/home$/, // home_pattern
                },
                {
                    pattern: /^\/document(?:\/(add|edit))?(?:\/(\w+))?\/(\d+)$/, //document_pattern
                },
                {
                    pattern: /^\/setup\/gallery(?:\/(edit|add))?(?:\/(\d+))?(?:\/(\d+))?$/, // gallery_pattern
                },
                {
                    pattern: /^\/setup\/photo(?:\/(list|add|edit))?(?:\/(\d+))?(?:\/(\d+))?(?:\/(\d+))?$/, // photo_list_pattern
                }
            ];

            var matched = false; // Flag to track if a pattern was matched

            // Iterate through the patterns array
            for (var i = 0; i < patterns.length; i++) {
                var pattern = patterns[i].pattern;

                if (pattern.test(url_path)) {
                    matched = true;
                    break; // Exit the loop after finding a match
                }
            }

            if (!matched) {
                // Perform default action when no pattern is matched
                $('.sidebar .nav-item a[href="' + url + '"]').addClass('active');
                $('.sidebar .nav-item a[href="' + url + '"]').parents('ul').css('display', 'block');
                $('.sidebar .nav-item a[href="' + url + '"]').parents('li').addClass('nav-item menu-open');
                // $('.sidebar .nav-item a').filter(function() {
                //     return this.href == url;
                // }).addClass('active');

            }


        }

        function highlight_navigation(list_array) {
            var path = window.location.href;
            path = path.replace(/\/$/, "");
            path = decodeURIComponent(path);
            var max_value = [];
            for (var i = 0; i < list_array.length; i++) {
                var percent = similar(list_array[i].name, path);
                max_value.push({
                    'name': list_array[i].name,
                    'percent': percent
                });
            }
            var xValues = max_value.map(function(o) {
                return o.percent;
            });
            xValues = Array.from(max_value, o => o.percent);
            var xMax = Math.max.apply(null, xValues);
            xMax = Math.max(...xValues);
            var maxXObjects = max_value.filter(function(o) {
                return o.percent === xMax;
            });
            var the_arr = maxXObjects[0].name.split('/');
            return (the_arr.join('/'));
        }

        function similar(a, b) {
            var equivalency = 0;
            var minLength = (a.length > b.length) ? b.length : a.length;
            var maxLength = (a.length < b.length) ? b.length : a.length;
            for (var i = 0; i < minLength; i++) {
                if (a[i] == b[i]) {
                    equivalency++;
                }
            }
            var weight = equivalency / maxLength;
            return Math.round(weight * 100); // + "%";
        }

        $('li').each(function() {
            // Check if the li tag has the "menu-open" class
            if ($(this).hasClass('menu-open')) {
                // Add the "active" class to anchor tags inside li tags with the "menu-open" class
                $(this).find('a').addClass('active');
            } else {
                // Remove the "active" class from anchor tags inside li tags without the "menu-open" class
                $(this).find('a').removeClass('active');
            }
        });
    });
</script>
{{-- Menu Initialized from $nav_menu Array --}}

@php
    $prefix = Request::route()->getPrefix();
    $route = Route::current()->getName();
    $user_role_array = Auth::user()->user_role;
    // dd($user_role_array);
    if (isset($user_role_array) && count($user_role_array) > 0) {
        foreach ($user_role_array as $rolee) {
            $user_role[] = $rolee->role_id;
        }
    } else {
        $user_role = [];
    }
    // dd($user_role);
    $parentroutearray = explode('.', $route);
    $parentroute = $parentroutearray[0];
    $childroute = $parentroute . '.' . @$parentroutearray[1];
    $nav_menu = [];
@endphp



@if (Auth::user()->id == '1')
    @php
        $grand_parents = App\Models\Menu::where('parent', 0)
            ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->get();
        foreach ($grand_parents as $grand_parent) {
            $nav_menu[$grand_parent->id]['grand_parent'] = $grand_parent->name;
            $nav_menu[$grand_parent->id]['grand_parent_route'] = $grand_parent->route;
            $nav_menu[$grand_parent->id]['grand_parent_icon'] = $grand_parent->icon;
            $parents = App\Models\Menu::where('parent', $grand_parent->id)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
            foreach ($parents as $parent) {
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_id'] = $parent->id;
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_name'] = $parent->name;
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_route'] = $parent->route;
                $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_icon'] = $parent->icon;
                $childs = App\Models\Menu::where('parent', $parent->id)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc')
                    ->get();
                foreach ($childs as $child) {
                    $nav_menu[$grand_parent->id]['is_child'] = 'yes';
                    $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_id'] = $child->id;
                    $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_name'] = $child->name;
                    $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_route'] = $child->route;
                }
            }
        }
    @endphp
@else
    @php
        $grand_parents = App\Models\Menu::where('parent', 0)
            ->where('status', 1)
            ->where('id', '!=', 1)
            ->orderBy('sort', 'asc')
            ->get();
        // dd($grand_parents);
        foreach ($grand_parents as $grand_parent) {
            $parents = App\Models\Menu::where('parent', $grand_parent->id)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
            // dd($parents);
            foreach ($parents as $parent) {
                $check_parent = App\Models\MenuPermission::where('menu_id', $parent->id)
                    ->whereIn('role_id', @$user_role)
                    ->first();
                // dd($check_parent);
                if ($check_parent) {
                    $permission = App\Models\MenuPermission::where('permitted_route', $parent->route)
                        ->whereIn('role_id', @$user_role)
                        ->first();
                    // dd($permission);
                    if ($permission) {
                        $nav_menu[$grand_parent->id]['grand_parent'] = $grand_parent->name;
                        $nav_menu[$grand_parent->id]['grand_parent_route'] = $grand_parent->route;
                        $nav_menu[$grand_parent->id]['grand_parent_icon'] = $grand_parent->icon;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_id'] = $parent->id;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_name'] = $parent->name;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_route'] = $parent->route;
                        $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_icon'] = $parent->icon;
                    }
                }
        
                $childs = App\Models\Menu::where('parent', $parent->id)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc')
                    ->get();
                if (count($childs) > 0) {
                    foreach ($childs as $child) {
                        $permission = App\Models\MenuPermission::where('permitted_route', $child->route)
                            ->whereIn('role_id', @$user_role)
                            ->first();
                        if ($permission) {
                            $nav_menu[$grand_parent->id]['is_child'] = 'yes';
                            $nav_menu[$grand_parent->id]['grand_parent'] = $grand_parent->name;
                            $nav_menu[$grand_parent->id]['grand_parent_route'] = $grand_parent->route;
                            $nav_menu[$grand_parent->id]['grand_parent_icon'] = $grand_parent->icon;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_name'] = $parent->name;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_route'] = $parent->route;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['parent_icon'] = $parent->icon;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_id'] = $child->id;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_name'] = $child->name;
                            $nav_menu[$grand_parent->id]['parent'][$parent->id]['child'][$child->id]['child_route'] = $child->route;
                        }
                    }
                }
            }
        }
        // dd($nav_menu);
    @endphp
@endif
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: #222d32;">
    <a href="{{ route('index') }}" target="_blank" class="brand-link">
        <img src="{{ asset('public/images/logo/bup_logo.png') }}" alt="Admin Dashboard"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light" style="font-size: 15px;font-weight: bold;">BUP</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('public/images/avatar.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="true">
                {{-- @dd($nav_menu) --}}
                @foreach ($nav_menu as $grand_menu)
                    <li class="nav-item has-treeview ">
                        <a href="#" class="nav-link d-flex align-items-center">
                            <i class="nav-icon {{ $grand_menu['grand_parent_icon'] ? $grand_menu['grand_parent_icon'] : 'fas fa-tools' }}"
                                {!! $grand_menu['grand_parent_icon'] ? 'style="font-size: 1.4rem;"' : '' !!}>
                            </i>
                            <p>
                                {{ $grand_menu['grand_parent'] }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        @if ($grand_menu['parent'])
                            <ul class="nav nav-treeview" style="display:none">
                                @foreach ($grand_menu['parent'] as $parent_menu)
                                    @if (!empty($parent_menu['child']))
                                    @else
                                        <li class="nav-item">
                                            <a href="{{ route($parent_menu['parent_route']) }}"
                                                class="nav-link d-flex align-items-center">
                                                <i class="nav-icon {{ $parent_menu['parent_icon'] ? $parent_menu['parent_icon'] : 'ion-ionic' }}"></i>
                                                <p>{{ $parent_menu['parent_name'] }}</p>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>

<script type="text/javascript">
    $(document).ready(function() {
        var mylist = [];
        var list_counter = 1;
        $(".sidebar .nav-item a").each(function() {
            if ($(this).attr('href') != '#') {
                mylist.push({
                    id: list_counter++,
                    name: $(this).attr('href')
                });
            }
        });
        navigationMenu(mylist);

        function navigationMenu(menu_array = null) {
            var url;
            var url_path = window.location.pathname;

            if (menu_array == null) {
                url = window.location.href;
            } else {
                if (menu_array.some(item => item.name === url_path)) {
                    url = window.location.href;
                } else {
                    url = highlight_navigation(menu_array);
                }
            }

        
            var patterns = [{
                    pattern: /^\/home$/, // home_pattern
                },
                {
                    pattern: /^\/document(?:\/(add|edit))?(?:\/(\w+))?\/(\d+)$/, //document_pattern
                },
                {
                    pattern: /^\/setup\/gallery(?:\/(edit|add))?(?:\/(\d+))?(?:\/(\d+))?$/, // gallery_pattern
                },
                {
                    pattern: /^\/setup\/photo(?:\/(list|add|edit))?(?:\/(\d+))?(?:\/(\d+))?(?:\/(\d+))?$/, // photo_list_pattern
                }
            ];

            var matched = false; // Flag to track if a pattern was matched

            // Iterate through the patterns array
            for (var i = 0; i < patterns.length; i++) {
                var pattern = patterns[i].pattern;

                if (pattern.test(url_path)) {
                    matched = true;
                    break; // Exit the loop after finding a match
                }
            }

            if (!matched) {
                // Perform default action when no pattern is matched
                $('.sidebar .nav-item a[href="' + url + '"]').addClass('active');
                $('.sidebar .nav-item a[href="' + url + '"]').parents('ul').css('display', 'block');
                $('.sidebar .nav-item a[href="' + url + '"]').parents('li').addClass('nav-item menu-open');
                // $('.sidebar .nav-item a').filter(function() {
                //     return this.href == url;
                // }).addClass('active');

            }


        }

        function highlight_navigation(list_array) {
            var path = window.location.href;
            path = path.replace(/\/$/, "");
            path = decodeURIComponent(path);
            var max_value = [];
            for (var i = 0; i < list_array.length; i++) {
                var percent = similar(list_array[i].name, path);
                max_value.push({
                    'name': list_array[i].name,
                    'percent': percent
                });
            }
            var xValues = max_value.map(function(o) {
                return o.percent;
            });
            xValues = Array.from(max_value, o => o.percent);
            var xMax = Math.max.apply(null, xValues);
            xMax = Math.max(...xValues);
            var maxXObjects = max_value.filter(function(o) {
                return o.percent === xMax;
            });
            var the_arr = maxXObjects[0].name.split('/');
            return (the_arr.join('/'));
        }

        function similar(a, b) {
            var equivalency = 0;
            var minLength = (a.length > b.length) ? b.length : a.length;
            var maxLength = (a.length < b.length) ? b.length : a.length;
            for (var i = 0; i < minLength; i++) {
                if (a[i] == b[i]) {
                    equivalency++;
                }
            }
            var weight = equivalency / maxLength;
            return Math.round(weight * 100); // + "%";
        }

        $('li').each(function() {
            // Check if the li tag has the "menu-open" class
            if ($(this).hasClass('menu-open')) {
                // Add the "active" class to anchor tags inside li tags with the "menu-open" class
                $(this).find('a').addClass('active');
            } else {
                // Remove the "active" class from anchor tags inside li tags without the "menu-open" class
                $(this).find('a').removeClass('active');
            }
        });
    });
</script>
