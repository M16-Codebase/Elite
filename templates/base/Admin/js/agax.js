var req = Create();

function ge(id)
{
    return document.getElementById(id);
}

function Create()
{  
    if(navigator.appName == "Microsoft Internet Explorer")
    {  
        req = new ActiveXObject("Microsoft.XMLHTTP");  
    }
    else
    {  
        req = new XMLHttpRequest();  
    }  
return req;  
}  

function Request(query)
{
    req.open('post', '/top-100/wp-content/themes/twentyseventeen-top100/assets/php/sendPartner.php' , true );
    req.onreadystatechange = Refresh;
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
    req.send(query);  
}

function Refresh()
{
    var a = req.readyState;  
  
    if( a == 4 )
    {   
        var b = req.responseText;
        document.getElementById('ajax').innerHTML = b;
    }
    else
    {  
        document.getElementById('ajax').innerHTML = '<br><center>Отправка.........</center>';
		
    }
}
/////Собераем все элементы формы которые будем отправлять
function Pusk()
{  
    var query;
	var txt11 = encodeURIComponent(ge('authorp').value);
	var txt22 = encodeURIComponent(ge('emailp').value); 
    query = 'name='+txt11+'&email='+txt22; 
    Request(query);
}

 