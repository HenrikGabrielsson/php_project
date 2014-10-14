
//funktioner och variabler för när man skapar undersökningar
//svarsfältt och labels när man skapar en poll
var answerFields = document.getElementsByClassName('createAnswer');
var answersLabels = document.getElementsByClassName('createAnswerLabels');

var shownAnswers = 2;

if(answerFields.length > 0)
{
	hideAnswers();
	showShowButton();
}

function hideAnswers()
{
	for(var i = 0; i < answerFields.length; i++)
	{
		if(i > shownAnswers - 1)
		{
			answersLabels[i].style.display = "none";
			answerFields[i].style.display = "none";
		}
		else
		{
			answersLabels[i].style.display = "block";
			answerFields[i].style.display = "block";	
		}
	}

}

function showShowButton()
{
	//hämta fieldsetet med svarsfälten
	var fieldset = document.getElementById("answers_fieldset");	

	var showButton = document.createElement("input");
	showButton.setAttribute("type","button");
	showButton.setAttribute("name","addAnswer");
	showButton.setAttribute("value","Add another answer");

	fieldset.appendChild(showButton);

	showButton.addEventListener("click", addField, false);
}

function addField()
{
	for(var i = 0; i <= shownAnswers;i++)
	{
		answersLabels[i].style.display = "block";
		answerFields[i].style.display = "block";		
	}

	//fokus på sista fältet som lades till
	answerFields[shownAnswers].focus();
	shownAnswers++;
}


//om det finns kommentarer så gömmer vi alla formulär och visar knappar för att visa formulären istället. Detta för att inte stöka till sidan med en massa formulär överallt
//om man inte använder javascript så får man leva med det ändå.
if(document.getElementsByClassName('reportComment').length > 0)
{
	var buttons = document.getElementsByClassName('showCommentReportForm');
	var reportForms = document.getElementsByClassName('reportComment');

	for (var i = 0; i < document.getElementsByClassName('reportComment').length;i++)
	{
		buttons[i].style.display = "block";
		addAnEventListener(buttons[i], reportForms[i]);
		reportForms[i].style.display = "none";
	}
}

//samma sak med själva undersökningen
if(document.getElementsByClassName("reportPoll").length > 0)
{

	var buttons = document.getElementsByClassName('showPollReportForm');
	var reportForms = document.getElementsByClassName('reportPoll');

	//finns bara en poll på sidan så plockar ut den första
	for (var i = 0; i < document.getElementsByClassName('reportPoll').length;i++)
	{
		buttons[i].style.display = "block";
		addAnEventListener(buttons[i], reportForms[i]);
		reportForms[i].style.display = "none";
	}
}


function addAnEventListener(button, form)
{
	button.addEventListener("click", function()
	{
		button.style.display = "none";
		form.style.display = "block";		
	}, false);
}







