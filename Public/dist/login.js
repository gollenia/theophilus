// "Submit"-Event abfangen und Login-Funktion aufrufen
submit =  document.getElementById("login");
submit.addEventListener ('submit', function (event) {
   tryLogin();
   event.preventDefault();
});

function tryLogin() {
	axios.post(base_url, {
		controller: 'user',
		method: 'login',
		user:  document.getElementById("user").value,
		pass: document.getElementById("pass").value,
		sticky: document.getElementById("sticky").value
	}).then(function(response) {
		// Bei Erfolg: Seite Neu laden
		location.reload();
	}).catch(function(error){
		document.getElementById("login").classList.add("uk-animation-shake");
		setTimeout(function(){
			document.getElementById("login").classList.remove("uk-animation-shake");
		}, 300);
	});
}