<?php

namespace view;

class ReportListView
{

	/**
	*	@return 	string 	hämtar den lista som admin vill se
	*/
	public function getListRequest()
	{
		return $_GET[helpers\GetHandler::$LIST];
	}

	/**
	*	@return 	string 	anledning till borttagning av poll
	*/
	public function getDeleteReason()
	{
		return $_POST[helpers\PostHandler::$DELETE_REASON];
	}

	/**
	*	@return 	int/bool 	hämtar id på comment som användare vill ta bort. false om det inte är valt.
	*/
	public function getObjectToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETEOBJECT_REPORTID]))
		{
			return $_POST[helpers\PostHandler::$DELETEOBJECT_REPORTID];	
		}
		return false;		
	}

	/**
	*	@return 	int/bool 	hämtar id på user som användare vill ta bort. false om det inte är valt.
	*/
	public function getUserToNominate()
	{
		if(isset($_POST[helpers\PostHandler::$NOMINATEUSER_USERID]))
		{
			return $_POST[helpers\PostHandler::$NOMINATEUSER_USERID];	
		}
		return false;	
	}

	/**
	*	@return 	int/bool 	hämtar id på user som användare vill ta bort (och gör). false om det inte är valt.
	*/
	public function getUserToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETEUSER_USERID]))
		{
			return $_POST[helpers\PostHandler::$DELETEUSER_USERID];	
		}
		return false;	
	}

	/**
	*	@return 	int/bool 	hämtar id på report som användare vill ta bort. false om det inte är valt.
	*/
	public function getIgnoreReport()
	{
		if(isset($_GET[helpers\GetHandler::$IGNORE]))
		{
			return $_GET[helpers\GetHandler::$IGNORE];	
		}
		return false;		
	}

	/**
	*	@return 	string	sidans title.
	*/
	public function getTitle()
	{
		return "Reported things";
	}


	/**
	*	@param 		array 	array med feedback
	*	@return 	string	innehåll för sidans huvud. Länkar till alla listor samt feedback visas gär.
	*/
	private function getContentHead($feedback)
	{
		return 
		'<h1>The Reports Lists</h1>
		<ul>
			<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'&'.helpers\GetHandler::$LIST.'='.helpers\GetHandler::$USERLIST.'">Reported Users</a></li>
			<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'&'.helpers\GetHandler::$LIST.'='.helpers\GetHandler::$POLLLIST.'">Reported Polls</a></li>
			<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'&'.helpers\GetHandler::$LIST.'='.helpers\GetHandler::$COMMENTLIST.'">Reported Comments</a></li>
		</ul>'
		.$this->makeFeedback($feedback);

	}

	/**
	*	Visar en tabell med alla rapporterade undersökningar så en admin kan bestämma vad som ska göras med dom.
	*	@param 		array 	alla undersökingar som nämns
	* 	@param 		array 	alla users som nämns
	*	@param 		array 	alla rapporter
	* 	@param 		array 	eventuell feedback.
	*	@return 	string 	hmtl-table för rapporterade undersökningar.
	*/
	public function getPollList($polls, $users, $reports, $feedback)
	{

		$table = 
		'
		<table>
			<tr>
				<th>Remove report</th> <th>Poll</th> <th>Reason</th> <th>Creator</th> <th>Delete</th>
			</tr>';

		//om det finns några rapporter.
		if($reports)
		{
			foreach ($reports as $report) 
			{
				//först hämtar vi den poll som rapporteringen gäller
				$thisPoll;
				foreach($polls as $poll)
				{
					if($poll->getId() == $report->getObjectId())
					{
						$thisPoll = $poll;
						break;
					}
				}

				//sen hämtar vi användaren som skapat den.
				$thisUser;
				foreach($users as $user)
				{
					if($user->getId() == $report->getUserId())
					{
						$thisUser = $user;
						break;
					}
				}

				//här lägger vi till nästa rad i tabellen.
				$table .= 
				'<tr>
					<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNORE.'='.$report->getId().'">Ignore this report</a></td> <td>'.$thisPoll->getQuestion().'</td> <td>'.$report->getComment().'</td> <td>'.$thisUser->getUserName().'</td> 
					<td>
						<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
							<input type="hidden" value='.$report->getId().' name="'.helpers\PostHandler::$DELETEOBJECT_REPORTID.'">
							<input type="text" placeholder="Why are you deleting this?." name="'.helpers\PostHandler::$DELETE_REASON.'">
							<input type="submit" value="Delete">
						</form>
					</td>
				</tr>';

			}
		}


		$table .= '</table>';

		$bodyContent = 
		'<h2>All reported polls</h2>
		<p>There are currently '.count($polls).' polls that has been reported as offensive.</p>
		'.$table;

		return $this->getContentHead($feedback).$bodyContent;
	}

	/**
	*	Visar en tabell med alla rapporterade comments så en admin kan bestämma vad som ska göras med dom.
	*	@param 		array 	alla comments som nämns
	* 	@param 		array 	alla users som nämns
	*	@param 		array 	alla rapporter
	* 	@param 		array 	eventuell feedback.
	*	@return 	string 	hmtl-table för rapporterade undersökningar.
	*/	
	public function getCommentList($comments, $users, $reports, $feedback)
	{

		$table = 
		'<table>
			<tr>
				<th>Remove report</th> <th>Comment</th> <th>Reason</th> <th>CommentWriter</th> <th>Delete</th>
			</tr>';

		//om det finns några rapporter
		if($reports)
		{
			foreach ($reports as $report) 
			{
				//först hämtar vi den comment som rapporteringen gäller
				$thisComment;
				foreach($comments as $comment)
				{
					if($comment->getId() == $report->getObjectId())
					{
						$thisComment = $comment;
						break;
					}
				}

				//sen hämtar vi användaren som skapat den.
				$thisUser;
				foreach($users as $user)
				{
					if($user->getId() == $report->getUserId())
					{
						$thisUser = $user;
						break;
					}
				}

				//här lägger vi till nästa rad i tabellen.
				$table .= 
				'<tr>
					<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNORE.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisComment->getComment().'</td> <td>'.$report->getComment().'</td> <td>'.$thisUser->getUserName().'</td> 
					<td>
						<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
							<input type="hidden" value='.$report->getId().' name="'.helpers\PostHandler::$DELETEOBJECT_REPORTID.'">
							<input type="text" placeholder="Why are you deleting this?." name="'.helpers\PostHandler::$DELETE_REASON.'">
							<input type="submit" value="Delete">
						</form>
					</td>
				</tr>';

			}
		}


		$table .= '</table>';

		$bodyContent = 
		'<h2>All reported comments</h2>
		<p>There are currently '.count($comments).' comments that has been reported as offensive.</p>
		'.$table;
		
		return $this->getContentHead($feedback).$bodyContent;
	}


	//Borde eventuellt slå ihop delar av dessa tre funktioner eftersom mycket kod är duplicerad
	//Skapa tabeller som ger bättre översikt kanske?

	/**
	*	Visar två tabeller med alla rapporterade users så en admin kan bestämma vad som ska göras med dom.
	*	@param 		array 	alla users som nämns
	*	@param 		array 	alla rapporter
	* 	@param 		array 	eventuell feedback.
	*	@return 	string 	hmtl-table för rapporterade undersökningar.
	*/
	public function getUserList($users, $userReports, $feedback)
	{

		//tabell för rapporterade users. Inte nån risk borttagning än.
		$reportedTable = 	
		'<h2>Users with 1 or more reports</h2>
		<table>
		<tr>
			<th>Remove report</th> <th>User</th> <th>Reason</th> <th>Type</th> <th>Delete</th>
		</tr>
		';

		//tabell för rapporterade users, nominerade av en admin för att bli borttagna.
		$nominatedTable = 
		'<h2>Users nominated for deletion</h2>
		<table>
		<tr>
			<th>Remove report</th> <th>User</th> <th>Reason</th> <th>Type</th> <th>Delete</th>
		</tr>
		';

		//om det finns rapporter.
		if($userReports)
		{
			foreach($userReports as $report)
			{
				//hämta denna rapports användare.
				$thisUser;
				foreach($users as $user)
				{
					if($user->getId() == $report->getUserId())
					{
						$thisUser = $user;
						break;
					}
				}


				//detta är de som ännu inte har fått en nominering att bli borttagna.
				if(is_null($report->getNomination()))
				{
					$reportedTable .=
					'<tr>
						<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNORE.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisUser->getUserName().'</td> <td>'.$report->getComment().'</td> <td>'.$report->getType().'</td> 
						<td>
							<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
								<input type="hidden" value='.$thisUser->getId().' name="'.helpers\PostHandler::$NOMINATEUSER_USERID.'">
								<input type="submit" value="Nominate for deletion">
							</form>
						</td>
					</tr>';					
				}

				//de med nomineringar.
				else
				{
					$nominatedTable .=
					'<tr>
						<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNORE.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisUser->getUserName().'</td> <td>'.$report->getComment().'</td> <td>'.$report->getType().'</td> 
						<td>
							<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
								<input type="hidden" value='.$thisUser->getId().' name="'.helpers\PostHandler::$DELETEUSER_USERID.'">
								<input type="submit" value="Accept Deletion">
							</form>
						</td>
					</tr>';		
				}
			}
		}
		$nominatedTable .= "</table>";
		$reportedTable .= "</table>";

		return $this->getContentHead($feedback).$reportedTable. $nominatedTable;
	}


	/**
	*	Skapar feedbacklista
	*	@param 	array 	array med konstanter som berättar vilken typ av feedback som bör ges.
	*	@return string  html-lista med feedback.
	*/
	private function makeFeedback($feedback)
	{
		$retString = '<div id="feedback">';  
	
		$retString .= "<ul>";
		if(in_array(\model\ReportHandler::LONGREASON, $feedback))
        {
            $retString .= "<li>The reason you wrote was too long. Maximum number of characters is 200.</li>";
        }
     	if(in_array(\model\ReportHandler::NOREASON, $feedback))
        {
            $retString .= "<li>You must add a reason for your choice..</li>";
        }
		if(in_array(\model\ReportHandler::NOCOMMENT, $feedback))
        {
            $retString .= "<li>This comment doesn't exist.</li>";
        }
     	if(in_array(\model\ReportHandler::NOPOLL, $feedback))
        {
            $retString .= "<li>This poll doesn't exist.</li>";
        }
		if(in_array(\model\ReportHandler::SAMEADMIN, $feedback))
        {
            $retString .= "<li>Another admin must delete the user.</li>";
        }

     	//inte fel utan rättmeddelandwen
     	if(in_array(\model\ReportHandler::DELETED, $feedback))
        {
            $retString .= "<li>Deletion finished. Thanks for the help.</li>";
        }	
     	if(in_array(\model\ReportHandler::USERNOMINATED, $feedback))
        {
            $retString .= "<li>You want to delete this user. Someone else must confirm first. Thanks for the help.</li>";
        }	

        return $retString . "</div>";
	}	
}