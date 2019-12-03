<?php
if(empty($auth_user['shib_eppn'])){
	$url = urlencode(Router::url('/users/dashboard', $full = true));
	?>
	<div class="notice">
		<p>
			Do you have an institutional sigle-sign-on identity?
			Then please log in with your ID via Shibboleth to link your account
			to the DH-Courseregistry:
		</p>
		<p>
			Please log in
			<a href="<?php echo Configure::read('shib.idpSelect') . $url; ?>"
               onclick="alert('Single Sign-On is having problems at the moment. We are working on a solution...')">here</a>
			(you will be redirected to an external website).
		</p>
	</div>
	<?php
}
?>