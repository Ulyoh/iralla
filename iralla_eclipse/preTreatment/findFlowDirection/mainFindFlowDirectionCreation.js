/**
 * @author Yoh
 */

function mainFindFlowDirection(){
	
	var tablePreTreatment = document.getElementById('tablePreTreatment');
	var node = document.getElementById("button_FindFlowDirection");
	var newField;

	if (node.state != "activate") {
		preTreatment.current = 'findFlowDirection';
		node.innerHTML = node.innerHTML + " : ACTIVATED";
		node.state = "activate";
        
		//hide the buttons not in use:
		for ( var i = 2; i < tablePreTreatment.childNodes.length; i++){
			tablePreTreatment.childNodes[i].style.display = "none";
		}

		//add a button to add arrows on the flow of vertex order between two boundaries
		if (document.getElementById('button_add_arrow') === null) {
			newField = newLineOfTablePreTreatment();
			var button_add_arrow = document.createElement('button');
			button_add_arrow.setAttribute('id', 'button_add_arrow');
			button_add_arrow.innerHTML = 'add arrow';
			newField.appendChild(button_add_arrow);
		}
		document.getElementById('button_add_arrow').setAttribute('onclick', 'SubMap._busLinesArray.enableAddArrow()');
		
		//add a button to remove arrows between two boundaries
		if (document.getElementById('button_remove_arrow') === null) {
			newField = newLineOfTablePreTreatment();
			var button_remove_arrow = document.createElement('button');
			button_remove_arrow.setAttribute('id', 'button_remove_arrow');
			button_remove_arrow.innerHTML = 'remove arrow';
			newField.appendChild(button_remove_arrow);
		}
		document.getElementById('button_remove_arrow').setAttribute('onclick', 'SubMap._busLinesArray.enableRemoveArrows(MouseEvent.latLng)');
	
		//add a button to add a boundary
		if (document.getElementById('button_add_boundary') === null) {
			newField = newLineOfTablePreTreatment();
			var button_add_boundary = document.createElement('button');
			button_add_boundary.setAttribute('id', 'button_add_boundary');
			button_add_boundary.innerHTML = 'add boundary';
			newField.appendChild(button_add_boundary);
		}
		document.getElementById('button_add_boundary').setAttribute('onclick', 'SubMap._busLinesArray.enableAddBoundary()');	
	
		//add a button to automaticaly find the flow and put the arrows 
		if (document.getElementById('button_find_flow_auto') === null) {
			newField = newLineOfTablePreTreatment();
			var button_find_flow_auto = document.createElement('button');
			button_find_flow_auto.setAttribute('id', 'button_find_flow_auto');
			button_find_flow_auto.innerHTML = 'find flow automaticaly';
			newField.appendChild(button_find_flow_auto);
		}
		document.getElementById('button_find_flow_auto').setAttribute('onclick', 'SubMap._busLinesArray.enableFindFlowAuto()');
		
		//add a button to find the flow from the arrows of the xml file
		
		//add a button to show the arrows from xml file on the map 
		if (document.getElementById('button_show_arrows_from_file') === null) {
			newField = newLineOfTablePreTreatment();
			var button_show_arrows_from_file = document.createElement('button');
			button_show_arrows_from_file.setAttribute('id', 'button_show_arrows_from_file');
			button_show_arrows_from_file.innerHTML = 'show arrow from file';
			newField.appendChild(button_show_arrows_from_file);
		}
		document.getElementById('button_show_arrows_from_file').setAttribute('onclick', 'showArrowsOnMap()');

		//add a button to hide the arrows from xml file on the map 
		if (document.getElementById('button_hide_arrows_from_file') === null) {
			newField = newLineOfTablePreTreatment();
			var button_hide_arrows_from_file = document.createElement('button');
			button_hide_arrows_from_file.setAttribute('id', 'button_hide_arrows_from_file');
			button_hide_arrows_from_file.innerHTML = 'hide arrow from file';
			newField.appendChild(button_hide_arrows_from_file);
		}
		document.getElementById('button_hide_arrows_from_file').setAttribute('onclick', 'hideArrowsOnMap()');
		
		//add a button to determinate the flow from the arrow of the xml file 
		if (document.getElementById('button_find_flow_from_xml_arrows') === null) {
			newField = newLineOfTablePreTreatment();
			var button_find_flow_from_xml_arrows = document.createElement('button');
			button_find_flow_from_xml_arrows.setAttribute('id', 'button_find_flow_from_xml_arrows');
			button_find_flow_from_xml_arrows.innerHTML = 'find flow from xml arrows';
			newField.appendChild(button_find_flow_from_xml_arrows);
		}
		document.getElementById('button_find_flow_from_xml_arrows').setAttribute('onclick', 'SubMap._busLinesArray.enableFindFlowFromXmlArrows()');
		
		//add a button to reverse the flow
		if (document.getElementById('button_reverse_flow') === null) {
			newField = newLineOfTablePreTreatment();
			var button_reverse_flow = document.createElement('button');
			button_reverse_flow.setAttribute('id', 'button_reverse_flow');
			button_reverse_flow.innerHTML = 'reverse the flow';
			newField.appendChild(button_reverse_flow);
		}
		document.getElementById('button_reverse_flow').setAttribute('onclick', 'SubMap._busLinesArray.enableReverseFlow()');
		
		//add a button to make a bidirectional flow
		if (document.getElementById('button_bidirectional_flow') === null) {
			newField = newLineOfTablePreTreatment();
			var button_bidirectional_flow = document.createElement('button');
			button_bidirectional_flow.setAttribute('id', 'button_bidirectional_flow');
			button_bidirectional_flow.innerHTML = 'bidirectional the flow';
			newField.appendChild(button_bidirectional_flow);
		}
		document.getElementById('button_bidirectional_flow').setAttribute('onclick', 'SubMap._busLinesArray.enableAddBidirectionalArrows()');
		
		//add a button to make a bidirectional flow
		if (document.getElementById('button_find_flow_of_all_bus_lines') === null) {
			newField = newLineOfTablePreTreatment();
			var button_find_flow_of_all_bus_lines = document.createElement('button');
			button_find_flow_of_all_bus_lines.setAttribute('id', 'button_find_flow_of_all_bus_lines');
			button_find_flow_of_all_bus_lines.innerHTML = 'find flow for all bus lines';
			newField.appendChild(button_find_flow_of_all_bus_lines);
		}
		document.getElementById('button_find_flow_of_all_bus_lines').setAttribute('onclick', 'SubMap._busLinesArray.findFlowOfAllBusLines(0)');

		//add a button to show the flows
		if (document.getElementById('button_show_flows') === null) {
			newField = newLineOfTablePreTreatment();
			var button_show_flows = document.createElement('button');
			button_show_flows.setAttribute('id', 'button_show_flows');
			button_show_flows.innerHTML = 'show flows';
			newField.appendChild(button_show_flows);
		}
		document.getElementById('button_show_flows').setAttribute('onclick', 'SubMap._busLinesArray.showFlows()');
		
		//add a button to hide the flows
		if (document.getElementById('button_hide_flows') === null) {
			newField = newLineOfTablePreTreatment();
			var button_hide_flows = document.createElement('button');
			button_hide_flows.setAttribute('id', 'button_hide_flows');
			button_hide_flows.innerHTML = 'hide flows';
			newField.appendChild(button_hide_flows);
		}
		document.getElementById('button_hide_flows').setAttribute('onclick', 'SubMap._busLinesArray.hideFlows()');
		
		//add a button to save the flows in the database
		if (document.getElementById('button_save_flows_in_database') === null) {
			newField = newLineOfTablePreTreatment();
			var button_save_flows_in_database = document.createElement('button');
			button_save_flows_in_database.setAttribute('id', 'button_save_flows_in_database');
			button_save_flows_in_database.innerHTML = 'save flows in database';
			newField.appendChild(button_save_flows_in_database);
		}
		document.getElementById('button_save_flows_in_database').setAttribute('onclick', 'SubMap._busLinesArray.hideFlows()');
		
	}
	else {
		node.innerHTML = 'determinate flow direction';
		
		//remove the button to add an arrow:
		removeNodeById('button_add_arrow');
		
		//remove the button 'remove arrow'
		removeNodeById('button_remove_arrow');
		
		//remove the button 'add a boundary'
		removeNodeById('button_add_boundary');
		
		//remove the button 'find flow automaticaly'
		removeNodeById('button_find_flow_auto');
		
		//remove the button 'show arrow from file'
		removeNodeById('button_show_arrows_from_file');
		
		//remove the button 'hide arrow from file'
		removeNodeById('button_hide_arrows_from_file');

		//remove the button 'find flow from xml file'
		removeNodeById('button_find_flow_from_xml_arrows');

		//remove the button 'reverse the flow'
		removeNodeById('button_reverse_flow');
		
		//remove the button 'bidirectional the flow'
		removeNodeById('button_bidirectional_flow');
				
		//remove the button 'find flow of all bus lines'
		removeNodeById('button_find_flow_of_all_bus_lines');
		
		//remove the button 'show flows'
		removeNodeById('button_show_flows');
		
		//remove the button 'hide flows'
		removeNodeById('button_hide_flows');
		
		//remove the button 'save the flows in the database'
		removeNodeById('button_save_flows_in_database');

		//deselect the previous function:
		//virtual press the deselect button:
		if (document.getElementById('button_deselect') !== null){
			document.getElementById('button_deselect').click();
		}
				
		//remove the button 'deselect'
		removeNodeById('button_deselect');
		
		node.state = "desactivate";
		
		//remove the fields of the table that are empty:
		var trs = tablePreTreatment.getElementsByTagName('tr');
		
		for ( i = 0; i < trs.length; i++){
			if (trs[i].childNodes[0].childNodes.length === 0){
				trs[i].parentNode.removeChild(trs[i]);
				i--;
			}
		}
		
		//add the buttons hidden:
		for ( i = 0; i < tablePreTreatment.childNodes.length; i++){
			tablePreTreatment.childNodes[i].style.display = "table-row";
		}
		preTreatment.current = null;
	}
}

loaded.findFlowDirection.push('mainFindFlowDirectionCreation.js');
