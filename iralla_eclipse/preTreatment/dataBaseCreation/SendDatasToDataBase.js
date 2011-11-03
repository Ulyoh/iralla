/**
 * @author Yoh
 */
SendDatasToDataBase = function(nothing, iterationNbr){
	
	currentIteration = iterationNbr;
	if (iterationNbr >= 0){
	var parametersToSend = SubMap._busLinesArray[iterationNbr].id;
		for( var i=0; i < SubMap._busLinesArray[iterationNbr].fields.length; i++){
			parametersToSend += '-' + SubMap._busLinesArray[iterationNbr].fields[i];
		}
		
		request({
			phpFileCalled: mysite + 'preTreatment/dataBaseCreation/createDataBase.php',
			setRequestHeaderName: 'application/x-www-form-urlencoded',
			setRequestHeaderValue: 'application/json',
			argumentsToPhpFile: 'q=' + parametersToSend,
			type: "",
			callback: SendDatasToDataBase,
			argumentsCallback: --iterationNbr,
			asynchrone: true
			});
	}
};

loaded.dataBaseCreation.push('SendDatasToDataBase');
