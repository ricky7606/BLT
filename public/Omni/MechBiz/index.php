<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP Salesforce test</title>
</head>

<body>
<?php
define("USERNAME", "invoicing@omnidataservices.com");
define("PASSWORD", "g0awayn0w");
define("SECURITY_TOKEN", "2eSgCn54B4DsRwRcy0Is8p6P");
 
require_once ('soapclient/SforcePartnerClient.php');
 
$mySforceConnection = new SforcePartnerClient();
$mySforceConnection->createConnection("PartnerWSDL.xml");
$mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);
echo "1";
 
$query = "select LastName, FirstName, Salutation, Title, Account.Name, MailingStreet, MailingCity, MailingState, MailingPostalCode, Phone, Email, Contact_Number__c from Contact where XID__c = '0031J00001caaTkQAI'";
$response = $mySforceConnection->query($query);
 
foreach ($response->records as $record)
{
echo '<tr>
    <td>'.$record->Id.'</td>
    <td>'.$record->fields->FirstName.'</td>
    <td>'.$record->fields->LastName.'</td>
    <td>'.$record->fields->Phone.'</td>
     </tr>';
 }
?>
</body>
</html>