---
$title@: Validators-vtypes-for-ext4js
author@: Viktor Zharina
$order: 255
$dates:
  published: 2016-03-16 04:04:49
---
[js]Ext.form.VTypes[&quot;hostnameVal1&quot;] = /^[a-zA-Z][-.a-zA-Z0-9]{0,254}$/;

Ext.form.VTypes[&quot;hostnameVal2&quot;] = /^[a-zA-Z]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9]){0,1}([.][a-zA-Z]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9]){0,1}){0,}$/;

Ext.form.VTypes[&quot;ipVal&quot;] = /^([1-9][0-9]{0,1}|1[013-9][0-9]|12[0-689]|2[01][0-9]|22[0-3])([.]([1-9]{0,1}[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2}[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-4])$/;

Ext.form.VTypes[&quot;netmaskVal&quot;] = /^(128|192|224|24[08]|25[245].0.0.0)|(255.(0|128|192|224|24[08]|25[245]).0.0)|(255.255.(0|128|192|224|24[08]|25[245]).0)|(255.255.255.(0|128|192|224|24[08]|252))$/;

Ext.form.VTypes[&quot;portVal&quot;] = /^(0|[1-9][0-9]{0,3}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$/;

Ext.form.VTypes[&quot;multicastVal&quot;] = /^((22[5-9]|23[0-9])([.](0|[1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])){3})|(224[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])([.](0|[1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2})|(224[.]0[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])([.](0|[1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])))$/;

Ext.form.VTypes[&quot;usernameVal&quot;] = /^[a-zA-Z][-_.a-zA-Z0-9]{0,30}$/;

Ext.form.VTypes[&quot;passwordVal1&quot;] = /^.{6,31}$/;

Ext.form.VTypes[&quot;passwordVal2&quot;] = /[^a-zA-Z].*[^a-zA-Z]/;

Ext.form.VTypes[&quot;hostname&quot;]=function(v){

 if(!Ext.form.VTypes[&quot;hostnameVal1&quot;].test(v)){

  Ext.form.VTypes[&quot;hostnameText&quot;]=&quot;Must begin with a letter and not exceed 255 characters&quot;

  return false;

 }

 Ext.form.VTypes[&quot;hostnameText&quot;]=&quot;L[.L][.L][.L][...] where L begins with a letter, ends with a letter or number, and does not exceed 63 characters&quot;;

 return Ext.form.VTypes[&quot;hostnameVal2&quot;].test(v);

}

Ext.form.VTypes[&quot;hostnameText&quot;]=&quot;Invalid Hostname&quot;

Ext.form.VTypes[&quot;hostnameMask&quot;]=/[-.a-zA-Z0-9]/;

Ext.form.VTypes[&quot;ip&quot;]=function(v){

 return Ext.form.VTypes[&quot;ipVal&quot;].test(v);

}

Ext.form.VTypes[&quot;ipText&quot;]=&quot;1.0.0.1 - 223.255.255.254 excluding 127.x.x.x&quot;

Ext.form.VTypes[&quot;ipMask&quot;]=/[.0-9]/;

Ext.form.VTypes[&quot;netmask&quot;]=function(v){

 return Ext.form.VTypes[&quot;netmaskVal&quot;].test(v);

}

Ext.form.VTypes[&quot;netmaskText&quot;]=&quot;128.0.0.0 - 255.255.255.252&quot;

Ext.form.VTypes[&quot;netmaskMask&quot;]=/[.0-9]/;

Ext.form.VTypes[&quot;port&quot;]=function(v){

 return Ext.form.VTypes[&quot;portVal&quot;].test(v);

}

Ext.form.VTypes[&quot;portText&quot;]=&quot;0 - 65535&quot;

Ext.form.VTypes[&quot;portMask&quot;]=/[0-9]/;

Ext.form.VTypes[&quot;multicast&quot;]=function(v){

 return Ext.form.VTypes[&quot;multicastVal&quot;].test(v);

}

Ext.form.VTypes[&quot;multicastText&quot;]=&quot;224.0.1.0 - 239.255.255.255&quot;

Ext.form.VTypes[&quot;multicastMask&quot;]=/[.0-9]/;

Ext.form.VTypes[&quot;username&quot;]=function(v){

 return Ext.form.VTypes[&quot;usernameVal&quot;].test(v);

}

Ext.form.VTypes[&quot;usernameText&quot;]=&quot;Username must begin with a letter and cannot exceed 255 characters&quot;

Ext.form.VTypes[&quot;usernameMask&quot;]=/[-_.a-zA-Z0-9]/;

Ext.form.VTypes[&quot;password&quot;]=function(v){

 if(!Ext.form.VTypes[&quot;passwordVal1&quot;].test(v)){

  Ext.form.VTypes[&quot;passwordText&quot;]=&quot;Password length must be 6 to 31 characters long&quot;;

  return false;

 }

 Ext.form.VTypes[&quot;passwordText&quot;]=&quot;Password must include atleast 2 numbers or symbols&quot;;

 return Ext.form.VTypes[&quot;passwordVal2&quot;].test(v);

}

Ext.form.VTypes[&quot;passwordText&quot;]=&quot;Invalid Password&quot;

Ext.form.VTypes[&quot;passwordMask&quot;]=/./;

[/js]