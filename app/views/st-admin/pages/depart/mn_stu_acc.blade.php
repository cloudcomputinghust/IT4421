<!--deparment So giao duc-->
@extends('st-admin.layout.layout')

@section('title')
	Trang quan li cua BGDDT
@stop

@section('sidebar')

	@include('st-admin.includes.depart_sidebar')

	<script type="text/javascript">
		var element = document.getElementById("depart-menu").getElementsByTagName("li");
		element[1].classList.add("active");
	</script>

@stop

@section('content')
<!-- <div id="main"> -->

	<div class="content">
		<div class="row">
			<div class="col-lg-12">
				<button class="hideSidebar">
					<span class="glyphicon glyphicon-arrow-left"></span>
				</button>
				<button class="showSidebar">
					<span class="glyphicon glyphicon-arrow-right"></span>     
			    </button>
				
				<br>
				<br>

				{{	InsertForm::SearchStudentForm("stunumber","stuid",Asset('/st-admin/depart/mn_stu_acc/search'));	}}			


				<br>
				<button type = "submit" class="btn btn-success" data-toggle="modal" data-target="#addStuModal">Add new data</button> 
				<button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#exportExcelFile">Export As Excel</button>
				<button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#importExcelFile">Import Data</button>
				<br>

				
				<br>
				<table class="table table-hover table-bordered table-striped table-responsive">
					
				<thead>
					<td>ID</td>
					<td>CMTND</td>
					<td>Họ</td>
					<td>Tên</td>
					<td>Action</td>
					<td>Action</td>
				</thead>
				<tbody>

					@foreach ($students as $student)
					<tr>
						<td>{{$student['id']}}</td>
						<td>{{$student['indentity_code']}}
						<td>{{$student['firstname']}}</td>
						<td>{{$student['lastname']}}</td>
						<td><button class="btn btn-success" data-toggle="modal" data-target="#editStuModal" onclick="editStuForm({{$student['id']}})">Edit</button></td>
						<td><button class="btn btn-danger" onclick="deleteStuForm({{$student['id']}})">Delete</button></td>

					</tr>
					@endforeach
				</tbody>
					<?php echo $students->links(); ?>
				</table>
				
			</div>
		</div>
	</div>
				{{	InsertForm::FileExport("exportExcelFile",Asset('/st-admin/depart/mn_stu_acc/export'));	}}
				{{	InsertForm::FileExcel("importExcelFile",Asset('/st-admin/minis/mn_stu_acc/add_many')); }}
				{{	InsertForm::Student("addStuModal");	}}			

				{{	EditForm::StuForm("editStuModal");	}}
				
	<script type="text/javascript">
		function editStuForm(id){
			console.log(id);
			$.ajax({
                url : "{{Asset('/st-admin/depart/mn_stu_acc/edit')}}/"+id,
                type : "GET",
                data : {
                     // number : $('#number').val()
                },
                success : function (result){
                    var obj = jQuery.parseJSON(result);
                    console.log(obj);
                    $modal = $('#editStuModal').find('input');
					$($modal[0]).val(obj.id);
					$($modal[1]).val(obj.profile_code);
					$($modal[2]).val(obj.registration_number);
					$($modal[3]).val(obj.firstname);
					$($modal[4]).val(obj.lastname);
					$($modal[5]).val(obj.indentity_code);
					$($modal[6]).val(obj.birthday);
					$($modal[7]).val(obj.sex);
					$($modal[8]).val(obj.plus_score);
					$($modal[9]).val(obj.department_id);
                }
            });
		}

		function deleteStuForm(id){

			if(confirm("Bạn có muốn xóa không ?") == true){
				$.ajax({
                url : "{{Asset('/st-admin/depart/mn_stu_acc/delete')}}/"+id,
                type : "GET",
                data : {
                     // number : $('#number').val()
                },
                success : function (result){
					var url = window.location.href;
		            location.reload(url);
                    // console.log(result);	
                    // alert("delete success");
                },
                error : function(result){
                	location.reload(url);
                    // console.log(result);	
                }
            });				
			}
		}

		$('#editStuModal').submit(function(e)
		{
			console.log('ok');
		    var postData = $(this).serializeArray();
		    var formURL = $(this).attr("action");
		    $.ajax(
		    {
		        url : "{{Asset('/st-admin/depart/mn_stu_acc/update')}}",
		        type: "POST",
		        data : postData,
		        success:function(data, textStatus, jqXHR) 
		        {
		        	// console.log(data);
		        	var url = window.location.href;
		            location.reload(url);
		            //data: return data from server
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		            //if fails   
		            var url = window.location.href;
		            location.reload(url);   
		        }
		    });
		    e.preventDefault(); //STOP default action
		    // e.unbind(); //unbind. to stop multiple form submit.
		    $('#editStuModalclosebtn').click();
		});
		 
			// $('#editStuForm').submit(); //Submit  the FORM
				
		//Thêm một thí sinh mới --------------
		$('#addStuModal').submit(function(e)
		{
			console.log('ok');
		    var data1 = $(this).serializeArray();
		    console.log(data1);
		    var data = {
		    	student:{
		    		profile_code:data1[2].value,
		    		registration_number:data1[3].value,
		    		firstname:data1[4].value,
		    		lastname:data1[5].value,
		    		indentity_code:data1[6].value,
		    		birthday:data1[7].value,
		    		sex:data1[8].value,
		    		plus_score:data1[9].value,
		    		department_id:data1[10].value
		    	},
		    	user:{
		    		username:data1[0].value,
		    		password:data1[1].value
		    	}
		    };
		    // alert(data);
		    $.ajax(
		    {
		        url : "{{Asset('/st-admin/depart/mn_stu_acc/add')}}",
		        type: "POST",
		        data : data,
		        success:function(data, textStatus, jqXHR) 
		        {
					// console.log(data);
		        	var url = window.location.href;
		            location.reload(url);		            
		        },
		        error: function(jqXHR, textStatus, errorThrown) 
		        {
		        	// console.log(data);
		            //if fails    
		        	var url = window.location.href;
		            location.reload(url);		              
		        }
		    });
		    e.preventDefault(); //STOP default action
		    // e.unbind(); //unbind. to stop multiple form submit.
		    $('#editClusModalclosebtn').click();
		});



	</script>


@stop
