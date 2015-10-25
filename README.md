# Nest Extend
Nest Extend is a WordPress Plugin for use with the Nest Cloud API. It provides a wrapper around the Nest Cloud REST API.

https://developer.nest.com/documentation/api-reference/overview

Nest Extend Example Usage
----------------------------

```  js

jQuery(function($) {
	var readDevices = function() {
		/* Prepare the request */
		var data = {
			'action': 'nest_extend',
			'method': 'devices'
		};

		/* Perform the request */
		jQuery.post(ajaxurl, data, function(response) {
			/* Dump the result */
			console.log(response);
		});
	}
});
```
