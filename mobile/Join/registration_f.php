<?php include('vdaemon.php'); ?>
<html>
<head>
<title>Registration Form Sample</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="samples.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1>Registration Form Sample</h1>
<form id="Register" action="index.php" method="POST" runat="vdaemon" disablebuttons="all">
  <table cellpadding="2" cellspacing="0" border="0">
    <tr>
      <td width="130">
        <vllabel validators="username,UserIDExist" errclass="error" for="username" cerrclass="controlerror">User name</vllabel>
      </td>
      <td width="140">
        <input name="username" type="text" class="control" id="username" size="15">
        <vlvalidator name="username" type="required" control="UserID" errmsg="User ID required">
        <vlvalidator name="UserIDExist" type="custom" control="UserID" errmsg="User ID already exist" function="UserIDCheck">
      </td>
      <td width="300" rowspan="7" valign="top">
        <vlsummary class="error" headertext="Error(s) found:" displaymode="bulletlist">
      </td>
    </tr>
    <tr>
      <td>
        <vllabel errclass="error" validators="Password,PassCmp" for="Password" cerrclass="controlerror">Password:</vllabel>
      </td>
      <td>
        <input name="password" type="password" class="control" id="password" size="15">
        <vlvalidator type="required" name="Password" control="Password" errmsg="Password required">
        <vlvalidator name="PassCmp" type="compare" control="Password" comparecontrol="Password2"
          operator="e" validtype="string" errmsg="Both Password fields must be equal">
      </td>
    </tr>
    <tr>
     
    </tr>
    <tr>
      <td>
        <vllabel errclass="error" validators="NameReq,NameRegExp" for="Name" cerrclass="controlerror">name:</vllabel>
      </td>
      <td>
        <input name="real_name" type="text" class="control" id="real_name" size="15">
        <vlvalidator type="required" name="NameReq" control="Name" errmsg="Name required">
        <vlvalidator type="regexp" name="NameRegExp" control="Name" regexp="/^[A-Za-z'\s]*$/" errmsg="Invalid Name">
      </td>
    </tr>
    <tr>
      <td>
        <vllabel errclass="error" validators="EmailReq,Email" for="Email" cerrclass="controlerror">E-mail:</vllabel>
      </td>
      <td>
        <input name="email" type="TEXT" class="control" id="email" size="15">
        <vlvalidator type="required" name="EmailReq" control="Email" errmsg="E-mail required">
        <vlvalidator type="format" format="email" name="Email" control="Email" errmsg="Invalid E-mail">
      </td>
    </tr>
    <tr>
     
    </tr>
    <tr>
      <td>
        <input type="file" name="uploadedfile" size="20"> </td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="submit" class="control" value="Register"></td>
    </tr>
  </table>
</form>
</body>
</html>
<?php VDEnd(); ?>