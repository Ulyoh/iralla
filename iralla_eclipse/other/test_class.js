/**
 * @author Yoh
 */

function MyClass(name){
	//publics variables:
	this.publicVariable = name;
	//privates variables:
	var privateVariable = "coucou";
	//public methods whit access to privates variables:
	setPublicVariable = function(){
		this.publicVariable = privateVariable;
	};
}

//public methods whitout access to privates variables:
MyClass.prototype = {
	getPublicVariable: function() {
		return this.publicVariable;
	}
};

MyClass.prototype = (function(){
	//static private variables:
	var staticPrivateVariable = "spv";
	//console.log(staticPrivateVariable);
	return(
	{
		//public methods whith access to privates variables of HERE but not of non static privates variables:
		getPublicVariable2: function() {
			return this.publicVariable;
		},
		setPublicVariable2: function(){	
			this.setPublicVariable();
		},
		changeStaticPrivateVariable: function(){
			staticPrivateVariable = "changed";
		},
		getStaticPrivateVariable: function(){
			return staticPrivateVariable;
		}
	});
})();



var objet1 = new MyClass("premier");
var objet2 = new MyClass("2eme");
console.log(objet1.publicVariable);
objet1.setPublicVariable2();
console.log(objet1.publicVariable);
console.log(objet2.publicVariable);
/*
console.log(objet1.getStaticPrivateVariable());
console.log(objet2.getStaticPrivateVariable());
objet1.changeStaticPrivateVariable();
console.log(objet1.getStaticPrivateVariable());
console.log(objet2.getStaticPrivateVariable());

*/










