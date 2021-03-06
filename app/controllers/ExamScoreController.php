<?php
require_once (dirname(__FILE__).'/Excel/reader.php');
require_once (dirname(__FILE__).'/Utils.php');
class ExamScoreController extends \BaseController {
	protected $column = array('student_id', 'room_id', 'subject_id', 'score', 'state');

	public function search() {
		$cluster_id = Input::get('cluster');
		$inputdata = Input::get('input');
		// print_r($cluster_id.' '.$inputdata);
		$rooms = Cluster::find($cluster_id)->rooms()->get();
		$result = array(
			'result' => 'false',
		);
		foreach ($rooms as $room) {
			$students = $room->students;
			foreach ($students as $student) {
				if (($student->indentity_code == $inputdata) || ($student->registration_number == $inputdata)) {
					$result['student'] = array(
						'id' => $student->id,
						'name' => $student->firstname . ' ' . $student->lastname,
						'sbd' => $student->indentity_code,
						'toan' => '',
						'van' => '',
						'ly' => '',
						'hoa' => '',
						'sinh' => '',
						'ta' => '',
					);
					$scores = $student->examscores;
					$tong = 0;
					foreach ($scores as $score) {
						$tong = $tong + $score->score;
						if ($score->subject_id == 1) {
							$result['student']['toan'] = $score->score;
						}
						if ($score->subject_id == 2) {
							$result['student']['van'] = $score->score;
						}
						if ($score->subject_id == 3) {
							$result['student']['ly'] = $score->score;
						}
						if ($score->subject_id == 4) {
							$result['student']['hoa'] = $score->score;
						}
						if ($score->subject_id == 5) {
							$result['student']['sinh'] = $score->score;
						}
						if ($score->subject_id == 6) {
							$result['student']['ta'] = $score->score;
						}
					}
					$result['student']['tong'] = $tong;
					$result['result'] = 'true';
					// echo json_encode($student);
				}
			}
		}
		echo json_encode($result);
	}

	public function show_page() {
		$clusters = Cluster::all();
		return View::make('pages.result_info')->with('clusters', $clusters);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$student_id = Input::get('student_id');
		$score = Student::find($student_id)->examscores()->get();
		$result = json_encode($score);
		return $result;
	}

	public function import_excel_file() {
		$fileInputName = 'examscore_exel_file';
		$data = Utils::importExelFile($fileInputName);
		return $data;
	}

	public function edit_one_show() {
		$student_id = Input::get('student_id');
		$score = Student::find($student_id)->examscores()->get();
		$result = json_encode($score);
		return $result;
	}

	public function edit_one() {
		$data = Input::all();
		$examscore = $this->update($data);
		return json_encode($examscore);
	}

	public function update_many() {
		$fileInputName = 'excel_file';		
		$count_create = 0;
		$count_update = 0;
		if (isset($data)) {
			foreach ($data as $key => $value) {
				// dd($value);
				// Kiem tra du lieu da ton tai trong csdl?
				$exist_score = ExamScore::where('student_id', $value[0])->where('subject_id', $value[2])->first();
				if (!isset($exist_score)) {					
					// Neu chua ton tai thi tao record examscore moi					
					$score = new ExamScore(array('student_id'=>$value[0], 'room_id'=>$value[1], 'subject_id'=>$value[2], 'score'=>$value[3], 'state'=>$value[4]));
					$examscores = Student::find($value[0])->examscores();
					$examscores->save($score);					
					$count_create++;
				} else {
					// Neu da ton tai thi update Student
					$check_update = $this->update($exist_score,array('student_id'=>$value[0], 'room_id'=>$value[1], 'subject_id'=>$value[2], 'score'=>$value[3], 'state'=>$value[4]));
					if ($check_update) {
						$count_update++;
					}

				}
			}
		}
		Session::flash('alert-class', 'alert-success');
		Session::flash('message', 'Thêm mới thành công '.$count_update.' bản ghi!!');
		// echo json_encode('success');	
		return Redirect::to('/st-admin/clus/mn_stu_acc');	
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function update($examscore,$data) {		
		$check = $examscore->save($data);
		if ($check) {
			return True;
		} else {
			return False;
		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
	}
	// public function import_score(){
	// 	$fileInputName = 'excel_file';
	// 	$data = Utils::importExelFile($fileInputName);
	// 	$count = 0;
	// 	if (isset($data)) {
	// 		foreach ($data as $key => $value) {
	// 			// Kiem tra du lieu da ton tai trong csdl?
	// 			$check = ExamScore::where('student_id', $value[0])->where('room_id', $value[1])->first();
	// 			if (!isset($check)) {
	// 				// Neu chua ton tai thi moi insert
	// 				$data_insert = array_combine($this->column, array($value[0],$value[1],$value[2]));						
	// 				$uni = University::create($data_insert);

	// 				$user = new User(array('username'=>$value[3],'password'=>$value[4],'email'=>$value[5]));
	// 				$user->password = Hash::make($value[4]);
	// 				$uni->user()->save($user);
	// 				if (isset($uni)) {
	// 					$count += 1;
	// 				}
	// 			}

	// 		}
	// 	}		
	// 	Session::flash('alert-class', 'alert-success');
	// 	Session::flash('message', 'Thêm mới thành công '.$count.' bản ghi!!');
	// 	echo json_encode('success');
	// }

}
