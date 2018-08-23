<template>
	<div>
		<form>
			<div class="form-group">
				{{ userinfo }}
			    <label>Ændre brugernavn?</label>
			    <input type="username" class="form-control" v-model="username" aria-describedby="usernameHelp" disabled>
			    <small id="usernameHelp" class="form-text text-muted">Dit brugernavn</small>
				<div class="form-check">
				  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
				  <label class="form-check-label" for="exampleRadios1">
				    Uændret.
				  </label>
				</div>
				<div class="form-check">
				  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
				  <label class="form-check-label" for="exampleRadios2">
				    Normaliser brugernavn. Fjerner specialtegn og ændre alle store bogstaver til små bogstaver.
				  </label>
				</div>
				<div class="form-check disabled">
				  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3">
				  <label class="form-check-label" for="exampleRadios3">
				    Ændre til din e-mail adresse.
				  </label>
				</div>
			</div>
			<div class="form-group">
			    <label for="Password">Nyt kodeord</label>
			    <input type="password" class="form-control" id="Password" v-model="password" aria-describedby="passwordHelp" placeholder="Nyt kodeord">
			    <small id="passwordHelp" class="form-text text-muted">Angiv dit nye kodeord.</small>
			</div>
			<div class="form-group">
			    <label for="PasswordConfirmed">Gentag nyt kodeord</label>
			    <input type="password" class="form-control" id="PasswordConfirmed" v-model="password_confirmation" aria-describedby="passwordConfirmedHelp" placeholder="Gentag nyt kodeord">
			    <small id="passwordConfirmedHelp" class="form-text text-muted">Gentag dit nye kodeord.</small>
			</div>
			<center v-if="sendok !== true">
				<input type=submit v-if="loading" class="btn btn-secondary" disabled="" value="Vent venligst..">
			    <input type=submit v-else-if="email === ''" class="btn btn-secondary" disabled value="Nulstil kodeord (og brugernavn)">
			    <input type=submit v-else @click="sendResetRequest" class="btn btn-primary" value="Nulstil kodeord (og brugernavn)">
			</center>
		</form>
	</div>
</template>

<script>
	export default {
		props: ['userinfo'],
	}
</script>