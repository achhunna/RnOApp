<?php
	//LDAP authentication file

	function authenticate(){
		header('WWW-Authenticate: Basic realm="LDAP Auth Login"');
		header('HTTP/1.0 401 Unauthorized');
		echo '<script>window.history.back()</script>';
		exit;
	}

	if(!isset($_SERVER['PHP_AUTH_USER'])) {
		authenticate();
	}else{
		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];
		$emailDomain = ""; //Provide @ email domain
		$email = $username.$emailDomain;
		
		$domain = ""; //Provide LDAP domain xxx.ad.xxx.com
		$adServer = "ldap://".$domain;
		
		$ldap = ldap_connect($adServer) or die("Cannot connect to LDAP server.");

		$ldaprdn = $domain. "\\" . $username;

		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		$bind = @ldap_bind($ldap, $email, $password);

		//
		if ($bind) {
			$filter="(userprincipalname=$email)";
			$result = ldap_search($ldap,"dc=".strtoupper($domain).",dc=AD,dc=xxx,dc=COM",$filter)or exit("Unable to search LDAP server");
			ldap_sort($ldap,$result,"sn");
			$info = ldap_get_entries($ldap, $result);
			ldap_unbind($ldap);

			for ($i=0; $i<$info["count"]; $i++)
			{	
				if($info["count"] > 1)
					break;
				$userFirstname = $info[$i]["givenname"][0];
				$userLastname = $info[$i]["sn"][0];
				$userDn = strtolower($info[$i]["samaccountname"][0]);
			}
			$_SESSION["userFirstname"] = $userFirstname;
			$_SESSION["userLastname"] = $userLastname;
			$_SESSION["userDn"] = $userDn;
			@ldap_close($ldap);
		} else {
			$msg = "Invalid username / password <br />";
			authenticate();
		}
	}
?>