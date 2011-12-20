$(document).ready(function()
{
	i18n_dict_fr = { 
		"user_already_exist"  : "Ce nom d'utilisateur est déjà utilisé.",
	};
	
	i18n_dict_en = { 
		"user_already_exist"  : "This username is already taken.",
	};
		
	var lang = getCookie('lang');
	
	//On charge la langue en consÃ©quence
	if(lang == 'fr')
		{
		//$.i18n.setDictionary(i18n_dict_fr);
		$.i18n.setDictionary(i18n_dict_en);
		}
	else
		{
		$.i18n.setDictionary(i18n_dict_en);
		}
});

function getCookie(c_name)
{
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
{
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
}