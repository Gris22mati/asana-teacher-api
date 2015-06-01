<?php namespace AsanaTeacher\Managers;

class RegisterManager extends BaseManager {
	
	/**
	 * @return array
	 */
	public function getRules(){
		return [
			'full_name'             => 'min:3|max:50|required',
			'username'              => 'required|unique:users,username',
			'password'              => 'confirmed|min:6|max:25|required',
			'password_confirmation' => 'min:6|max:25|required',
			'email'                 => 'confirmed|email|required|unique:users,email',
			'email_confirmation'    => 'required',
			'category_id'			=> 'required|exists:categories,id',
			'authorized'            => 'in:on,off',
		];
	}

	/**
	 * @param array $data
	 * @return array $data
	 */
	public function preparateData(array $data)
	{
		$data['full_name'] = strip_tags($data['full_name']);

		$delete = ['password_confirmation', 'email_confirmation'];

		foreach ($delete as $value) {
			unset($data[$value]);
		}

		return $data;
	}

	public function update() {

		if (is_null($this->entity)) {
			return \App::abort(404, "NotFoundUser");
		}

		$id = $this->entity->id;

		$validation = \Validator::make($this->data, $this->getRules());

		if ($validation->fails()){

			$users = \User::where('email', $this->data['email'])->orWhere('username', $this->data['username'])->get();
			
			if ($users[0]->id != $id || count($users) != 1) {
				return $validation->messages();
			}

		}

		$this->entity->category_id = $this->data['category_id'];

		$this->entity->update($this->prepareData($this->data));

		return $this->entity;
	}

	

}