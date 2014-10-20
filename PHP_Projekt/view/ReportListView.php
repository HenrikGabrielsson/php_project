<?php

namespace view;

class ReportListView
{

	public function getListRequest()
	{
		return $_GET[helpers\GetHandler::$LIST];
	}

	public function getPollToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETEPOLL_POLLID]))
		{
			return $_POST[helpers\PostHandler::$DELETEPOLL_POLLID];	
		}
		return false;
		
	}

	public function getDeletePollReason()
	{
		return $_POST[helpers\PostHandler::$DELETEPOLL_REASON];
	}

	public function getDeleteCommentReason()
	{
		return $_POST[helpers\PostHandler::$DELETECOMMENT_REASON];
	}

	public function getCommentToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETECOMMENT_COMMENTID]))
		{
			return $_POST[helpers\PostHandler::$DELETECOMMENT_COMMENTID];	
		}
		return false;		
	}

	public function getUserToNominate()
	{
		if(isset($_POST[helpers\PostHandler::$NOMINATEUSER_USERID]))
		{
			return $_POST[helpers\PostHandler::$NOMINATEUSER_USERID];	
		}
		return false;	
	}

	public function getUserToDelete()
	{
		if(isset($_POST[helpers\PostHandler::$DELETEUSER_USERID]))
		{
			return $_POST[helpers\PostHandler::$DELETEUSER_USERID];	
		}
		return false;	
	}

	public function getIgnoreUserReport()
	{
		if(isset($_GET[helpers\GetHandler::$IGNOREUSER]))
		{
			return $_GET[helpers\GetHandler::$IGNOREUSER];	
		}
		return false;		
	}

	public function getIgnorePollReport()
	{
		if(isset($_GET[helpers\GetHandler::$IGNOREPOLL]))
		{
			return $_GET[helpers\GetHandler::$IGNOREPOLL];	
		}
		return false;		
	}

	public function getIgnoreCommentReport()
	{
		if(isset($_GET[helpers\GetHandler::$IGNORECOMMENT]))
		{
			return $_GET[helpers\GetHandler::$IGNORECOMMENT];	
		}
		return false;		
	}

	public function getTitle()
	{
		return "Reported things";
	}

	public function denyPage()
	{
		return 
		"<h1>Access Denied</h1>
		<p>We're Sorry. You're not allowed to visit this page.Go play somewhere else.</p>
		";
	}

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

	public function getPollList($polls, $users, $reports, $feedback)
	{

		$table = 
		'
		<table>
			<tr>
				<th>Remove report</th> <th>Poll</th> <th>Reason</th> <th>Creator</th> <th>Delete</th>
			</tr>';

		if($reports)
		{
			foreach ($reports as $report) 
			{
				//först hämtar vi den poll som rapporteringen gäller
				$thisPoll;
				foreach($polls as $poll)
				{
					if($poll->getId() == $report->getPollId())
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
					<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNOREPOLL.'='.$report->getId().'">Ignore this report</a></td> <td>'.$thisPoll->getQuestion().'</td> <td>'.$report->getCommentFromReporter().'</td> <td>'.$thisUser->getUserName().'</td> 
					<td>
						<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
							<input type="hidden" value='.$thisPoll->getId().' name="'.helpers\PostHandler::$DELETEPOLL_POLLID.'">
							<input type="text" placeholder="Why are you deleting this?." name="'.helpers\PostHandler::$DELETEPOLL_REASON.'">
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

	public function getCommentList($comments, $users, $reports, $feedback)
	{

		$table = 
		'<table>
			<tr>
				<th>Remove report</th> <th>Comment</th> <th>Reason</th> <th>CommentWriter</th> <th>Delete</th>
			</tr>';

		if($reports)
		{
			foreach ($reports as $report) 
			{
				//först hämtar vi den comment som rapporteringen gäller
				$thisComment;
				foreach($comments as $comment)
				{
					if($comment->getId() == $report->getCommentId())
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
					<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNORECOMMENT.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisComment->getComment().'</td> <td>'.$report->getCommentFromReporter().'</td> <td>'.$thisUser->getUserName().'</td> 
					<td>
						<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
							<input type="hidden" value='.$thisComment->getId().' name="'.helpers\PostHandler::$DELETECOMMENT_COMMENTID.'">
							<input type="text" placeholder="Why are you deleting this?." name="'.helpers\PostHandler::$DELETECOMMENT_REASON.'">
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

	public function getUserList($users, $userReports, $feedback)
	{
		$reportedTable = 	
		'<h2>Users with 1 or more reports</h2>
		<table>
		<tr>
			<th>Remove report</th> <th>User</th> <th>Reason</th> <th>Type</th> <th>Delete</th>
		</tr>
		';


		$nominatedTable = 
		'<h2>Users nominated for deletion</h2>
		<table>
		<tr>
			<th>Remove report</th> <th>User</th> <th>Reason</th> <th>Type</th> <th>Delete</th>
		</tr>
		';

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
						<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNOREUSER.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisUser->getUserName().'</td> <td>'.$report->getCommentFromAdmin().'</td> <td>'.$report->getType().'</td> 
						<td>
							<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
								<input type="hidden" value='.$thisUser->getId().' name="'.helpers\PostHandler::$NOMINATEUSER_USERID.'">
								<input type="submit" value="Nominate for deletion">
							</form>
						</td>
					</tr>';					
				}
				else
				{
					$nominatedTable .=
					'<tr>
						<td><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$IGNOREUSER.'='.$report->getId().'">Ignore this report</a></td><td>'.$thisUser->getUserName().'</td> <td>'.$report->getCommentFromAdmin().'</td> <td>'.$report->getType().'</td> 
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

	private function makeFeedback($feedback)
	{
		$retString = '<div id="feedback">';  

		if(is_array($feedback))
		{		
			$retString = "<ul>";
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
	            $retString .= "<li>This poll doesn't exist..</li>";
	        }
			if(in_array(\model\ReportHandler::SAMEADMIN, $feedback))
	        {
	            $retString .= "<li>Another admin must delete the user.</li>";
	        }
	    }

	    else
	    {
	    	$retString = '<p>'.$feedback.'</p>';
	    }

        return $retString . "</div>";
	}

	
}