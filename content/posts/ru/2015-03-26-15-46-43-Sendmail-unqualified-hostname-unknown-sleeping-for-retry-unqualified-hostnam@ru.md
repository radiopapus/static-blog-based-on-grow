---
$title@: Sendmail-unqualified-hostname-unknown-sleeping-for-retry-unqualified-hostname
author@: Viktor Zharina
$order: 207
$dates:
  published: 2015-03-26 15:46:43
---
http://forum.linuxcareer.com/threads/1697-Sendmail-quot-unqualified-hostname-unknown-sleeping-for-retry-unqualified-hostname



Sendmail: "unqualified hostname unknown; sleeping for retry unqualified hostname

Description:

Sendmail hang or is very slow when sending an email. 

Code:

debian sm-mta[8129]: My unqualified host name (debian) unknown; sleeping for retry

Operating System:

Linux

Solution:

sendmail is searching for a FQDN ( fully qualified domain name ). In our case the host name is "debian" and that is not a FQDN. To resolve this problem change /etc/hosts:

FROM:

Code:

127.0.0.1       localhost

127.0.1.1       debian

TO

Code:

127.0.0.1       localhost.localdomain localhost debian

127.0.1.1       debian

Where "debian" is a hostname.