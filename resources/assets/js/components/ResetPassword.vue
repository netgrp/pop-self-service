<template>
    <div>
        <form @submit.prevent="sendResetRequest">
            <div class="form-group" v-if="username">
                <label>Ændre brugernavn?</label>
                <input type="username" class="form-control" v-model="username" aria-describedby="usernameHelp" disabled>
                <small id="usernameHelp" class="form-text text-muted">Dit brugernavn</small>
                <div class="form-check">
                  <input class="form-check-input" type="radio" :disabled="sendok || loading" v-model="username_reset" id="username_reset1" value="" checked>
                  <label class="form-check-label" for="username_reset1">
                    Uændret.
                  </label>
                </div>
                <div class="form-check" v-if="normalized">
                  <input class="form-check-input" type="radio" :disabled="sendok || loading" v-model="username_reset" id="username_reset2" value="normalize">
                  <label class="form-check-label" for="username_reset2">
                    Normaliser brugernavn. Fjerner specialtegn og ændre alle store bogstaver til små bogstaver.
                  </label>
                </div>
                <div class="form-check disabled" v-if="email">
                  <input class="form-check-input" type="radio" :disabled="sendok || loading" v-model="username_reset" id="username_reset3" value="email">
                  <label class="form-check-label" for="username_reset3">
                    Ændre til din e-mail adresse.
                  </label>
                </div>
            </div>
            <div class="form-group">
                <label for="Password">Nyt kodeord</label>
                <input type="password" @keydown="hasErrors = false" :disabled="sendok || loading" required class="form-control" id="Password" v-model="password" aria-describedby="passwordHelp" placeholder="Nyt kodeord">
                <small id="passwordHelp" class="form-text text-muted">Angiv dit nye kodeord.</small>
            </div>
            <div class="form-group">
                <label for="PasswordConfirmed">Gentag nyt kodeord</label>
                <input type="password" @keydown="hasErrors = false" :disabled="sendok || loading" required class="form-control" id="PasswordConfirmed" v-model="password_confirmation" aria-describedby="passwordConfirmedHelp" placeholder="Gentag nyt kodeord">
                <small id="passwordConfirmedHelp" class="form-text text-muted">Gentag dit nye kodeord.</small>
            </div>

            <div class="alert alert-danger" v-if="pwned">
                <strong>Fejl!</strong> Koden er ikke sikker nok. Læs mere <a href="https://haveibeenpwned.com/Passwords">her</a>.
            </div>
            <div class="alert alert-danger" v-if="hasErrors">
                <strong>Fejl!</strong> Koden må ikke være tom, skal være mindst 6 tegn og skal skrives ens to gange.
            </div>
            <div class="alert alert-danger" v-if="sendok === false">
                <strong>Fejl!</strong> Prøv igen senere. Fortsætter problemet så kontakt netgruppen.
            </div>
            <div class="alert alert-success" v-if="sendok">
                <strong>Success!</strong> {{ submitText }} er gennemført.
            </div>

            <center v-if="sendok !== true">
                <input type=submit :class="(hasErrors || loading) ? 'btn btn-secondary' : 'btn btn-primary'" :disabled="hasErrors || loading" v-model="submitText">
            </center>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['userinfo','token'],
        data() {
            return {
                unchanged: null,
                email: null,
                normalized: null,
                password: '',
                password_confirmation: '',
                sendok: null,
                loading: false,
                hasErrors: false,
                username_reset: '',
                pwned: false,
            };
        },
        mounted() {
            let temp = JSON.parse(this.userinfo);
            this.unchanged = temp['unchanged'];
            this.email = temp['email'];
            this.normalized = temp['normalized'];
        },
        computed: {
            username: {
                get: function() {
                    if (this.username_reset == '') {
                        return this.unchanged;
                    }
                    else if (this.username_reset == 'normalize') {
                        return this.normalized;
                    }
                    else if (this.username_reset == 'email') {
                        return this.email;
                    }
                },
                set: function() {
                    return this.unchanged;
                },
            },
            submitText: {
                get: function() {
                    if (this.loading) {
                        return "Vent venligst...";
                    }
                    else if (this.username_reset == '') {
                        return "Nulstil kodeord";
                    } else {
                        return "Nulstil brugernavn og kodeord";
                    }
                },
                set: function() {
                    if (this.username_reset == '') {
                        return "Nulstil kodeord";
                    } else {
                        return "Nulstil brugernavn og kodeord";
                    }
                },
            }
        },
        methods: {
            sendResetRequest() {
                if (this.password == this.password_confirmation && this.password != '' && this.password.length > 5) {
                    this.loading = true;
                    this.pwned = false;
                    axios.patch('/reset/'+this.token, {
                        username_reset: this.username_reset,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    })
                    .then(reponse => {
                        this.sendok = reponse.data['sendok'];
                        this.loading = false;
                    })
                    .catch(error => {
                        if (error.response) {
                            if (error.response.data['errors']['password'][0] == "validation.pwned") {
                                this.pwned = true;
                            }
                            else if (Object.keys(error.response.data['errors']).length > 0) {
                                this.hasErrors = true;
                            }
                            else {
                                this.sendok = false;
                            }
                        }
                        else {
                            this.sendok = false;
                        }
                        this.loading = false;
                    });
                } else {
                    this.hasErrors = true;
                }
            },
        },
    }
</script>
