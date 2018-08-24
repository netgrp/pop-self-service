<template>
	<div>
		<div v-if="!consent">
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
		</div>
		<div v-if="consent">
			<form @submit.prevent="sendResetRequest">
			  <div class="form-group">
			    <label for="Email">E-mail</label>
			    <input type="email" @keydown="hasErrors = false" class="form-control" id="Email" v-model="email" aria-describedby="emailHelp" placeholder="E-mail" required :disabled="sendok || loading">
			    <small id="emailHelp" class="form-text text-muted">Angiv din e-mail addresse.</small>
			  </div>

			  <vue-recaptcha
                  ref="recaptcha"
                  @verify="onCaptchaVerified"
                  @expired="onCaptchaExpired"
                  size="invisible"
                  v-bind:sitekey="sitekey">
                </vue-recaptcha>

				<div class="alert alert-danger" v-if="hasErrors">
					<strong>Fejl!</strong> Indtast en gyldig e-mailadresse.
				</div>
				<div class="alert alert-danger" v-if="sendok === false">
					<strong>Fejl!</strong> Kan ikke sende dig en mail lige nu. Prøv igen senere.
				</div>
			    <div class="alert alert-success" v-if="sendok">
			    	<strong>Success!</strong> Vi har sendt dig en mail med yderligere instrukser.
			    </div>

				<center v-if="sendok !== true">
					<input type=submit v-if="loading" class="btn btn-secondary" disabled="" value="Vent venligst..">
				    <input type=submit v-else :class="(hasErrors) ? 'btn btn-secondary' : 'btn btn-primary'" :disabled="hasErrors" value="Nulstil kodeord">
				</center>
			</form>
		</div>
	</div>
</template>

<script>
	import VueRecaptcha from 'vue-recaptcha';
	export default {
		props: ['sitekey'],
		components: { VueRecaptcha },
        data() {
	  		return {
	        	consent: false,
	        	loading: false,
	        	email: '',
	        	sendok: null,
	        	hasErrors: false,
	        	reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
	        };
	    },
	    methods: {
	        sendResetRequest() {
	        	if (!this.loading && this.email != '' && this.consent && this.isEmailValid())
	        	{
	        		this.loading = true;
	        		this.$refs.recaptcha.execute();
	        	}
	        	else {
	        		this.hasErrors = true;
	        	}
	        },
	        onCaptchaVerified(recaptchaToken) {
	        	this.$refs.recaptcha.reset();
	        	axios.post('/resetPassword', {
	            	consent: this.consent,
	            	email: this.email,
	            	recaptchaToken: recaptchaToken,
	            })
	            .then(reponse => {
	            	this.sendok = reponse.data['sendok'];
	            	this.loading = false;
	            })
	            .catch(error => {
	            	this.sendok = false;
	            	this.loading = false;
	            });
	        },
	        onCaptchaExpired() {
	        	this.$refs.recaptcha.reset();
	        },
	        isEmailValid: function() {
				return (this.email == "")? "" : this.reg.test(this.email);
			},
	    }
	}
</script>