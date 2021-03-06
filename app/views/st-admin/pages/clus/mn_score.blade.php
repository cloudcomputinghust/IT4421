<!--deparment So giao duc-->
@extends('st-admin.layout.layout')

@section('title')
	Trang quan li cua BGDDT
@stop

@section('sidebar')

	@include('st-admin.includes.clus_sidebar')

	<script type="text/javascript">
		var element = document.getElementById("clus-menu").getElementsByTagName("li");
		element[2].classList.add("active");
	</script>

@stop

@section('content')
<!-- <div id="main"> -->

	<div class="content">
		<!-- <div class="row">
			<div class="col-lg-12">
				<button class="hideSidebar">
					<span class="glyphicon glyphicon-arrow-left"></span>
				</button>
				<button class="showSidebar">
					<span class="glyphicon glyphicon-arrow-right"></span>     
			    </button>
				
				<br>
				<br> -->
		<div class="row">
		<br><br>
				{{	InsertForm::SearchScoreForm("registration_number",Asset('/st-admin/clus/score/search'));	}}			
				{{	InsertForm::FileExcel("importExcelFile",Asset('/st-admin/clus/mn_stu_acc/score/import')); }}
				<br>
				<button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#importExcelFile">Import Data</button>
				<br><br>
				<table class="table table-hover table-bordered table-striped table-responsive">
					<thead>
						<td>Số báo danh</td>
						<td>Môn</td>
						<td>Điểm</td>
					</thead>
				@if(isset($examscores))
					<tbody>
						@foreach ($examscores['scores'] as $examscore)
						<tr>
							<td>{{$examscores['registration_number']}}
							<td>{{$examscore[0]}}</td>
							<td>{{$examscore['score']}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@endif

		</div>			

	</script>


@stop
