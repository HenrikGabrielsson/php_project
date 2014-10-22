<?php

require_once("./Settings.php");
require_once("./DBSetup.php");


function showInstallerForm()
{
	return
	'
	<!doctype HTML>
	<head>
		<title>Install PHP Polls</title>
	</head>

	<body>
		<h1>Install PHP Polls</h1>


		<p>Hello! This is what you will need to do to install the database for PHP Polls properly:</p>
		<ul>
			<li>Make sure you have a database ready to get filled with tables and procedures.</li>
		 	<li>Fill in the correct values in Settings.php</li>
		 	<li>Click the buttons below to add the tables to the database the database:</li>
		 	<li>After that you should add some stored procedures by opening the database and using this SQL query: </li>
		</ul>

		<textarea>
	    DELIMITER //
	    CREATE PROCEDURE henrikgabrielss.deleteComment(IN id INT)
	    BEGIN
	    #ta bort reports på denna comment och kommentaren själv
	    DELETE FROM reportedComment WHERE commentId = id;
	          DELETE FROM comment WHERE commentId = id;
	    END //
	    DELIMITER ;



	    DELIMITER //
	    CREATE PROCEDURE henrikgabrielss.deletePoll(IN id INT)
	    BEGIN

	    #ta bort alla reports på comments som tas bort.
	    DELETE reportedComment.* FROM reportedComment INNER JOIN comment ON reportedComment.commentId = comment.commentId
	    WHERE comment.pollId = id;

	    #ta bort alla comments i poll
	    DELETE FROM comment WHERE comment.pollId = id;

	    #ta bort alla votes i poll
	    DELETE vote.* FROM vote INNER JOIN answer ON vote.answerId = answer.answerId 
	    WHERE answer.pollId = id;

	    #ta bort alla svar i poll
	    DELETE FROM answer WHERE answer.pollId = id;

	    #ta bort alla reports på poll 
	    DELETE FROM reportedPoll WHERE pollId = id; 

	    DELETE FROM poll WHERE pollId = id;
	    END //
	    DELIMITER ;


	    DELIMITER //
	    CREATE PROCEDURE henrikgabrielss.deleteUser(IN id INT)
	    BEGIN
	      #ta bort användarens polls och allt som hör till dem
	            DELETE vote.* FROM vote INNER JOIN answer ON vote.answerId = answer.answerId INNER JOIN poll ON answer.pollId = poll.pollId
	            WHERE poll.creatorId = id;
	            DELETE answer.* FROM answer INNER JOIN poll ON answer.pollId = poll.pollId 
	            WHERE poll.creatorId = id;
	            DELETE reportedComment.* FROM reportedComment INNER JOIN comment ON reportedComment.commentId = comment.commentId INNER JOIN poll ON comment.pollId = poll.pollId
	            WHERE poll.creatorId = id;
	            DELETE comment.* FROM comment INNER JOIN poll on comment.pollId = poll.pollId 
	            WHERE poll.creatorId = id;
	      DELETE reportedPoll.* FROM reportedPoll INNER JOIN poll ON reportedPoll.pollId = poll.pollId
	            WHERE poll.creatorId = id;
	            DELETE FROM poll WHERE poll.creatorId = id;
	            
	            #alla användarens commentarer och dess reports
	            DELETE FROM reportedComment WHERE reportedComment.userId = id;
	            DELETE FROM comment WHERE comment.userId = id;
	            
	            #användaren själv och hans/hennes reports
	            DELETE FROM reportedUser WHERE reportedUser.userId = id;
	            DELETE FROM user WHERE user.userId = id;
	            
	            
	    END //
	    DELIMITER ;

	    DELIMITER //
	    CREATE PROCEDURE henrikgabrielss.didUserVote (IN p_answerid INT, IN p_ip VARCHAR(255))
	    BEGIN
	      DECLARE votePoll int DEFAULT 0;
	      SELECT pollId FROM answer WHERE answerId = p_answerId INTO votePoll;
	            
	            SELECT vote.voteId FROM vote INNER JOIN answer ON vote.answerId = answer.answerId
	      WHERE vote.ip = p_ip AND answer.pollId = votePoll; 
	            
	    END //
	    DELIMITER ;
	    </textarea>

		<form method="post" action="#">
			<input type="hidden" name="createTables">
			<input type="submit" value="Add tables!">

		</form>



	</body>

	</html>
	';
}


if(isset($_POST["createTables"]))
{
	$DBSetup = new DBSetup();
	$DBSetup->installDB();
}
else
{
	echo showInstallerForm();
}
