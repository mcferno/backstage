<?php
/**
 * Determines which Google Analytics tracker to use, if any.
 * 
 * Some effort is made to avoid tracking site administrators as this heavily
 * skews the reporting data (due to testing, site upkeep).
 * 
 * This element should not be cached, but is cache friendly due to the use of
 * no-cache tags and sessions data-only (available even in cache)
 */
?>
<!--nocache-->
<?php
App::uses('Access', 'Model');

// determine if we are on the live domain
$isLiveDomain = (stripos(env('HTTP_HOST'),'kennyquotemachine.com') !== false);

// determine if a admin-user session is active
$isAdminUser = Access::hasRole('Admin');

// determine which tracker to use.
$isBackend = (isset($this->request->params['prefix']) && ($this->request->params['prefix'] == 'admin'));

// only use Analytics if both conditions are met
if($isLiveDomain && !$isAdminUser) : 
?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?= ($isBackend)?'UA-23502634-2':'UA-23502634-1'; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php else: // Analytics is disabled ?>
<script type="text/javascript">
	var _gaq = [];
</script>
<?php endif; ?>
<!--/nocache-->