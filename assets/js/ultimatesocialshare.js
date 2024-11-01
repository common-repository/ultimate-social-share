document.addEventListener("DOMContentLoaded", function() {

	var ultimatesocialshareButtons = document.querySelectorAll(".ultimatesocialshare-button:not(.ultimatesocialshare-follow-button), .ultimatesocialshare-ctt, .ultimatesocialshare-pinterest-image-button");

	ultimatesocialshareButtons.forEach(function(button) {

		button.addEventListener('click', function(e) {

			//ignore whatsapp and email
			if(this.classList.contains('whatsapp') || this.classList.contains('email') || this.classList.contains('sms') || this.classList.contains('messenger')) {
				return;
			}

			//ignore twitter if the twttr window is not available
			if((this.classList.contains('twitter') || this.classList.contains('ultimatesocialshare-ctt')) && typeof window.twttr != 'undefined') {
				return;
			}

			//disable link event
			e.preventDefault();

			//check for print button
			if(this.classList.contains('print')) {
				window.print();
				return;
			}

			//stop if we don't have a link to use
			if(this.getAttribute("href") == '#' || this.href == '#') {
				return false;
			}

			//take focus off of clicked element
			this.blur();

			//setup window dimensions
			var window_size = {
				width  : 700,
				height : 300
			}

			//specific dimensions for buffer
			if(this.classList.contains('buffer')) {
				window_size.width = 800;
				window_size.height = 575;
			}

			//grab url
			var url = (typeof this.href != 'undefined' ? this.href : this.getAttribute("href"));

			//open popup window
			window.open(url,'targetWindow', "toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=" + window_size.width + ",height=" + window_size.height + ",top=200,left=" + (window.innerWidth - window_size.width)/2);
		});
	});
});