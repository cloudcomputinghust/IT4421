<?php
// require_once (dirname(__FILE__).'/Excel/reader.php');
require_once (dirname(__FILE__).'/Utils.php');
class ClusterController extends \BaseController {
	protected $column = array('code', 'name');
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function search() {
		$code=Input::get('cluscode');
		$name=Input::get('clusname');
		$clusters = Cluster::where('code', 'like', '%' . $code . '%')->where('name', 'like', '%' . $name . '%')->paginate(10);
		return View::make('st-admin.pages.minis.mn_clus_acc')->with('clusters', $clusters);
	}
	public function search_score() {
		// dd(Input::all());
		// $name=Input::get('lastname');
		$code=Input::get('registration_number');
		// $clusters = ExamScore::where('lastname', 'like', '%' . $name . '%')->orWhere('indentity_code', 'like', '%' . $code . '%')->paginate(10);
		$students = Student::where('registration_number',$code)->distinct()->get();	
		// dd($student);	
		// $examscores = array('registration_number'=>$code);
		$scores = array();
		foreach ($students as $key => $value) {
			$score = $value->examscores->toArray();		
			foreach ($score as $k => $v) {
					$subject = Subject::find($v['subject_id'])->toArray();
					// array_merge($v, new array('subject'=>$subject['name']);
					// dd($subject['name']);
					array_push($v, $subject['name']);
					// dd($v);				
					// $v['subject_id'] = $subject['name'];
					// dd($subject->toArray());
			array_push($scores,$v);			
				}				
		}
		$examscores = array('registration_number'=>$code,'scores'=>$scores);
		// dd($examscores);
		return View::make('st-admin.pages.clus.mn_score')->with('examscores', $examscores);
	}

	public function get_list() {
		$clusters = Cluster::all();
		echo ($clusters);
	}
	public function index() {
		return View::make('st-admin.pages.clus.clus');
	}
	public function manage_student_page() {
		if (Auth::check()) {
			$cluster_id = Auth::user()->userable_id;
			$students_data = array();
			$rooms = Cluster::find($cluster_id)->rooms()->get();
			foreach ($rooms as $room) {
				$students_data = array_merge($students_data, $room->students->toArray());
			}
			$students = Paginator::make($students_data, count($students_data), 10);
			// dd($students);
			return View::make('st-admin.pages.clus.mn_stu_acc')->with("students", $students);
		}
	}
	public function mn_clus_search(){
		if (Auth::check()) {			
			$name=Input::get('name');
			$indentity_code=Input::get('indentity_code');
			if(!isset($indentity_code))
				$indentity_code = '';
			if(!isset($name))
				$name = '';
			// dd(Input::all());
			$cluster_id = Auth::user()->userable_id;
			$students_data = array();
			$result = array();
			$rooms = Cluster::find($cluster_id)->rooms()->get();
			foreach ($rooms as $room) {
				$students_data = array_merge($students_data, $room->students->toArray());				
			}
			// dd($students_data);
			foreach ($students_data as $key => $value) {
				// dd($value);
				if($value['indentity_code']==$indentity_code ||$value['lastname']==$name ){
					array_push($result, $value);
				}				
			}
			// dd($result);
			return View::make('st-admin.pages.clus.mn_stu_acc')->with('students', $result);
		}		
		
		// $students = Student::where('registration_number', 'like', '%' . $code . '%')->where('indentity_code', 'like', '%' . $name . '%')->paginate(10);
	}
	public function syn_result() {
		if (Auth::check()) {
			$id = Auth::user()->userable_id;
			$rooms = Cluster::find($id)->rooms()->get();
			$students = array();			
			foreach ($rooms as $room) {
				array_push($students,$room->students);				
			}						
			$subject_frequent = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0
				,9=>0,10=>0);			
			$result = array('1'=>new ArrayObject($subject_frequent),'2'=>new ArrayObject($subject_frequent),'3'=>new ArrayObject($subject_frequent),'4'=>$d=$subject_frequent,
				'5'=>$e=$subject_frequent,'6'=>$f=$subject_frequent,'7'=>$g=$subject_frequent,'8'=>$h=$subject_frequent);

			foreach ($students as $key => $value) {												
				foreach ($value as $t => $d) {			
					foreach ($d->examscores as $k => $v) {																
						$result[$v->subject_id][intval($v->score)] =$result[$v->subject_id][intval($v->score)]+1;

					}
					
				}
				// exit();	
			}		

			// tra ve danh sach thong ke diem theo tung nhom nganh			
			return View::make('st-admin.pages.clus.syn_result')->with('result',$result);
		}		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {

	}

