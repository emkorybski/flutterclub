var setBal = document.getElementById("set_bal");
var increaseBal = document.getElementById("inc_bal");

setBal.onclick = function() {
    
   
    var bal_ded = document.getElementById("bal_ded").value;
   
     //alert(bal_ded);
     document.getElementById("money_amount").innerHTML = bal_ded;
     this.setAttribute('disabled','disabled');
     
     
    //return false;
}

increaseBal.onclick = function(){
    
    var getCurrentValue = document.getElementById("money_amount").innerHTML;
    alert(getCurrentValue);
    var curValueInt = parseInt(getCurrentValue);
    alert(curValueInt);
    var incrValue = document.getElementById("bal_inc").value;
    alert(incrValue);
    var newValue = curValueInt + parseInt(incrValue);
    document.getElementById("money_amount").innerHTML = newValue;
    
}



