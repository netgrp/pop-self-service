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
		<form v-on:submit.prevent>
		  <div class="form-group">
		    <label for="roomNumber">Værelsesnummer</label>
		    <input type="number" min=2 max=299 class="form-control" id="roomNumber" v-model="roomNumber" aria-describedby="roomNumberHelp" placeholder="Værelsesnummer" required :disabled="roomok || roomok === false">
		    <small id="roomNumberHelp" class="form-text text-muted">Angiv dit værelsesnummer.</small>
		  </div>
		</form>

		<form v-on:submit.prevent v-if="roomok && roomNumber != ''">
		  <div class="form-group">
		    <label for="Email">E-mail</label>
		    <input type="email" class="form-control" id="Email" v-model="email" aria-describedby="emailHelp" placeholder="E-mail" required :disabled="sendok">
		    <small id="emailHelp" class="form-text text-muted">Angiv din e-mail addresse.</small>
		  </div>
		</form>

		<p v-if="roomok === false">
			Dit kodeord kan ikke nulstilles via selvbetjeningen. Du bedes tage kontakt til en fra netværksudvalget.
		</p>

		<center v-if="roomok !== false">
			<button v-if="loading" type="button" class="btn btn-secondary" disabled="">Vent venligst..</button>
			<button v-else-if="sendok" type="button" class="btn btn-success" disabled>Sendt</button>
			<button v-else-if="roomNumber === ''" type="button" class="btn btn-secondary" disabled>Næste</button>
		    <button v-else-if="roomok && email === ''" type="button" class="btn btn-secondary" disabled="">Nulstil kodeord</button>
		    <button v-else-if="roomok" @click="sendResetRequest" type="button" class="btn btn-primary">Nulstil kodeord</button>
		    <button v-else @click="checkRoom" type="button" class="btn btn-primary">Næste</button>

		    <p v-if="sendok"><b>Hvis den korrekte e-mail addresse er oplyst, vil du om lidt modtage en mail med et link. Tryk på dette link for at nulstille din kode.</b></p>
		</center>
	</password-reset>
	<noscript>JavaScript must be enabled</noscript>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js" integrity="sha256-FtWfRI+thWlNz2sB3SJbwKx5PgMyKIVgwHCTwa3biXc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js" integrity="sha256-mpnrJ5DpEZZkwkE1ZgkEQQJW/46CSEh/STrZKOB/qoM=" crossorigin="anonymous"></script>

        <script>
            Vue.component('user-consent', {
                template: '<p><slot></slot></p>',

                data() {
                    return {
                        message: 'Foobar'
                    }
                }
            })

            Vue.component('password-reset', {
                template: '<p><slot></slot></p>',

                data() {
                    return {
                        message: 'Foobar'
                    }
                }
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
                    checkRoom() {
                    	this.loading = true;
                        axios.post('/api/checkRoom', {
                        	roomNumber: this.roomNumber,
                        })
                        .then(reponse => {
                        	this.roomok = reponse.data['roomok'];
                        	this.loading = false;
                        })
                        .catch(error => alert(error));
                    },
                    sendResetRequest() {
                    	this.loading = true;
                    	axios.post('/api/resetPassword', {
                        	roomNumber: this.roomNumber,
                        	email: this.email,
                        })
                        .then(reponse => {
                        	this.sendok = reponse.data['sendok'];
                        	this.loading = false;
                        })
                        .catch(error => alert(error));
                    },
                }
            })
        </script>

@endsection