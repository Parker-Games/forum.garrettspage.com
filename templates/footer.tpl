{* $Id$ *}
{* ==> put in this file what is not displayed in the layout (javascript, debug..)*}
<div id="bootstrap-modal" class="modal fade footer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			{* Add header with title to avoid HTML validation errors for aria-labelledby missing a title while hidden.
			Gets replaced when modal becomes visible.*}
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>
		</div>
	</div>
</div>
<div id="bootstrap-modal-2" class="modal fade footer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		</div>
	</div>
</div>
<div id="bootstrap-modal-3" class="modal fade footer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		</div>
	</div>
</div>
{if isset($force_fill_action)}
	{include file="tiki-tracker_force_fill.tpl"}
{/if}
{if $module_pref_errors|default:null}
	<div class="container{if isset($smarty.session.fullscreen) && $smarty.session.fullscreen eq 'y'}-fluid{/if} modules">
		{remarksbox type="warning" title="{tr}Module errors{/tr}"}
			{tr}The following modules could not be loaded{/tr}
			<form method="post" action="tiki-admin.php">
				{ticket}
				{foreach from=$module_pref_errors key=index item=pref_error}
					<p>{$pref_error.mod_name}:</p>
					{preference name=$pref_error.pref_name}
				{/foreach}
				<div class="submit">
					<input type="submit" class="btn btn-primary" value="{tr}Change{/tr}"/>
				</div>
			</form>
		{/remarksbox}
	</div>
{/if}
{if (! isset($display) or $display eq '')}
	{if count($phpErrors)}
		{if ($prefs.error_reporting_adminonly eq 'y' and $tiki_p_admin eq 'y') or $prefs.error_reporting_adminonly eq 'n'}
	<div class="container{if isset($smarty.session.fullscreen) && $smarty.session.fullscreen eq 'y'}-fluid{/if} my-3">
		{button _ajax="n" _id="show-errors-button" _onclick="flip('errors');return false;" _text="{tr}Show PHP error messages{/tr}"}
		<div id="errors" class="alert alert-warning" style="display: {if (isset($smarty.session.tiki_cookie_jar.show_errors) and $smarty.session.tiki_cookie_jar.show_errors eq 'y') or $prefs.javascript_enabled ne 'y'}block{else}none{/if};">
			&nbsp;{listfilter selectors='#errors>div.rbox-data'}
			{foreach item=err from=$phpErrors}
				{$err}
			{/foreach}
		</div>
	</div>
		{/if}
	{/if}

	{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
		{* Include debugging console.*}
		{debugger}
	{/if}

{/if}

{if isset($prefs.socialnetworks_user_firstlogin) && $prefs.socialnetworks_user_firstlogin == 'y'}
	{include file='tiki-socialnetworks_firstlogin_launcher.tpl'}
{/if}

{if $prefs.site_google_analytics_account}
	{wikiplugin _name=googleanalytics account=$prefs.site_google_analytics_account group_option=$prefs.site_google_analytics_group_option groups={','|implode:$prefs.site_google_analytics_groups}}{/wikiplugin}
{/if}
{interactivetranslation}
<!-- Put JS at the end -->
{if $headerlib}
	{$headerlib->output_js_config()}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
	{* some js to enabled falsely detected js disabled browsers to be rechecked * disabled when in the installer *}
	{if empty($smarty.cookies.javascript_enabled_detect) and $prefs.javascript_enabled eq 'n' and $prefs.disableJavascript eq 'n' and $smarty.server.SCRIPT_NAME|strpos:'tiki-install.php' === false}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
if (confirm("A problem occurred while detecting JavaScript on this page, click ok to retry.")) {ldelim}
	document.cookie = "javascript_enabled_detect=";
	location = location.href;
{rdelim}
//--><!]]>
</script>
	{/if}
{/if}
{if $prefs.feature_endbody_code}
	{eval var=$prefs.feature_endbody_code}
{/if}
{if $prefs.site_piwik_code}
	{wikiplugin _name=piwik code=$prefs.site_piwik_code group_option=$prefs.site_piwik_group_option groups={','|implode:$prefs.site_piwik_groups}}{/wikiplugin}
{/if}
{if $prefs.feature_scheduler eq "y" && $prefs.webcron_enabled == 'y' && $prefs.webcron_type != 'url'}
	<script type="text/javascript">
		$(window).on('load', function () {
			function cron() {
				$.get("cron.php?token={$prefs.webcron_token|escape:url}");
			}
			setTimeout(cron, 500);
			setInterval(cron, 60000);
		});
	</script>
{/if}
{* when we are on a page to be printed open the print dialog auto-magically *}
{if !empty($print_page) and $print_page eq "y"}
	<script type="text/javascript">
		$(document).ready(function(){
			print();
		});
	</script>
{/if}
