/**
 * @author Yoh
 */


function round(x, y){
	if (typeof(y) == 'undefined')
	return Math.round(x * 100000000) / 100000000;
	else
	return Math.round(x * Math.pow(10,y)) / Math.pow(10,y);
}

//to verify the file is loaded
loaded.tools.push('round');