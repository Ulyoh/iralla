/**
 * @author Yoh
 */
SendDatasToDataBase = function(nothing, iterationNbr){
	
	currentIteration = iterationNbr;
	if (iterationNbr >= 0){
	var parametersToSend = arrayOfBusLines[iterationNbr].id;
		for( var i=0; i < arrayOfBusLines[iterationNbr].fields.length; i++){
			parametersToSend += '-' + arrayOfBusLines[iterationNbr].fields[i];
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
