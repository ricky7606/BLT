<?php
header ( " Expires: Mon, 26 Jul 1970 05:00:00 GMT " );
header ( " Last-Modified:" . gmdate ( " D, d M Y H:i:s " ). "GMT " );
header ( " Cache-Control: no-cache, must-revalidate " );
header ( " Pragma: no-cache " );
header("Content-Type:text/html;charset=utf-8");

define("USERNAME", "invoicing@omnidataservices.com");
define("PASSWORD", "g0awayn0w");
define("SECURITY_TOKEN", "2eSgCn54B4DsRwRcy0Is8p6P");
 
require_once ('soapclient/SforcePartnerClient.php');
 
$mySforceConnection = new SforcePartnerClient();
$mySforceConnection->createConnection("PartnerWSDL.xml");
$mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);

$XID = $_GET['XID'];
if($XID!=''){
	$query = "select LastName, FirstName, Salutation, Title, Account.Name, MailingStreet, MailingCity, MailingState, MailingPostalCode, Phone, Email, Contact_Number__c from Contact where XID__c = '".$XID."'";
	$response = $mySforceConnection->query($query);
}
foreach ($response->records as $record){
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>MechBiz</title>
<link href="MechBiz.css" rel="stylesheet" type="text/css">
<style>
body{margin:0; padding:0;}
.outer{width:100%; text-align:center; padding-top:15px;}
.content{width:640px; margin:0 auto; text-align:left;}
.inputtext{width:350px;}
.inputLabel{padding-top:3px;}
@media screen and (max-width: 680px) {
	.mobile_hide {
		min-height: 0px;
		max-height: 0px;
		max-width: 0px;
		display: none;
		overflow: hidden;
		font-size: 0px;
	}
	.desktop_hide {
		display: block !important;
		max-height: none !important;
	}
	body,p,div,font,span,input,select {font-size:16px !important; line-height:20px !important;}
	.content{ width:100% !important;}
	.inputtext{width:310px !important;}
	.inputLabel{width:160px !important;}
	#dp_ValidEmployees{width:200px !important;}
	#dp_BusinessType_Other{width:310px !important;}
	#dp_BusinessType{width:310px !important;}
	.productDiv{width:330px !important;}
	#jobsite{height:255px !important;}
	#vc{width:80px !important;}
	#imgObj{height:30px !important; width:80px !important;}
	#bnOK{ font-size:20px !important; font-weight:bold !important;}
}
</style>
<script type="text/javascript">
var u = navigator.userAgent;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; 
var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); 
if(/Android (\d+\.\d+)/.test(navigator.userAgent)){
	var version = parseFloat(RegExp.$1);
	if(version>2.3){
		var phoneScale = parseInt(window.screen.width)/640;
		document.write('<meta name="viewport" content="width=640, minimum-scale = '+ phoneScale +', maximum-scale = '+ phoneScale +', target-densitydpi=device-dpi">');
	}else{
		document.write('<meta name="viewport" content="width=640, target-densitydpi=device-dpi">');
	}
}else{
		document.write('<meta name="viewport" content="width=640, user-scalable=no, target-densitydpi=device-dpi">');
}
</script>
<script type="text/javascript" src="jquery-1.11.2.min.js"></script>
<script type="text/javascript">
function showContact(){
	<?php
	if($response!=''){
	?>
	$('#txtLastName').val('<?php echo $record->fields->LastName ?>');
	$('#txtFirstName').val('<?php echo $record->fields->FirstName ?>');
	$('#drop_Salut').val('<?php echo $record->fields->Salutation ?>');
	$('#txtJobTitle').val('<?php echo $record->fields->Title ?>');
	$('#txtCompanyName').val('');
	$('#txtAddress').val('<?php echo $record->fields->MailingStreet ?>');
	$('#txtCity').val('<?php echo $record->fields->MailingCity ?>');
	$('#txtProvince').val('<?php echo $record->fields->MailingState ?>');
	$('#txtPostalCode').val('<?php echo $record->fields->MailingPostalCode ?>');
	$('#txtTelephone').val('<?php echo $record->fields->Phone ?>');
	$('#txtEmailAddress').val('<?php echo $record->fields->Email ?>');
	$('#ContactNumber').val('<?php echo $record->fields->Contact_Number__c ?>');
	$('#bnOK').show();
	<?php
	}
	?>
	
	checkOption(jQuery('#dp_BusinessType'));
}
function checkOption(se)
{
	if(se.value == "Other Industry")
	{
		jQuery('#other_tr1').show();
		jQuery('#other_tr2').show();
	}
	else
	{
		jQuery('#other_tr1').hide();
		jQuery('#other_tr2').hide();
	}
}
function doSubmit()
{
	if(jQuery.trim(jQuery('#txtLastName').val()) == "")
	{
		alert("Please enter last name.");
		jQuery('#txtLastName').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtFirstName').val()) == "")
	{
		alert("Please enter first name.");
		jQuery('#txtFirstName').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtJobTitle').val()) == "")
	{
		alert("Please enter job title.");
		jQuery('#txtJobTitle').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtCompanyName').val()) == "")
	{
		alert("Please enter company name.");
		jQuery('#txtCompanyName').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtAddress').val()) == "")
	{
		alert("Please enter address.");
		jQuery('#txtAddress').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtCity').val()) == "")
	{
		alert("Please enter city.");
		jQuery('#txtCity').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtProvince').val()) == "")
	{
		alert("Please enter province.");
		jQuery('#txtProvince').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtPostalCode').val()) == "")
	{
		alert("Please enter postal code.");
		jQuery('#txtPostalCode').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtTelephone').val()) == "")
	{
		alert("Please enter telephone.");
		jQuery('#txtTelephone').focus();
		return;
	}
	if(jQuery.trim(jQuery('#txtEmailAddress').val()) == "")
	{
		alert("Please enter email address.");
		jQuery('#txtEmailAddress').focus();
		return;
	}
	if(jQuery('#dp_ValidEmployees').val() == "")
	{
		alert("Please select what is the approximate number of employees at this location.");
		jQuery('#dp_ValidEmployees').focus();
		return;
	}
	if(jQuery('#dp_BusinessType').val() == "")
	{
		alert("Please select what best describes your type of business.");
		jQuery('#dp_BusinessType').focus();
		return;
	}
	else if(jQuery('#dp_BusinessType').val() == "Other Industry" && jQuery.trim(jQuery('#dp_BusinessType_Other').val()) == "")
	{
		alert("Please enter what best describes your type of business.");
		jQuery('#dp_BusinessType_Other').focus();
		return;
	}
	
	var a = "";
	var b = "";
	var c = "";
	jQuery(":checkbox").each(function()
	{
		if(this.checked == true)
		{
			if(this.id.indexOf("A") > -1)
			{
				a = "1";
			}
			if(this.id.indexOf("B") > -1)
			{
				b = "1";
			}
			if(this.id.indexOf("C") > -1)
			{
				c = "1";
			}
		}
	});
	
	if(a == "")
	{
		alert("Please select which of the following products and services do you recommend, specify, select or approve the purchase of.");
		return;
	}
	if(b == "")
	{
		alert("Please select what Job Sector Markets are you active in.");
		return;
	}
	if(c == "")
	{
		alert("Please select Company Jobsite Activities.");
		return;
	}
	
	if(jQuery.trim(jQuery('#vc').val()) == "")
	{
		alert("Please enter verification code.");
		jQuery('#vc').focus();
		return;
	}
	
	jQuery('#bnOK').attr("disabled",true);
	jQuery('#bnOK').val("Processing");
	
	var CN = jQuery('#txtCompanyName').val().replace("&", "%26");
	
	jQuery.ajax(
	{
		type:"POST",
		url:"<%=request.getContextPath()%>/servlet/MechBizServlet?action=saveContact",
		data:"XID=<%=request.getParameter("XID")%>" + "&ContactNumber=" + jQuery('#ContactNumber').val() + "&FN=" + jQuery('#txtFirstName').val() + "&LN=" + jQuery('#txtLastName').val() + "&Salutation=" + jQuery('#drop_Salut').val() + "&JT=" + jQuery('#txtJobTitle').val() + "&CN=" + CN + "&address=" + jQuery('#txtAddress').val() + "&city=" + jQuery('#txtCity').val() + "&province=" + jQuery('#txtProvince').val() + "&postal=" + jQuery('#txtPostalCode').val() + "&phone=" + jQuery('#txtTelephone').val() + "&email=" + jQuery('#txtEmailAddress').val() + "&A1=" + jQuery('#A1').is(':checked') + "&A2=" + jQuery('#A2').is(':checked') + "&A3=" + jQuery('#A3').is(':checked') + "&A4=" + jQuery('#A4').is(':checked') + "&A5=" + jQuery('#A5').is(':checked') + "&A6=" + jQuery('#A6').is(':checked') + "&A7=" + jQuery('#A7').is(':checked') + "&A8=" + jQuery('#A8').is(':checked') + "&A9=" + jQuery('#A9').is(':checked') + "&A10=" + jQuery('#A10').is(':checked') + "&A11=" + jQuery('#A11').is(':checked') + "&A12=" + jQuery('#A12').is(':checked') + "&A13=" + jQuery('#A13').is(':checked') + "&A14=" + jQuery('#A14').is(':checked') + "&A15=" + jQuery('#A15').is(':checked') + "&B1=" + jQuery('#B1').is(':checked') + "&B2=" + jQuery('#B2').is(':checked') + "&B3=" + jQuery('#B3').is(':checked') + "&B4=" + jQuery('#B4').is(':checked') + "&B5=" + jQuery('#B5').is(':checked') + "&B6=" + jQuery('#B6').is(':checked') + "&C1=" + jQuery('#C1').is(':checked') + "&C2=" + jQuery('#C2').is(':checked') + "&C3=" + jQuery('#C3').is(':checked') + "&C4=" + jQuery('#C4').is(':checked') + "&C5=" + jQuery('#C5').is(':checked') + "&C6=" + jQuery('#C6').is(':checked') + "&C7=" + jQuery('#C7').is(':checked') + "&C8=" + jQuery('#C8').is(':checked') + "&C9=" + jQuery('#C9').is(':checked') + "&C10=" + jQuery('#C10').is(':checked') + "&C11=" + jQuery('#C11').is(':checked') + "&C12=" + jQuery('#C12').is(':checked') + "&C13=" + jQuery('#C13').is(':checked') + "&C14=" + jQuery('#C14').is(':checked') + "&C15=" + jQuery('#C15').is(':checked') + "&receive=" + jQuery('input:radio[name="online"]:checked').val() + "&employee=" + jQuery('#dp_ValidEmployees').val() + "&business=" + jQuery('#dp_BusinessType').val() + "&other=" + jQuery('#dp_BusinessType_Other').val() + "&vc=" + jQuery('#vc').val() + "&here=" + jQuery('input:radio[name="online2"]:checked').val(),
		dataType: "text",
		success: function(s)
		{
			if(s == "Submit Successfully.")
			{
				if(top.location == location)
				{
					location.href = "Finished.jsp";
				}
				else
				{
					top.location.href = "http://www.mechanicalbusiness.com/thank-you-for-subscribing/";
				}
			}
			else
			{
				alert(s);
				jQuery('#bnOK').attr("disabled",false);
				jQuery('#bnOK').val("Submit Request");
			}
		}
	});
}
function changeImg()
{
	var imgSrc = jQuery("#imgObj");
	var src = imgSrc.attr("src");
	imgSrc.attr("src",chgUrl(src));
}
function chgUrl(url)
{
	var timestamp = (new Date()).valueOf();
	//url = url.substring(0,17);
	if((url.indexOf("&") >= 0))
	{
		url = url + "&tamp=" + timestamp;
	}
	else
	{
		url = url + "?timestamp=" + timestamp;
	}
	return url;
}
</script>
</head>

<body>
<div class="outer">
	<div class="content">
    	<div class="SRFormTxtNB" style="height:auto;">Mechanical Business is offered free to qualified subscribers. Certain criteria have to be met in order to qualify. Free subscriptions are only available to Canadian residents. Fields marked with * must be completed.</div>
        <div style="clear:both; height:5px;"></div>
        <div class="SRFormLB">
        	<div style="float:left; width:80%;"><font style="font-size:14px;font-weight:bold">I want to receive / continue to receive Mechanical Business, absolutely free of charge!</font><font color="#ff0000">&nbsp;**&nbsp;</font> &nbsp;&nbsp;</div>
            <div style="float:left; width:20%; text-align:left; padding-top:10px;">
            <input id="receive_yes" type="radio" name="online" value="true" />Yes &nbsp;
            <input id="receive_no" type="radio" name="online" value="false" />No
            </div>
            <div style="clear:both; height:5px;"></div>
        </div>
        <div class="SRFormLB"><font style="font-size:14px;font-weight:bold">We generally send out our printed magazine free of charge to qualified recipients in Canada. If you would prefer our digital edition, please indicate your preference here:</font><font color="#ff0000">&nbsp;**&nbsp;</font> &nbsp;&nbsp;</div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;flex-wrap:wrap;flex-direction:row;"><div><input id="here2" type="radio" name="online2" value="Print and Digital" checked="checked" />Print and Digital&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div><input id="here1" type="radio" name="online2" value="Print" />Print Only&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div><input id="here3" type="radio" name="online2" value="Digital" />Digital Only&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></div>
        <div class="SRFormLB" style="padding-top:5px; padding-bottom:10px;">(USA and International should choose "Digital Only")</div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Last Name<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtLastName" type="text" maxlength="50" id="txtLastName" class="inputtext" style="width: 350px;" /></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">First Name<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtFirstName" type="text" maxlength="50" id="txtFirstName" class="inputtext" style="width: 350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Salutation/Prefix</div>
        	<div style="float:left;">
              <select name="drop_Salut" id="drop_Salut" class="fonts" style="width:80px">
                  <option selected="selected" value=""></option>
                  <option value="Mr.">Mr.</option>
                  <option value="Mrs.">Mrs.</option>
                  <option value="Miss.">Miss.</option>
                  <option value="Dr.">Dr.</option>
                  <option value="Ms.">Ms.</option>
                  <option value="Prof.">Prof.</option>
                  <option value="Capt.">Capt.</option>
                  <option value="Brig.">Brig.</option>
              </select>
            </div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Job Title<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtJobTitle" type="text" maxlength="255" id="txtJobTitle" class="inputtext" style="width: 350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Company Name<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtCompanyName" type="text" maxlength="255" id="txtCompanyName" class="inputtext" style="width:350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Address<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtAddress" type="text" maxlength="255" id="txtAddress" class="inputtext" style="width:350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">City<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtCity" type="text" maxlength="100" id="txtCity" class="inputtext" style="width: 350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Province<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtProvince" type="text" maxlength="100" id="txtProvince" class="inputtext" style="width: 350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Postal Code<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtPostalCode" type="text" maxlength="50" id="txtPostalCode" class="inputtext" style="width: 350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Telephone<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtTelephone" type="text" maxlength="50" id="txtTelephone" class="inputtext" style="width:350px"></div>
            <div style="clear:both; height:2px;"></div>
        </div>
        <div class="SRFormLB">
        	<div style="float:left; width:121px;" class="inputLabel">Email Address<font color="#ff0000">&nbsp;**&nbsp;</font></div>
        	<div style="float:left;"><input name="txtEmailAddress" type="text" maxlength="255" id="txtEmailAddress" class="inputtext" style="width: 350px"></div>
            <div style="clear:both; height:20px;"></div>
        </div>
        <div class="SRFormLB"><font style="font-size:14px;font-weight:bold">What is the approximate number of employees at this location?</font><font color="#ff0000">&nbsp;**&nbsp;</font></div>
        <div class="SRFormLB">
          <select name="dp_ValidEmployees" id="dp_ValidEmployees" class="fonts" style="width:100px">
              <option selected="selected" value="">Choose One</option>
              <option value="A">1 - 4</option>
              <option value="B">5 - 9</option>
              <option value="C">10 - 19</option>
              <option value="D">20 - 49</option>
              <option value="E">50 - 99</option>
              <option value="F">100 +</option>
          </select> 
        </div>
        <div style="clear:both; height:20px;"></div>
        <div class="SRFormLB"><font style="font-size:14px;font-weight:bold">What best describes your type of business?</font><font color="#ff0000">&nbsp;**&nbsp;</font></div>
        <div class="SRFormLB">
          <select name="dp_BusinessType" id="dp_BusinessType" class="fonts" style="width:400px" onChange="checkOption(this);">
              <option selected="selected" value="">Choose One</option>
              <option value="Mechanical Contractor: Plumbing, Hydronic, HVAC/R">Mechanical Contractor:Plumbing, Hydronic, HVAC/R</option>
              <option value="Wholesaler">Wholesaler</option>
              <option value="Distributor/Agent/Rep">Distributor/Agent/Rep</option>
              <option value="Mechanical Consulting Engineer/Mechanical Specifier">Mechanical Consulting Engineer/Mechanical Specifier</option>
              <option value="Building Contractor/Developer/Facilities Mgt/Maintenance">Building Contractor/Developer/Facilities Mgt/Maintenance</option>
              <option value="Other Industry">Other Industry</option>
          </select> 
        </div>
        <div class="SRFormLB" id="other_tr1"><font style="font-size:14px;font-weight:bold">What best describes your type of business?</font><font color="#ff0000">&nbsp;**&nbsp;</font></div>
        <div class="SRFormLB" id="other_tr2"><input name="dp_BusinessType_Other" type="text" maxlength="255" id="dp_BusinessType_Other" class="inputtext" style="width: 400px"></div>
        <div style="clear:both; height:20px;"></div>
        <div class="SRFormLB"><font style="font-size:14px;font-weight:bold">Which of the following products and services do you recommend, specify, select or approve the purchase of? (Please check ALL that apply.)</font><font color="#ff0000">&nbsp;**&nbsp;</font></div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:row;">
        	<div style="width:160px;" class="productDiv"><input id="A1" type="checkbox" name="A1"><label for="A1">Plumbing/Piping(A1)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A10" type="checkbox" name="A10"><label for="A10">Service Vehicles &amp; Accessories(A10)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A8" type="checkbox" name="A8"><label for="A8">Fire Protection(A8)</label></div>
            <div style="width:160px;" class="productDiv"><input id="A5" type="checkbox" name="A5"><label for="A5">Refrigeration(A5)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A3" type="checkbox" name="A3"><label for="A3">Warm Air Heating(A3)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A13" type="checkbox" name="A13"><label for="A13">Air Conditioning(A13)</label></div>
            <div style="width:160px;" class="productDiv"><input id="A9" type="checkbox" name="A9"><label for="A9">Infrared Heating(A9)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A7" type="checkbox" name="A7"><label for="A7">Drain Cleaning(A7)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A14" type="checkbox" name="A14"><label for="A14">Sheet Metal(A14)</label></div>
            <div style="width:160px;" class="productDiv"><input id="A2" type="checkbox" name="A2"><label for="A2">Hydronic Heating(A2)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A11" type="checkbox" name="A11"><label for="A11">Controls/Instrumentation(A11)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A15" type="checkbox" name="A15"><label for="A15">Fireplaces(A15)</label></div>
            <div style="width:160px;" class="productDiv"><input id="A6" type="checkbox" name="A6"><label for="A6">IAQ/Ventilation(A6)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A4" type="checkbox" name="A4"><label for="A4">Geothermal/Solar(A4)</label></div>
            <div style="width:240px;" class="productDiv"><input id="A12" type="checkbox" name="A12"><label for="A12">No Purchase Authority(A12)</label></div>
        </div>
        <div style="clear:both; height:20px;"></div>
        <div class="SRFormLB"><font style="font-size:14px;font-weight:bold">What Job Sector Markets are you active in? (Check ALL that apply)</font><font color="#ff0000">&nbsp;**&nbsp;</font></div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:row;">
        	<div style="width:190px;" class="productDiv"><input id="B1" type="checkbox" name="B1"><label for="B1">Commercial(B1)</label></div>
            <div style="width:190px;" class="productDiv"><input id="B5" type="checkbox" name="B5"><label for="B5">Service(B5)</label></div>
            <div style="width:190px;" class="productDiv"><input id="B4" type="checkbox" name="B4"><label for="B4">Industrial(B4)</label></div>
            <div style="width:190px;" class="productDiv"><input id="B3" type="checkbox" name="B3"><label for="B3">Institutional(B3)</label></div>
            <div style="width:190px;" class="productDiv"><input id="B2" type="checkbox" name="B2"><label for="B2">Residential(B2)</label></div>
            <div style="width:190px;" class="productDiv"><input id="B6" type="checkbox" name="B6"><label for="B6">New Construction(B6)</label></div>
        </div>
        <div style="clear:both; height:20px;"></div>
        <div class="SRFormLB"><font style="font-size:14px;font-weight:bold">Company Jobsite Activities (Check ALL that apply)</font><font color="#ff0000">&nbsp;**&nbsp;</font></div>
        <div class="SRFormLB" id="jobsite" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:column;height:75px; padding-bottom:0px !important;">
        	<div style="width:190px;" class="productDiv"><strong>Heating</strong></div>
            <div style="width:190px;" class="productDiv"><input id="C1" type="checkbox" name="C1"><label for="C1">Warm Air(C1)</label></div>
            <div style="width:190px;" class="productDiv"><input id="C2" type="checkbox" name="C2"><label for="C2">Hydronic(C2)</label></div>
            <div style="width:190px;" class="productDiv"><input id="C3" type="checkbox" name="C3"><label for="C3">Infra-Red(C3)</label></div>
            <div style="width:190px;" class="productDiv"><strong>Fuel Types</strong></div>
            <div style="width:190px;" class="productDiv"><input id="C11" type="checkbox" name="C11"><label for="C11">Natural Gas(C11)</label></div>
            <div style="width:190px;" class="productDiv"><input id="C14" type="checkbox" name="C14"><label for="C14">Fuel Oil(C14)</label></div>
            <div style="width:190px;" class="productDiv"><input id="C12" type="checkbox" name="C12"><label for="C12">Propane(C12)</label></div>
            <div style="width:190px;" class="productDiv">&nbsp;</div>
            <div style="width:190px;" class="productDiv"><input id="C15" type="checkbox" name="C15"><label for="C15">Electric(C15)</label></div>
            <div style="width:190px;" class="productDiv"><input id="C13" type="checkbox" name="C13"><label for="C13">Alternative/Green(C13)</label></div>
        </div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:row; padding-top:0px !important;">
        	<div style="width:390px;" class="productDiv"><strong>Plumbing</strong></div>
            <div style="width:390px;" class="productDiv"><input id="C4" type="checkbox" name="C4"><label for="C4">Sanitary/Fixtures/DHW(C4)</label></div>
            <div style="width:390px;" class="productDiv"><input id="C5" type="checkbox" name="C5"><label for="C5">Piping/Controls(C5)</label></div>
            <div style="width:390px;" class="productDiv"><input id="C6" type="checkbox" name="C6"><label for="C6">Drain Services(C6)</label></div>
            <div style="width:390px;" class="productDiv"><input id="C7" type="checkbox" name="C7"><label for="C7">Fire Protection(C7)</label></div>
        </div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:row; padding-top:0px !important;">
        	<div style="width:390px;" class="productDiv"><strong>Cooling</strong></div>
            <div style="width:390px;" class="productDiv"><input id="C8" type="checkbox" name="C8"><label for="C8">Air Conditioning/Comfort Cooling(C8)</label></div>
            <div style="width:390px;" class="productDiv"><input id="C9" type="checkbox" name="C9"><label for="C9">Refrigeration(C9)</label></div>
        </div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:row; padding-top:0px !important;">
        	<div style="width:390px;" class="productDiv"><strong>Ventilation</strong></div>
            <div style="width:390px;" class="productDiv"><input id="C10" type="checkbox" name="C10"><label for="C10">IAQ/Air Movement(C10)</label></div>
        </div>
        <div style="clear:both; height:20px;"></div>
        <div class="SRFormLB" style="width:100%;display:inline-flex;display: -webkit-flex;flex-wrap:wrap;flex-direction:row; padding-top:0px !important;">
        	<div style="width:280px;" class="productDiv"><strong>Please enter the verification code :</strong>&nbsp;&nbsp;&nbsp;</div>
            <div><input name="vc" type="text" maxlength="4" id="vc" class="inputtext" style="width: 60px; margin-bottom:10px;" />&nbsp;&nbsp;&nbsp;</div>
            <div><img id="imgObj" src="" style="cursor:pointer;" width="67" height="21" alt="" onClick="changeImg();"></div>
        </div>
        <div style="clear:both; height:10px;"></div>
        <div style="width:100%; text-align:center;">
			<input type="hidden" name="ContactNumber" id="ContactNumber">
            <input type="button" name="bnOK" value="Submit Request" onClick="doSubmit();" id="bnOK" style="display:none">        
        </div>
    </div>
</div>
<script>
	<?php
	if($record!=''){
	?>
	$('#txtLastName').val('<?php echo $record->fields->LastName ?>');
	$('#txtFirstName').val('<?php echo $record->fields->FirstName ?>');
	$('#drop_Salut').val('<?php echo $record->fields->Salutation ?>');
	$('#txtJobTitle').val('<?php echo $record->fields->Title ?>');
	$('#txtCompanyName').val('<?php echo $record->fields->Account->fields->Name ?>');
	$('#txtAddress').val('<?php echo $record->fields->MailingStreet ?>');
	$('#txtCity').val('<?php echo $record->fields->MailingCity ?>');
	$('#txtProvince').val('<?php echo $record->fields->MailingState ?>');
	$('#txtPostalCode').val('<?php echo $record->fields->MailingPostalCode ?>');
	$('#txtTelephone').val('<?php echo $record->fields->Phone ?>');
	$('#txtEmailAddress').val('<?php echo $record->fields->Email ?>');
	$('#ContactNumber').val('<?php echo $record->fields->Contact_Number__c ?>');
	$('#bnOK').show();
	<?php
	}
	?>
</script>
</body>
</html>
