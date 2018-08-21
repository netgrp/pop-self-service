<template>
	<div><slot></slot></div>
</template>

<script>
	export default {
	    methods: {
	        sendResetRequest() {
	        	event.preventDefault();
	        	this.loading = true;
	        	axios.post('/api/resetPassword', {
	            	consent: this.consent,
	            	email: this.email,
	            })
	            .then(reponse => {
	            	this.sendok = reponse.data['sendok'];
	            	this.loading = false;
	            })
	            .catch(error => alert(error));
	        },
	    }
	}
</script>