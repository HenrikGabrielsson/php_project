<?php 

namespace controller;

require_once("./view/RegistrationView.php");
require_once("./view/helpers/GetHandler.php");

class RegistrationController
{
	private $htmlView;
	private $regView;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->regView = new \view\RegistrationView();
	}

	public function getContent($login)
	{

		$title = $this->regView->getTitle();

		if($this->regView->userWantsToRegister())
		{
			$username = $this->regView->getUsername();
			$email = $this->regView->getEmail();
			$pass1 = $this->regView->getPassword1();
			$pass2 = $this->regView->getPassword2();

			//försöker registrera. Om det lyckas så visas inte formuläret, utan en sida som berättar att allt gick bra.
			$success = $login->attemptRegister($username, $email, $pass1, $pass2);

			if($success)
			{
				$body = $this->regView->getSuccessPage();
				$this->htmlView->showHTML($title, $body);
				return;
			}
		}

		//Om en registrering misslyckas så visas formuläret igen med feedback.
		$feedback = $login->getErrorList();

		$body = $this->regView->getRegister($feedback);

		$this->htmlView->showHTML($title, $body);
			
	}
}