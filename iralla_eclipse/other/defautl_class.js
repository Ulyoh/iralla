/**
 * @author Yoh
 */
function MyClass(args){
	//inherit class: 
		//empty ex: SuperClass.prototype.call(this, args);
	
//non-static properties:
	//public:
		//empty ex : this.publicVariable;
		
	//private:
		//empty ex: var staticPrivateVariable;

}

//public static properties:
	//empty ex: MyClass.publicStaticVariable;

MyClass.prototype = (function(){
//private static properties:
	//empty ex: var staticPrivateVariable;
	
//private static methods:
	//empty
	
	//constructor:
	function constructor(/*arg*/){
		//empty ex: staticPrivateVariable++;
	};
	//privileged static method: 
		//empty ex constructor.getSomething = function(){...};

	return constructor;
})();


//Methods public: ex : MyClass.prototype.theFonction = function(){...}
	//empty ex: MyClass.prototype.myMethodPublic = function(){...}


