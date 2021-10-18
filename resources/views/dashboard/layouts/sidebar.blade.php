<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
    <div class="brand-logo">
        <a href="{{ route("dashboard.home") }}">
            <h5 class="logo-text">Hotel Management</h5>
        </a>
    </div>
    <ul class="sidebar-menu do-nicescrol">
        <li>
            <a href="{{ route("dashboard.home") }}">
                <i class="zmdi zmdi-view-dashboard"></i> <span style="margin-left: 10px">Dashboard</span>
            </a>
        </li>
        @if(Auth::guard("employee")->user()->isAccessible("admin"))
        <li>
            <a href="{{ route("dashboard.analysis") }}">
                <i class="fa fa-line-chart"></i><span style="margin-left: 9px">Statistic</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route("dashboard.housekeeper") }}">
                <i class="zmdi zmdi-info"></i> <span style="margin-left: 9px">Today</span>
            </a>
        </li>
        <li>
            <a href="{{ route("dashboard.reservation") }}">
                <i class="fa fa-ticket"></i><span style="margin-left: 9px">Reservation</span>
            </a>
        </li>
        <li>
            <a href="{{ route("dashboard.room") }}">
                <i class="fa fa-hotel"></i><span style="margin-left: 11px">Rooms</span>
            </a>
        </li>
        @if(Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
        <li>
            <a href="{{ route("dashboard.payment") }}">
                <i class="fa fa-dollar"></i><span style="margin-left: 9px">Payment</span>
            </a>
        </li>
        @endif
        @if(Auth::guard("employee")->user()->isAccessible("admin"))
        <li>
            <a style="cursor: pointer">
                <i class="fa fa-angle-left"></i><span style="margin-left: 22px">Employees</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route("dashboard.employee.create") }}"><i class="zmdi zmdi-account-add"></i><span>New Employee</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 0]) }}"><i class="zmdi zmdi-accounts"></i><span>Admin</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 1]) }}"><i class="zmdi zmdi-accounts"></i><span>Frontdesk</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 2]) }}"><i class="zmdi zmdi-accounts"></i><span>Housekeeper</span></a></li>
                <li><a href="{{ route("dashboard.employee") }}"><i class="zmdi zmdi-accounts"></i><span>All Employees</span></a></li>
            </ul>
        </li>
        @endif
        @if(Auth::guard("employee")->user()->isAccessible("admin", "frontdesk"))
        <li>
            <a href="{{ route("dashboard.room-type") }}">
                <i class="zmdi zmdi-home"></i><span style="margin-left: 14px">Room Types</span>
            </a>
        </li>
        @endif
        @if(Auth::guard("employee")->user()->isAccessible("admin"))
        <li>
            <a href="{{ route("dashboard.facility") }}">
                <i class="fa fa-wifi"></i><span style="margin-left: 11px">Facilities</span>
            </a>
        </li>
        @endif
        @if(Auth::guard("employee")->user()->isAccessible("admin"))
        <li>
            <a href="{{ route("dashboard.service") }}">
                <i class="zmdi zmdi-drink"></i><span style="margin-left: 17px">Room Services</span>
            </a>
        </li>
        @endif
        @if(Auth::guard("employee")->user()->isAccessible("admin", "frontdesk"))
        <li>
            <a href="{{ route("dashboard.customer") }}">
                <i class="zmdi zmdi-accounts"></i><span style="margin-left: 15px">Customers</span>
            </a>
        </li>
        @endif
    </ul>
</div>
<!--End sidebar-wrapper-->
