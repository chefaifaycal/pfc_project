<?php

namespace App\Controllers;
use App\Models\UserModel;

class Users extends BaseController
{
	public function index()
	{
		$data = [];
		helper(['form']);
		
		

			if ($this->request->getMethod() == 'post') {
				
				$rules = [
					'email' => 'required|min_length[6]|max_length[50]|valid_email',
					'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
				];

				$errors = [
					'password' => [
						'validateUser' => 'Email ou mot de passe incorrect'
					]
				];

				if (! $this->validate($rules, $errors)) {
					$data['validation'] = $this->validator;
				}else{
					$model = new UserModel();

					$user = $model->where('email', $this->request->getVar('email'))
												->first();

					$this->setUserSession($user);
					//$session->setFlashdata('success', 'Successful Registration');
					
					

					if($user['type'] == 'fournisseur'){
						return redirect()->to('dashboard/');
					}else return redirect()->to('dashboard/client');
					

				}
			}
	

		helper(['form']);
		
		return view('login');
		
	}



	private function setUserSession($user){
		$data = [
			'id' => $user['id'],
			'firstname' => $user['nom'],
			'lastname' => $user['prenom'],
			'email' => $user['email'],
			'isLoggedIn' => true,
		];

		session()->set($data);
		return true;
	}


	public function register()
	{
		$data = [];
		helper(['form']);
		
		if($this->request->getMethod() == 'post'){
			$rules = [
				'nom' => 'required|min_length[3]|max_length[50]',
				'prenom' => 'required|min_length[3]|max_length[50]',
				'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
				'password' => 'required|min_length[8]|max_length[255]',
				'password_confirm' => 'matches[password]',
				'username' => 'required|min_length[5]|max_length[20]|is_unique[users.username]',
				'dateNaissance' => 'required',
				'persoNumb' => 'required|min_length[10]|max_length[10]',
				'proNumb' => 'required|min_length[10]|max_length[10]',
				'dateMetier' => 'required',

			];

			if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
			}else{
				$model = new UserModel();

				$newData = [
					'type' => $this->request->getVar('type'),
					'username' => $this->request->getVar('username'),
					'nom' => $this->request->getVar('nom'),
					'prenom' => $this->request->getVar('prenom'),
					'date_naissance' => $this->request->getVar('dateNaissance'),
					'num_tel_perso' => $this->request->getVar('persoNumb'),
					'num_tel_pro' => $this->request->getVar('proNumb'),
					'email' => $this->request->getVar('email'),
					'pwd' => $this->request->getVar('password'),
					'date_debut_metier' => $this->request->getVar('dateMetier'),
					
					
				];
				$model->save($newData);
				$session = session();
				$session->setFlashdata('success', 'Successful Registration');
				return redirect()->to('/');

			}
		}



		
		echo view('register');
		
	}

	

	public function logout(){
		$session = session();
		$session->destroy();

		return redirect()->to('/');
	}

}
