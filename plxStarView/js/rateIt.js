window.onload = function() {	
	for (let e of  document.querySelectorAll('form[id^="rate-"]')) {
		
		let formId=e.getAttribute('id');
		let id = formId.slice(5); 
		let form_data = new FormData(e);	
		
		for (let b of  document.querySelectorAll('form[id='+formId+'] button')){
			b.addEventListener("click", function () {
			let name = b.getAttribute('name');
			let value = b.getAttribute('value');						
			form_data.append(name, value);
			});
		};					
					
	e.addEventListener("submit", function () {
			event.preventDefault(); 
		
		let xhttp = new XMLHttpRequest();
		let upUrl= '../../plugins/plxStars/rateIt.php';
		xhttp.open("POST", upUrl , true);
		xhttp.onload = function(event) {
			output = document.querySelector('#rate-'+id);
			if (xhttp.status == 200) {
				output.outerHTML = this.responseText;
				}
			};
	xhttp.send(form_data);
	}); 	
		
	}
}