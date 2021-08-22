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
                <i class="zmdi zmdi-view-dashboard"></i> <span style="margin-left: 10px">Dashboard</span>
            </a>
        </li>
        @if(Auth::guard('employee')->user()->isAdmin() || Auth::guard("employee")->user()->isStaff())
        <li>
            <a href="{{ route("dashboard.reservation") }}">
                <i class="fa fa-ticket"></i><span style="margin-left: 9px">Reservation</span>
            </a>
        </li>
        @endif
        @if(Auth::guard('employee')->user()->isAdmin())
        <li>
            <a style="cursor: pointer">
                <i class="fa fa-angle-left"></i><span style="margin-left: 22px">Employees</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route("dashboard.employee.create") }}"><i class="zmdi zmdi-account-add"></i><span>New Employee</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 0]) }}"><i class="zmdi zmdi-accounts"></i><span>Admin</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 1]) }}"><i class="zmdi zmdi-accounts"></i><span>Staff</span></a></li>
                <li><a href="{{ route("dashboard.employee", ["role" => 2]) }}"><i class="zmdi zmdi-accounts"></i><span>Housekeeper</span></a></li>
                <li><a href="{{ route("dashboard.employee") }}"><i class="zmdi zmdi-accounts"></i><span>All Employees</span></a></li>
            </ul>
        </li>
        @endif
        @if(Auth::guard('employee')->user()->isAdmin())
        <li>
            <a href="{{ route("dashboard.facility") }}">
                <i class="fa fa-wifi"></i><span style="margin-left: 12px">Facilities</span>
            </a>
        </li>
        @endif
        @if(Auth::guard('employee')->user()->isAdmin())
        <li>
            <a href="{{ route("dashboard.room") }}">
                <i class="fa fa-hotel"></i><span style="margin-left: 12px">Rooms</span>
            </a>
        </li>
        @endif
        @if(Auth::guard('employee')->user()->isAdmin())
        <li>
            <a href="{{ route("dashboard.service") }}">
                <i class="zmdi zmdi-drink"></i><span style="margin-left: 17px">Room Services</span>
            </a>
        </li>
        @endif
    </ul>
</div>
<!--End sidebar-wrapper-->
