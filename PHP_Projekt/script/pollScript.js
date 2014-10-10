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