<?php
define("NO_OF_MODULES", 12);
define("NO_OF_ROLES_PER_MODULE", 8);
define("NO_OF_GLOBAL_ROLES", 48);	
?>
<html>
<head>
	<title>User Token Generation</title>
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
	<style>span {margin-left:3px;}</style>
</head>
<body class="container">

	<h1>User Token</h1>
	
	<ul class="nav nav-tabs">
	  <li class="nav-item">
	    <a class="nav-link active" aria-current="page" href="#generationTabContent" data-bs-toggle="tab" >Generation</a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" href="#documentationTabContent" data-bs-toggle="tab">Documentation</a>
	  </li>
	  <li class="nav-item">
	    <a class="nav-link" href="#labelsTabContent" data-bs-toggle="tab">Labels</a>
	  </li>	  
	</ul>

	<div class="tab-content" id="myTabContent">
	  <div class="tab-pane fade show active" id="generationTabContent" role="tabpanel" >
			<form onchange="generateToken()" onsubmit="generateToken(); return false;">
				<hr/>
				<table width="100%">
					<tr>
						<td style="font-size:24px; width:90px;">Token</td>
						<td id="outputToken" ></td>
						<td id="validityStatus"  style="width:90px"><span class="badge bg-danger">INVALID</span></td>
					</tr>
				</table>
				<hr/>
				<h4>User Id</h4>
				Sequential Unique Number:<input type="text" id="userId" value="0" /><br/>
				<h4>Module Accesses</h4>
				<div id="moduleAccessBoolean" class="row">
					<?php foreach(range(1,NO_OF_MODULES) as $no) : ?>
					<div class="col-sm-4"><input type="checkbox" value="-" /><span> Access to Module #<?=sprintf("%02d",$no)?>:</span><br/></div>
				<?php endforeach; ?>
				</div>
				<h4>Initials</h4>
				User 3 letter initials:<input type="text" id="userInitials" value="aaa" length="3" /><br/>
				<h4>Global User Accesses/Roles:</h4>
				<div id="globalUserRoles">
					<?php foreach(range(1,NO_OF_GLOBAL_ROLES) as $no) : ?>
					<div><input type="checkbox" value="-" /><span> Global Access/Role #<?=sprintf("%02d",$no)?></span><br/></div>
				<?php endforeach; ?>
				</div>
	
				<h4>Per Module User Accesses/Roles</h4>
				<div id="perModuleUserRole">
					<table class="table">
						<thead>
							<tr>
								<?php foreach(range(1,NO_OF_MODULES) as $no) : ?>
								<th>Module #<?=sprintf("%02d",$no)?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach(range(1,NO_OF_ROLES_PER_MODULE) as $no) : ?>
							<tr>
								<?php foreach(range(1,NO_OF_MODULES) as $no) : ?>
									<td><input type="checkbox" value="-" /><span>Role #<?=sprintf("%02d",$no)?></span></td>
								<?php endforeach; ?>
							</tr>
								<?php endforeach; ?>
						</tbody>
					</table>
				</div>
	
	
			</form>		
	  </div>
	  <div class="tab-pane fade" id="documentationTabContent" role="tabpanel" >
			<h1>Documentation</h1>
			<h5>The token has 32 (base64) characters. That is a total of 192 bits.</h5>
			<table class="table">
				<thead>
					<tr>
						<th>Nome</th>
						<th>Bits</th>
						<th>Base64 Digits</th>
						<th>Bits Left</th>
						<th>Base64 Digits Left</th>
					</tr>

				</thead>
				<tbody>
					<tr>
						<td>User Id (Max: 4096)</td>
						<td>12</td>
						<td>2</td>
						<td>180</td>
						<td>30</td>
					</tr>
					<tr>
						<td>Module Access Bools (Max: 12 individual possibilities)</td>
						<td>12</td>
						<td>2</td>
						<td>168</td>
						<td>28</td>
					</tr>
					<tr>
						<td>User initials (3 lowercase english letters)</td>
						<td>18</td>
						<td>3</td>
						<td>150</td>
						<td>25</td>
					</tr>
					<tr>
						<td>Per Module User Roles (Max: 72 individual possibilites)</td>
						<td>96</td>
						<td>16</td>
						<td>54</td>
						<td>9</td>
					</tr>		
					<tr>
						<td>Global User Roles (Max: 72 individual possibilites)</td>
						<td>48</td>
						<td>8</td>
						<td>6</td>
						<td>1</td>
					</tr>
					<tr>
						<td>Check Digit</td>
						<td>6</td>
						<td>1</td>
						<td>0</td>
						<td>0</td>
					</tr>
		
				</tbody>
			</table>
	</div><!-- ./tab-pane fade -->
	<div class="tab-pane fade" id="labelsTabContent" role="tabpanel" >
		<textarea rows="15" cols="125" id="userSubmittedLabels" onchange='console.log(this);eval("window.Labels = "+this.value);replaceLabels();'></textArea>
	</div><!-- /.tab-pane fade#labelsTabContent -->
