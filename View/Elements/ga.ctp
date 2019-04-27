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
$isTrackingEnabled = Configure::read('Site.Tracking.GoogleAnalytics.enabled');

// determine if a admin-user session is active
$isAdminUser = Access::hasRole('Admin');

// track non-admin users, if enabled
if($isTrackingEnabled && !$isAdminUser) :

	$trackingId = $this->request->is('backend')
		? Configure::read('Site.Tracking.GoogleAnalytics.portalAccountID')
		: Configure::read('Site.Tracking.GoogleAnalytics.publicAccountID');

	if (!empty($trackingId)) :
?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', <?= json_encode($trackingId); ?>]);
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
<?php endif; // have valid tracking ID ?>
<?php endif; // tracking is enabled ?>
<!--/nocache-->