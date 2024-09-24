<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css'>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

</head>

<body>
    <!-- partial:index.partial.html -->
    <div id="nav-bar">
        <input id="nav-toggle" type="checkbox" />
        <div id="nav-header"><a id="nav-title" href="https://codepen.io" target="_blank">C<i
                    class="fab fa-codepen"></i>DEPEN</a>
            <label for="nav-toggle"><span id="nav-toggle-burger"></span></label>
            <hr />
        </div>
        <div id="nav-content">
            <div class="nav-button"><i class="fas fa-palette"></i><span>จองตั๋ว</span></div>
            <div class="nav-button"><i class="fas fa-images"></i><span>ค้นหาการจอง</span></div>
            <div class="nav-button"><i class="fas fa-thumbtack"></i><span>รอชำระเงิน</span></div>
            <hr />
            <div class="nav-button"><i class="fas fa-heart"></i><span>จัดการร้าน</span></div>
            <div class="nav-button"><i class="fas fa-chart-line"></i><span>รายงาน</span></div>
            {{-- เพิ่มมา --}}
            @if (Route::has('register'))
                <div class="nav-button"><i class="fas fa-fire"></i><span><a
                            href="{{ route('register') }}">สมัครแอดมิน</a></span></div>
            @endif
            <div class="nav-button"><i class="fas fa-magic"></i><span><a
                        href="{{ route('profile.edit') }}">แก้ไขโปรไฟล์</a></span></div>
            <hr />
            <div class="nav-button">
                <i class="fas fa-gem"></i>
                <span>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            ออกจากระบบ
                        </a>
                    </form>
                </span>
            </div>
            <div id="nav-content-highlight"></div>
        </div>
        <input id="nav-footer-toggle" type="checkbox" />
        <div id="nav-footer">
            <div id="nav-footer-heading">
                <div id="nav-footer-avatar"><img src="https://gravatar.com/avatar/4474ca42d303761c2901fa819c4f2547" />
                </div>
                <div id="nav-footer-titlebox"><a id="nav-footer-title" target="_blank">
                        <div>{{ Auth::user()->name }}</div>
                    </a><span id="nav-footer-subtitle">Admin</span></div>
                <label for="nav-footer-toggle"><i class="fas fa-caret-up"></i></label>
            </div>
            <div id="nav-footer-content">
                <Lorem>ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua.</Lorem>
            </div>
        </div>
    </div>
    <!-- partial -->
    <div class="container py-2">
        @yield('content')
    </div>

</body>

</html>
