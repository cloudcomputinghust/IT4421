
	<!-- sidebar -->
	<div class="sidebar">
		<div class="user-menu">
			<span class="user-avatar"> 
			{{	HTML::image('/components/img/user-avatar.png','logo_title',array( 'width' => '100%', 'height' => '100%' ))	}}</span>
			<ul>
				<li><a href="{{ Asset('/logout')}}">Đăng xuất</a></li>
			</ul>
		</div>

		<ul id="uni-menu" class="wrapper">
			<li><a href="{{Asset('/st-admin/uni')}}">CHỨC NĂNG</a></li>
			<li><a href="{{Asset('/st-admin/uni/mn_major')}}">QUẢN LÍ NGÀNH VÀ CHỈ TIÊU</a></li>
			<li><a href="{{Asset('/st-admin/uni/syn_result')}}">TỔNG HỢP KẾT QUẢ THI</a></li>'
		</ul>
	</div>

	<!--end sidebar -->