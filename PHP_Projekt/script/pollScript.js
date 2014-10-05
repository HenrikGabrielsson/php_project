var canvas = document.getElementById("pollCanvas");
var context = canvas.getContext("2d");


var pollData = [100,40,122,124];

var pollColors = ["#FF0000","#00FF00", "#0000FF", "#FFFF00", "#FF6600", "#800080",
 	"#008000", "#FF00FF", "#FFFFFF", "#000000", "#C0C0C0", "#808080", 
 	"#800000", "#8B4513", "#00FFFF", "#00BFFF", "#DAA520", "#00008B", "#FFE4B5", "#008080"];

var displayData = function(data)
{
	canvas.height = 200;
	canvas.width = 200;

	//konverterar data till procent
	var data = convertToPercentage(data);

	drawCircleDiagram(data);
}

var drawCircleDiagram = function(data)
{	
	//vart varje "tårtbit ska börja ritas."
	var startPoint = 0;
	var endPoint;

	for(var i = 0;i < data.length; i++)
	{
		//räknar ut endpoint.
		endPoint = startPoint + (Math.PI*2*data[i]);

		//ritar kurvan
		context.beginPath();
		context.arc(canvas.width/2, canvas.height/2,canvas.height/2, startPoint, endPoint);

		//uppdaterar startpoint till där den förra tårtbiten slutade.
		startPoint = endPoint;

		//resten av tårtbiten och lägg till färg
		context.lineTo(canvas.width/2, canvas.height/2);	
		context.fillStyle = pollColors[i];
		context.fill();
		context.closePath();
		context.stroke();		
	}
}


var convertToPercentage = function(data)
{
	var newDataArray = [];
	var dataSum = 0;

	//totala summan räknas ut.
	for(var i = 0; i < data.length; i++)
	{
		dataSum = dataSum + data[i];
	}

	//räknar ut hur många procent varje del tar upp av den totala summan. 
	for(var j = 0; j < data.length; j++) 
	{
		newDataArray[j] = data[j]/dataSum;
	}

	return newDataArray;

}

window.addEventListener("load", displayData(pollData), false);


