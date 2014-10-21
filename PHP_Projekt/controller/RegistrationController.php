<?php 

namespace controller;

require_once("./view/RegistrationView.php");

require_once("./controller/IMainContentController.php");

class RegistrationController implements IMainContentController
{
	private $regView;
	private $login;

	public function __construct(\model\LoginHandler $login)
	{
		$this->regView = new \view\RegistrationView();
		$this->login = $login;
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getBody()
	{
		//användaren vill försöka registrera sig.
		if($this->regView->userWantsToRegister())
		{
			//hämta formulärdata
			$username = $this->regView->getUsername();
			$email = $this->regView->getEmail();
			$pass1 = $this->regView->getPassword1();
			$pass2 = $this->regView->getPassword2();

			//försöker registrera. Om det lyckas så visas inte formuläret, utan en sida som berättar att allt gick bra.
			$success = $this->login->attemptRegister($username, $email, $pass1, $pass2);

			if($success)
			{
				return $this->regView->getSuccessPage();;
			}
		}

		//Om en registrering misslyckas så visas formuläret igen med feedback.
		$feedback = $this->login->getErrorList();

		return $this->regView->getRegister($feedback);		
	}

	public function getTitle()
	{
		return $this->regView->getTitle();
	}
}