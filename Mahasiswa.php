<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->input->get_request_header('app-key') !== APPKEY) {
			$response = ['status' => 90, 'message' => 'App key not valid'];
			$this->output
				->set_status_header(400)
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($response))
				->_display();
			exit();
		}
	}

	/**
	 * Show all students
	 * 
	 * @return void
	 */
	public function student_list()
	{
		$get_students = $this->db->get_where('mahasiswa')->result();
		foreach ($get_students as $student) {
			$students[] = [
				'nim' => $student->nim,
				'nama' => $student->nama,
				'prodi' => $student->prodi
			];
		}

		if (count($get_students) == 0) {
			$response = ['status' => 23, 'message' => 'success with no data'];
		} else {
			$response = ['status' => 1, 'message' => 'success', 'data' => $students];
		}

		$this->_create_response(200, $response);
	}

	/**
	 * Add new students
	 * 
	 * @return void
	 */
	public function add_student()
	{
		$get_post_data = file_get_contents('php://input');
		$decode_json = json_decode($get_post_data);

		$student = [
			'nim' => $decode_json->nim,
			'nama' => $decode_json->nama,
			'prodi' => $decode_json->prodi
		];
		$this->db->insert('mahasiswa', $student);

		$affected_rows = $this->db->affected_rows();

		if ($affected_rows > 0) {
			$response = ['status' => 1, 'message' => 'success', 'data' => $student];
			$http_status = 200;
		} else {
			$response = ['status' => 99, 'message' => 'error while create data'];
			$http_status = 500;
		}

		$this->_create_response($http_status, $response);
	}

	/**
	 * Get student(s) by some param
	 * 
	 * @return void
	 */
	public function get_student()
	{
		$get_post_data = file_get_contents('php://input');
		$decode_json = json_decode($get_post_data);

		$this->_is_param_exist($decode_json, 'filter');
		$filter = $decode_json->filter;
		$get_student = $this->db->query("SELECT * FROM mahasiswa WHERE {$filter}")->result();
		$affected_rows = $this->db->affected_rows();

		if ($affected_rows > 0) {
			foreach ($get_student as $student) {
				$students[] = [
					'nim' => $student->nim,
					'nama' => $student->nama,
					'prodi' => $student->prodi
				];	
			}
			$response = ['status' => 1, 'message' => 'success', 'data' => $students];
			$status_code = 200;

		} else {
			$response = ['status' => 23, 'message' => 'data not found'];
			$status_code = 400;
		}

		$this->_create_response($status_code, $response);
	}

	/**
	 * Update student data by his/her nim
	 * 
	 * @return void
	 */
	public function update()
	{
		$get_post_data = file_get_contents('php://input');
		$decode_json = json_decode($get_post_data);

		$this->_is_param_exist($decode_json, 'nim');
		$this->_is_param_exist($decode_json, 'record');
		$filter = $decode_json->nim;
		$this->_is_data_exist($filter);

		foreach ($decode_json->record as $key => $value) {
			$record[$key] = $value;
		}

		$this->db->update('mahasiswa', $record, ['nim' => $filter]);

		$affected_rows = $this->db->affected_rows();

		if ($affected_rows > 0) {
			$response = ['status' => 1, 'message' => 'success', 'data' => $record];
			$status_code = 200;

		} else {
			$response = ['status' => 99, 'message' => 'error while update data'];
			$status_code = 500;
		}

		$this->_create_response($status_code, $response);
	}

	/**
	 * Remove student by his/her nim
	 * 
	 * @return void
	 */
	public function remove()
	{
		$get_post_data = file_get_contents('php://input');
		$decode_json = json_decode($get_post_data);

		$this->_is_param_exist($decode_json, 'nim');
		$filter = $decode_json->nim;
		$this->_is_data_exist($filter);

		$this->db->delete('mahasiswa', ['nim' => $filter]);

		$affected_rows = $this->db->affected_rows();

		if ($affected_rows > 0) {
			$response = ['status' => 1, 'message' => 'success'];
			$status_code = 200;

		} else {
			$response = ['status' => 99, 'message' => 'error while delete data'];
			$status_code = 500;
		}

		$this->_create_response($status_code, $response);
	}

	/**
	 * Chack whether the object is exist or no
	 * 
	 * @param object 	$param
	 * @param string 	$object
	 * @return void
	 */
	private function _is_param_exist(object $param, string $object)
	{
		if (!isset($param->$object)) {
			$response = ['status' => 4, 'message' => 'invalid parameter'];
			$this->_create_response(400, $response);
		}
		return;
	}

	/**
	 * Check whether the student is exist by her/his nim
	 * 
	 * @param int 	$nim
	 * @return void
	 */
	private function _is_data_exist(int $nim) : void
	{
		$is_nim_available = $this->db->get_where('mahasiswa', ['nim' => $nim])->num_rows();
		if ($is_nim_available == 0) {
			$response = ['status' => 23, 'message' => 'data not found'];
			$this->_create_response(404, $response);
		}
		return;
	}

	/**
	 * Create response for each request
	 * 
	 * @param int 	$status_code
	 * @param array $payload
	 * @return void
	 */
	private function _create_response(int $status_code, array $payload) : void
	{
		$this->output
			->set_status_header($status_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload))
			->_display();
		exit();
	}
}

/* End of file Mahasiswa.php */
/* Location: ./application/controllers/Mahasiswa.php */
