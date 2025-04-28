<?php
use App\Models\Job_request;

?>

<ul class="nav nav-secondary">
    @if($userInfo->usertype == 1)
        <li class="nav-section">
            <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Administrator</h4>
        </li>
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{route('dashboard')}}">
                <i class="fas fa-home"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('view-technician') ? 'active' : '' }}">
            <a href="{{ route('view-technician') }}">
                <i class="fas fa-user-cog"></i>
                <p>Technician</p>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.request') ? 'active' : '' }}">
            <a href="{{ route('admin.request') }}">
                <i class="fas fa-desktop"></i>
                <p>Request</p>
                <span class="badge badge-danger">{{ $totalPending ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('finished') ? 'active' : '' }}">
            <a href="{{ route('finished') }}">
                <i class="fas fa-tasks"></i>
                <p>Finished</p>
            </a>
        </li>
    @elseif($userInfo->usertype == 2)
        <li class="nav-section">
            <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Technician</h4>
        </li>
        <li class="nav-item {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}">
            <a href="{{route('technician.dashboard')}}">
                <i class="fas fa-home"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('technician.request') ? 'active' : '' }}">
            <a href="{{ route('technician.request') }}">
                <i class="fas fa-desktop"></i>
                <p>Request</p>
                <span class="badge badge-danger">{{ $totalPending ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('technician.finished') ? 'active' : '' }}">
            <a href="{{ route('technician.finished') }}">
                <i class="fas fa-tasks"></i>
                <p>Finished</p>
            </a>
        </li>
    @endif
</ul>
