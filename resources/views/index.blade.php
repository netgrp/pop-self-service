@extends ('layouts.master')

@section ('title')
Selvbetjening - nulstil kodeord
@endsection


@section ('content')

<div id="root">
	<user-consent v-if="!consent">
		<p>
			Her kan du <b>nulstille det kodeord</b> du bruger til at gå på <b>kollegiets wifi, og på vores bookingsystem (book.pop.k-net.dk)</b>.
		</p>

		<p>
			Jeg bekræfter at jeg kun vil bruge denne selvbetjeningsportal til at nulstille mit eget kodeord, ydermere at
			<ul>
				<li>Information om mit besøg vil blive gemt, herunder (men ikke begrænset til) information om min browser og min IP-addresse</li>
				<li>Misbrug vil blive anmeldt og betragtes som grov overtrædelse af brugerklæringen. Har du opdaget et sikkerhedsproblem, så skriv til netværksudvalget, kontaktoplysninger er i højre side.</li>
			</ul>

			<center>
				<button @click="consent = true" type="button" class="btn btn-primary">Accepter og fortsæt</button>
			</center>
		</p>
	</user-consent>
	<password-reset v-if="consent">
		<form @submit="sendResetRequest">
		  <div class="form-group">
		    <label for="roomNumber">Værelsesnummer</label>
		    <input type="number" min=2 max=299 class="form-control" id="roomNumber" v-model="roomNumber" aria-describedby="roomNumberHelp" placeholder="Værelsesnummer" required :disabled="sendok || loading">
		    <small id="roomNumberHelp" class="form-text text-muted">Angiv dit værelsesnummer.</small>
		  </div>
		  <div class="form-group">
		    <label for="Email">E-mail</label>
		    <input type="email" class="form-control" id="Email" v-model="email" aria-describedby="emailHelp" placeholder="E-mail" required :disabled="sendok || loading">
		    <small id="emailHelp" class="form-text text-muted">Angiv din e-mail addresse.</small>
		  </div>

			<div class="alert alert-danger" v-if="roomok === false">
				<strong>Fejl!</strong> Dit kodeord kan ikke nulstilles via selvbetjeningen. Du bedes tage kontakt til en fra netværksudvalget.
			</div>

			<center v-if="roomok !== false && sendok !== true">
				<input type=submit v-if="loading" class="btn btn-secondary" disabled="" value="Vent venligst..">
			    <input type=submit v-else-if="roomNumber === '' || email === ''" class="btn btn-secondary" disabled value="Nulstil kodeord">
			    <input type=submit v-else @click="sendResetRequest" class="btn btn-primary" value="Nulstil kodeord">
			</center>
		    <div class="alert alert-success" v-if="sendok">
		    	<strong>Success!</strong> Hvis den korrekte e-mail addresse er oplyst, vil du om lidt modtage en mail med et link. Tryk på dette link for at nulstille din kode.
		    </div>
		</form>
	</password-reset>
	<noscript>Siden virker ikke uden JavaScript</noscript>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js" integrity="sha256-FtWfRI+thWlNz2sB3SJbwKx5PgMyKIVgwHCTwa3biXc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js" integrity="sha256-mpnrJ5DpEZZkwkE1ZgkEQQJW/46CSEh/STrZKOB/qoM=" crossorigin="anonymous"></script>

        <script>
            Vue.component('user-consent', {
                template: '<p><slot></slot></p>',
            })

            Vue.component('password-reset', {
                template: '<p><slot></slot></p>',
            })

            new Vue({
                el: "#root",
                data: {
                	consent: false,
                	roomNumber: '',
                	loading: false,
                	roomok: null,
                	email: '',
                	sendok: false,
                },

                methods: {
                    sendResetRequest() {
                    	event.preventDefault();
                    	this.loading = true;
                    	axios.post('/api/resetPassword', {
                        	consent: this.consent,
                        	roomNumber: this.roomNumber,
                        	email: this.email,
                        })
                        .then(reponse => {
                        	this.roomok = reponse.data['roomok'];
                        	this.sendok = reponse.data['sendok'];
                        	this.loading = false;
                        })
                        .catch(error => alert(error));
                    },
                }
            })
        </script>

@endsection