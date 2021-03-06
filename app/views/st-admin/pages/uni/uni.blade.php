<!--deparment So giao duc-->
@extends('st-admin.layout.layout')

@section('title')
	Trang quan li cua Cum Thi
@stop

@section('sidebar')

	@include('st-admin.includes.uni_sidebar')

	<script type="text/javascript">
		var element = document.getElementById("uni-menu").getElementsByTagName("li");
		element[0].classList.add("active");
		// console.log(element[0]);
	</script>

@stop

@section('content')
<!-- <div id="main"> -->
	

	<div class="content">
		<div class="row">
			<div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">Quản lí</div>
                                    <div>Ngành và chỉ tiêu</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{Asset('/st-admin/uni/mn_major')}}">
                            <div class="panel-footer">
                                <span class="pull-left">Xem trang</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-bullseye fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">TH</div>
                                    <div>Kết quả thi</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{Asset('/st-admin/uni/syn_result')}}">
                            <div class="panel-footer">
                                <span class="pull-left">Xem trang</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>                

                </div>
            </div>
		</div>
	</div>
	


<!-- </div>	 -->
@stop