</div><!-- ./tab-content -->



	
<script>
var base64 = "ABCDEEFGHIJKLMNOPQRSTUVWXYZabcdeefghijklmnopqrstuvwxyz0123456789-_"
	
function replaceLabels() {
	var moduleAccessGroup = document.getElementById("moduleAccessBoolean")
	var perModuleUserRole = document.getElementById("perModuleUserRole")
	if (typeof Labels == "undefined") return;
	/* 1. Module Access */
	if (typeof Labels.Modules != "undefined") {
		for (k in moduleAccessGroup.children)
			if (typeof Labels.Modules[k] != "undefined" && typeof moduleAccessGroup.children[k] == "object") 
				moduleAccessGroup.children[k].children[1].innerText = Labels.Modules[k]
				
		for(k in perModuleUserRole.children[0].children[0].children[0].children)
			if (typeof Labels.Modules[k] != "undefined" && typeof perModuleUserRole.children[0].children[0].children[0].children[k] == "object")
					perModuleUserRole.children[0].children[0].children[0].children[k].innerText = Labels.Modules[k]
	}
			
	/* 2. Global User Roles */
	if (typeof Labels.GlobalUserRoles != "undefined") {
		var globalUserRoles = document.getElementById("globalUserRoles")
		for (k in globalUserRoles.children)
			if (typeof Labels.GlobalUserRoles[k] != "undefined" && typeof globalUserRoles.children[k] == "object")
				globalUserRoles.children[k].children[1].innerText = Labels.GlobalUserRoles[k]
	}
	
	/* 3. Per Module User Roles */
	if (typeof Labels.PerModuleRole != "undefined") {
		for(k in perModuleUserRole.children[0].children[1].children) {			
			var row = perModuleUserRole.children[0].children[1].children[k]
			if (typeof row != "object") continue
			for (c in row.children) 
				if (typeof row.children[c] == "object" && typeof Labels.PerModuleRole[c] != "undefined" && typeof Labels.PerModuleRole[c][k] != "undefined")
					row.children[c].children[1].innerText = Labels.PerModuleRole[c][k]
			
		}
	}
			

}
function generateToken() { 
	var ot = document.getElementById("outputToken");
	var token = encodeUserId() + encodeModuleAccess()+encodeUserInitials()+encodeGlobalUserRoles()

	ot.innerText = token;
}
function encodeNativeNumberAsBase64(n) {
	var output = "-";
	while(n > 0) {
		var q = Math.floor(n / 64)
		var r = n % 64;
		output += base64[r];
		n = q;
	}
	return output;
}
function encodeUserId() {
	var userId = document.getElementById("userId")
	userId = parseInt(userId.value)
	var a = userId % 64;
	var b = (Math.floor(userId / 64)) % 64;
	return base64[a] + base64[b];
}
function encodeModuleAccess() {
	var moduleAccessGroup = document.getElementById("moduleAccessBoolean")
	var output = 0
	for (k in moduleAccessGroup.children) 
		if (typeof moduleAccessGroup.children[k] == "object" && moduleAccessGroup.children[k].children[0].checked)
			output |= (1 << k)
	
	return base64[output % 64] + base64[(Math.floor(output / 64)) % 64];
}
function encodeUserInitials() {
	var userInitials = document.getElementById("userInitials").value
	var output = 0
	for(k in userInitials) 
		output += (userInitials.charCodeAt(k) - "a".charCodeAt(0)) * Math.pow(26,k)
	return base64[output % 64] + base64[(Math.floor(output / (64*1))) % 64] + base64[(Math.floor(output / (64*2))) % 64];
}
function encodeGlobalUserRoles() {
	var globalUserRoles = document.getElementById("globalUserRoles")
	var output = 0
	for (k in globalUserRoles.children) 
		if (typeof globalUserRoles.children[k] == "object" && globalUserRoles.children[k].children[0].checked)
			output |= (1 << k)
	
	return encodeNativeNumberAsBase64(output);	
}
replaceLabels()
</script>

</body>
</html>
