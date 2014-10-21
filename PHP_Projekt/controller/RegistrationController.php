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

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	* @param Login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	*/
	public function getContent(\model\LoginHandler $login)
	{

		$title = $this->regView->getTitle();

		//användaren vill försöka registrera sig.
		if($this->regView->userWantsToRegister())
		{
			//hämta formulärdata
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