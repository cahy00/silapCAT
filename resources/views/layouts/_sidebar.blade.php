<a href="#" class="brand-link">
	<img src="{{asset('assets/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
	<span class="brand-text font-weight-light">SuperApp</span>
</a>

<div class="sidebar">

	{{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
		<div class="image">
			<img src="{{asset('assets/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
		</div>
		<div class="info">
			<a href="#" class="d-block">{{Auth::user()->name}}</a>
		</div>
	</div> --}}

	<!-- SidebarSearch Form -->
	<div class="form-inline"></div>

	<!-- Sidebar Menu -->
	<nav class="mt-2">
		<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
			<!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
			@if (Auth::user()->hasRole('admin'))
			<li class="nav-header">Dashboard</li>
			<li class="nav-item">
				<a href="/dashboard" class="nav-link">
					<i class="nav-icon fas fa-tachometer-alt"></i>
					<p>
						Dashboard
					</p>
				</a>
			</li>
			<li class="nav-header">Module Aplikasi Arsip</li>
			<li class="nav-item">
				<a href="/document/{{encrypt(1)}}" class="nav-link">
					<i class="nav-icon fas fa-share"></i>
					<p>Surat Masuk</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/document/{{encrypt(2)}}" class="nav-link">
					<i class="nav-icon fas fa-reply"></i>
					<p>Surat Keluar</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/type" class="nav-link">
					<i class="nav-icon fas fa-plus-square"></i>
					<p>Tipe Dokumen</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/type" class="nav-link">
					<i class="nav-icon fas fa-building"></i>
					<p>Unit Kerja</p>
				</a>
			</li>
			<li class="nav-header">Module Aplikasi REPO CAT</li>
			<li class="nav-item">
				<a href="/event" class="nav-link">
					<i class="nav-icon fas fa-calendar"></i>
					<p>Event Kegiatan</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/tilok" class="nav-link">
					<i class="nav-icon fas fa-compass"></i>
					<p>Titik Lokasi Kegiatan</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/event-tilok/create" class="nav-link">
					<i class="nav-icon fas fa-calendar-check"></i>
					<p>Detail Event</p>
				</a>
			</li>
			<li class="nav-header">Module Laporan</li>
			<li class="nav-item">
				<a href="/recap" class="nav-link">
					<i class="nav-icon fas fa-book"></i>
					<p>Laporan Pelaksanaan</p>
				</a>
			</li>
			<li class="nav-header">Manajemen User</li>
			<li class="nav-item">
				<a href="/user" class="nav-link">
					<i class="nav-icon fas fa-user"></i>
					<p>User</p>
				</a>
			</li>
			@endif
			@if (Auth::user()->role == 'arsip_digital')
			<li class="nav-header">Dashboard</li>
			<li class="nav-item">
				<a href="/dashboard/inka" class="nav-link">
					<i class="nav-icon fas fa-tachometer-alt"></i>
					<p>
						Dashboard
					</p>
				</a>
			</li>
			<li class="nav-header">Module Aplikasi Arsip</li>
			<li class="nav-item">
				<a href="/document/inka/{{encrypt(1)}}" class="nav-link">
					<i class="nav-icon fas fa-share"></i>
					<p>Surat Masuk</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/document/inka/{{encrypt(2)}}" class="nav-link">
					<i class="nav-icon fas fa-reply"></i>
					<p>Surat Keluar</p>
				</a>
			</li>
			{{-- <li class="nav-item">
				<a href="/type" class="nav-link">
					<i class="nav-icon fas fa-plus-square"></i>
					<p>Tipe Dokumen</p>
				</a>
			</li> --}}
			
			@endif

			@if (Auth::user()->role == 'repo_cat')
			<li class="nav-header">Dashboard</li>
			<li class="nav-item">
				<a href="/dashboard" class="nav-link">
					<i class="nav-icon fas fa-tachometer-alt"></i>
					<p>
						Dashboard
					</p>
				</a>
			</li>
			<li class="nav-header">Module Aplikasi REPO CAT</li>
			<li class="nav-item">
				<a href="/event" class="nav-link">
					<i class="nav-icon fas fa-calendar"></i>
					<p>Event Kegiatan</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="/detail-event" class="nav-link">
					<i class="nav-icon fas fa-calendar-check"></i>
					<p>Detail Event</p>
				</a>
			</li>
			
			@endif
			
			<li class="nav-header">Exit</li>
			<li class="nav-item">
				<a href="/logout" class="nav-link">
					<i class="nav-icon fas fa-copy"></i>
					<p>
						Logout
					</p>
				</a>
			</li>
		</ul>
	</nav>
	<!-- /.sidebar-menu -->
</div>