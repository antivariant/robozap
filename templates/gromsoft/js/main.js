jQuery(document).ready(function($){
	LoginShow();
	LoginClose();


function LoginShow(){
	$('#btnLogin').bind('click', function(e){
		
		if($('.login').css('display')=='none'){
		$('.login').css('display','block');
		}
		else{
			$('.login').css('display','none');	
		}

	});
}

function LoginClose(){
	$('.close').bind('click', function(e){
		$('.login').css('display','none');
	});
}

});


//В Омах	
function setR(val_id, mul_id, val){
	var r = document.getElementById(val_id);
	var mul = document.getElementById(mul_id);
	var mul_index = 0;
	//Омы
	if(val<1e3){
		mul_index = 0;
		}
	//КОм	
	else if(val>=1e3 && val<1e6){
		mul_index = 1;
		val = val/1e3;			
		}
	//МОм
	else if(val>1e6){
		mul_index = 2;
		val	= val/1e6;		
		}

	r.value = val.toFixed(2);
	mul.options[mul_index].selected = true;
	}	


//В Герцах	
function setF(val_id, mul_id, val){
	var f = document.getElementById(val_id);
	var mul = document.getElementById(mul_id);
	var mul_index = 0;
	//Гц
	if(val<1e3){
		mul_index = 0;
		}
	//КГц	
	else if(val>=1e3 && val<1e6){
		mul_index = 1;
		val = val/1e3;	
		}
	//МГц
	else if(val>=1e6 && val<1e9){
		mul_index = 2;
		val	= val/1e6;		
		}
	//ГГц
	else if(val>=1e9){
		mul_index = 3;
		val	= val/1e9;		
		}


	f.value = val.toFixed(2);
	mul.options[mul_index].selected = true;
	}	



//В Фарадах
function setC(val_id, mul_id, val){
	var c = document.getElementById(val_id);
	var mul = document.getElementById(mul_id);
	var mul_index = 0;
	//мкФ
	if(val>1e-9){
		mul_index = 0;
		val = val * 1e6;
		}
	//нФ
	else if(val>1e-12 && val<=1e-9){
		mul_index = 1;
		val = val*1e9;			
		}
	//пФ	
	else if(val<=1e-12){
		mul_index = 2;
		val	= val*1e12;		
		}
	
	c.value = val.toFixed(2);
	mul.options[mul_index].selected = true;
	}	

//В секундах
function setT(val_id, mul_id, val){
	var t = document.getElementById(val_id);
	var mul = document.getElementById(mul_id);
	var mul_index = 0;

	//с	
	if(val>=1){
		mul_index = 0;
		val	= val;
		}
	//мс
	else if(val>=1e-3 && val<1){
		mul_index = 1;
		val = val * 1e3;
		}
	//мкс
	else if(val>=1e-6 && val<1e-3){
		mul_index = 2;
		val = val * 1e6;
		}
	//нс
	else if(val<1e-6){
		mul_index = 3;
		val = val * 1e9;			
		}

	t.value = val.toFixed(2);
	mul.options[mul_index].selected = true;
	}	


function getVal(val_id, mul_id){
	var val = document.getElementById(val_id).value;
	var mul = document.getElementById(mul_id).value;
	return val*Math.pow(10, mul);
}
