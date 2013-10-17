<?php

class devAsynchronousPublishingFilter implements ezpAsynchronousPublishingFilterInterface {
	
	function accept() {
		if (file_exists("/mnt/ebs/iamproduction.txt") && strpos($_SERVER[HTTP_HOST], 'thinkcreativeinternal') === false) {
			return true;
		} else {
			return false;
		}
	}
	
}

?>