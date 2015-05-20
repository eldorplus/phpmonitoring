phpMonitoring is a 100% php based (with a mysql back end) server/network/website monitoring platform - all web based. The project goal is to be easy to setup and use. Secondary goal is making it easy for any php developer to make plug-ins.  Plug-ins can do just about anything  - even taking steps to healing the application with issues.  This app is designed to really only run on a Linux machine - although it could work on most posix based OS's.  On windows many plug-ins wouldn't work.  phpMonitoring is perfect for those who have any type of script(s) monitoring anything and would like to package those scripts together to a central place and have reporting/notices all done automatically.  We also recently added a special iphone webapp with home screen icon so you'll always be able to keep tabs on your network.


Best way to get the latest code is with SVN - the downloadable tar is most likely old - this assumes you want to put the files in the directory - /var/www/html/phpmonitoring


svn checkout http://phpmonitoring.googlecode.com/svn/trunk/ /var/www/html/phpmonitoring


if you don't have svn installed run this:

debian based:

> apt-get install subversion

redhat based:

> yum install subversion


