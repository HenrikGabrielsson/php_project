<?php 

namespace controller;

require_once("./model/Login.php");
require_once("./view/HTMLView.php");
require_once("./view/RegistrationView.php");
require_once("./view/helpers/GetHandler.php");

class RegistrationController
{
	private $htmlView;
	private $regView;
	private $registration;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->regView = new \view\RegistrationView();
		$this->registration = new \model\Registration();
	}

	public function getContent($id, $loggedIn)
	{

		$title = $this->regView->getTitle();
		$body;

		if($this->regView->userWantsToRegister())
		{
			$username = $this->regView->getUsername();
			$email = $this->regView->getEmail();
			$pass1 = $this->regView->getPassword1();
			$pass2 = $this->regView->getPassword2();

			//försöker registrera. Om det lyckas så visas inte formuläret, utan en sida som berättar att allt gick bra.
			$success = $this->registration->attemptRegister($username, $email, $pass1, $pass2);

			if($success)
			{
				$this->regView->getSuccessPage();
				return;
			}
		}

		//Om em registrering misslyckas så visas formuläret igen med feedback.
		$feedback = $this->registration->getErrorList();

		$body = $this->regView->getForm($feedback);

		$this->htmlView->showHTML($title, $body);
			
	}
}