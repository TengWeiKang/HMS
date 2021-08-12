<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
    <div class="brand-logo">
        <a href="index.html">
            <img src="{{ asset("dashboard/images/logo-icon.png") }}" class="logo-icon" alt="logo icon">
            <h5 class="logo-text">Dashboard</h5>
        </a>
    </div>
    <ul class="sidebar-menu do-nicescrol">
        {{-- <li class="sidebar-header">MAIN NAVIGATION</li> --}}
        <li>
            <a href="{{ route("dashboard.home") }}">
                <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa fa-angle-left"></i><span style="margin-left: 10px">Employees</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route("dashboard.employee.create") }}"><i class="zmdi zmdi-account-add"></i><span>New Employee</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 0]) }}"><i class="zmdi zmdi-accounts"></i><span>Admin</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 1]) }}"><i class="zmdi zmdi-accounts"></i><span>Staff</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 2]) }}"><i class="zmdi zmdi-accounts"></i><span>Housekeeper</span></a></li>
                <li><a href="{{ route("dashboard.employee") }}"><i class="zmdi zmdi-accounts"></i><span>All Employees</span></a></li>
            </ul>
        </li>
        <li>
            <a href="forms.html">
                <i class="zmdi zmdi-format-list-bulleted"></i> <span>Forms</span>
            </a>
        </li>

        <li>
            <a href="tables.html">
                <i class="zmdi zmdi-grid"></i> <span>Tables</span>
            </a>
        </li>

        <li>
            <a href="calendar.html">
                <i class="zmdi zmdi-calendar-check"></i> <span>Calendar</span>
                <small class="badge float-right badge-light">New</small>
            </a>
        </li>

        <li>
            <a href="profile.html">
                <i class="zmdi zmdi-face"></i> <span>Profile</span>
            </a>
        </li>

        <li>
            <a href="login.html" target="_blank">
                <i class="zmdi zmdi-lock"></i> <span>Login</span>
            </a>
        </li>

        <li>
            <a href="register.html" target="_blank">
                <i class="zmdi zmdi-account-circle"></i> <span>Registration</span>
            </a>
        </li>

        <li class="sidebar-header">LABELS</li>
        <li><a href="javaScript:void();"><i class="zmdi zmdi-coffee text-danger"></i> <span>Important</span></a></li>
        <li><a href="javaScript:void();"><i class="zmdi zmdi-chart-donut text-success"></i> <span>Warning</span></a></li>
        <li><a href="javaScript:void();"><i class="zmdi zmdi-share text-info"></i> <span>Information</span></a></li>
    </ul>
</div>
<!--End sidebar-wrapper-->