	/**
	 * HuanPC
	 * Store a newly created resource in storage.
	 * Store one by one
	 * @return Response
	 */
	public function add() {

		$data = Input::all();
		if (!isset($data)) {
			echo "error";
		}
		try {
			$cluster = Cluster::where('code', $data['cluster']['code'])->first();
			if (isset($cluster)) {
				echo "error";
				exit();
			}
			$clusterData = array_combine($this->column, $data['cluster']);
			$cluster = Cluster::create($clusterData);
			// $user = $dept->user;
			$user = new User($data['user']);
			$user->password = Hash::make($data['user']['password']);
			$cluster->user()->save($user);
		} catch (QueryException $e) {
			echo "error";
		}
		if (isset($cluster)) {
			Session::flash('alert-class', 'alert-success');
			Session::flash('message', 'Thêm mới thành công!!!');
			echo "success";
		} else {
			Session::flash('alert-class', 'alert-danger');
			Session::flash('message', 'Đã có lôi xảy ra, vui lòng thử lại!!!');
			echo "error";
		}
	}
	/**
	 * HuanPC
	 * Them nhieu ban ghi vao database tu file exel
	 * @return [type] [description]
	 */
	public function add_many() {
		$fileInputName = 'excel_file';
		$data = Utils::importExelFile($fileInputName);
		$count = 0;
		if (isset($data)) {
			foreach ($data as $key => $value) {
				// Kiem tra du lieu da ton tai trong csdl?
				$check = Cluster::where('code', $value[0])->first();
				if (!isset($check)) {
					// Neu chua ton tai thi moi insert
					$data_insert = array_combine($this->column, array($value[0],$value[1]));						
					$clus = Cluster::create($data_insert);
					$user = new User(array('username'=>$value[2],'password'=>$value[3],'email'=>$value[4]));
					$user->password = Hash::make($value[3]);
					$clus->user()->save($user);
					if (isset($clus)) {
						$count += 1;
					}
				}

			}
		}
		Session::flash('alert-class', 'alert-success');
		Session::flash('message', 'Thêm mới thành công '.$count.' bản ghi!!');
		echo json_encode('success');
	}
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		$cluster = Cluster::with('Room')->find(intval($id));
		$rooms = $cluster->rooms();
		return View::make('', array('cluster' => $cluster, 'rooms' => $rooms));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		$cluster = Cluster::find(intval($id));
		// print_r($cluster->id);
		// print_r($cluster->code);
		// print_r($cluster->name);
		$result1 = array('id' => $cluster->id, 'code' => $cluster->code, 'name' => $cluster->name);
		$rooms = $cluster->rooms;
		$result2 = array();
		foreach ($rooms as $key => $value) {
			array_push($result2, array('id' => $value->id, 'code' => $value->code, 'address' => $value->address));
		}
		$result = array('cluster' => $result1, 'rooms' => $result2);
		echo (json_encode($result, JSON_UNESCAPED_UNICODE));
		exit();
	}

	/**
	 * HuanPC
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update() {
		$data = Input::all();
		// $data = Input::get('departcode');
		// echo $data;

		// $data = '{"dept":{"id":81,"code":"adsf_new","name":"sdfasdfsd"},"user":{"id":8,"username":"dfsdf_new","password":"dsafdsf","email":"43243324"}}';
		// $data = json_decode($data,true);
		$cluster = Cluster::find(intval($data['id']));
		// print_r($dept);
		// exit();
		$result = $cluster->update($data);
		if ($result) {

			Session::flash('alert-class', 'alert-success');
			Session::flash('message', 'Cap nhat dư liệu thành công!!!');

			echo 'success';
			exit();
			// }
		}
		Session::flash('alert-class', 'alert-danger');
		Session::flash('message', 'Đã có lỗi xảy ra, vui lòng thử lại!!!');

		echo 'failed';
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		$result = Cluster::find(intval($id))->delete();
		if ($result > 0) {
			Session::flash('alert-class', 'alert-success');
			Session::flash('message', 'Xóa dư liệu thành công!!!');			
			echo "success";
		} else {
			Session::flash('alert-class', 'alert-danger');
			Session::flash('message', 'Đã có lỗi xảy ra, vui lòng thử lại!!!');			
			echo "failed";
		}

	}
	public function export_data(){
		$clusters = Cluster::all();
		$output_text = 'id,code,name'.PHP_EOL;
		foreach ($clusters as $key => $value) {			
			$output_text .= $value->id.','.$value->code.','.$value->name.PHP_EOL;
		}
		// echo ($output_text);
		$output_text = mb_convert_encoding($output_text, 'UTF-16LE', 'UTF-8');
		$file_output = Utils::exportCSVFile('Clusters.csv',$output_text);
		if (file_exists($file_output)) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.basename($file_output).'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file_output));
		    readfile($file_output);
		    exit;
		}
	}
	public function export_scores(){
		if (Auth::check()) {
			$id = Auth::user()->userable_id;
			$rooms = Cluster::find($id)->rooms()->get();
			$students = array();			
			foreach ($rooms as $room) {
				array_push($students,$room->students);				
			}						
			$subject_frequent = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0
				,9=>0,10=>0);			
			$result = array('1'=>new ArrayObject($subject_frequent),'2'=>new ArrayObject($subject_frequent),'3'=>new ArrayObject($subject_frequent),'4'=>$d=$subject_frequent,
				'5'=>$e=$subject_frequent,'6'=>$f=$subject_frequent,'7'=>$g=$subject_frequent,'8'=>$h=$subject_frequent);

			foreach ($students as $key => $value) {												
				foreach ($value as $t => $d) {			
					foreach ($d->examscores as $k => $v) {										
						$result[$v->subject_id][intval($v->score)] =$result[$v->subject_id][intval($v->score)]+1;
					}
					
				}
			}				
			$clusters = Cluster::all();
			$output_text = 'id,code,name'.PHP_EOL;
			foreach ($clusters as $key => $value) {			
				$output_text .= $value->id.','.$value->code.','.$value->name.PHP_EOL;
			}
			// echo ($output_text);
			$output_text = mb_convert_encoding($output_text, 'UTF-16LE', 'UTF-8');
			$file_output = Utils::exportCSVFile('Clusters.csv',$output_text);
			if (file_exists($file_output)) {
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.basename($file_output).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file_output));
			    readfile($file_output);
			    exit;
			}
		}		
	}
	public function export_students(){
		if (Auth::check()) {
			$cluster_id = Auth::user()->userable_id;
			$students_data = array();
			$rooms = Cluster::find($cluster_id)->rooms()->get();
			foreach ($rooms as $room) {
				$students_data = array_merge($students_data, $room->students->toArray());
			}
			$output_text = 'id,registration_number,profile_code,lastname,firstname,indentity_code,birthday,sex,plusscore'.PHP_EOL;
			foreach ($students_data as $key => $value) {			
				$output_text .= $value->id.','.$value->registration_number.','.$value->profile_code.','.
				$value->lastname.','.$value->firstname.','.$value->indentity_code.','.$value->birthday.','.$value->sex.','.$value->plusscore.PHP_EOL;
			}
			// echo ($output_text);
			$output_text = mb_convert_encoding($output_text, 'UTF-16LE', 'UTF-8');
			$file_output = Utils::exportCSVFile('Student_Clus.csv',$output_text);
			if (file_exists($file_output)) {
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.basename($file_output).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file_output));
			    readfile($file_output);
			    exit;
			}
		}
	}

	public function exam_score(){
		return View::make('st-admin.pages.clus.mn_score');
	}

	
}
