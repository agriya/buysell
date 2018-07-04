<script type="text/javascript">
	if (window.opener && window.opener.document) {
		window.opener.location.href = "{{ Url::action('AuthController@getExternalSignup') }}";
		window.close();
	}
	else
	{
		window.location.href = "{{ Url::action('AuthController@getExternalSignup') }}";
	}
</script>