{if $gBitSystem->isFeatureActive('multisites_per_site_content') and ( $gBitUser->hasPermission( 'p_multisites_restrict_content' ) or $gBitUser->isRegistered() ) }
{strip}
{jstab title="Restrict"}
	{legend legend="Restrict to Sites"}
		<div class="form-group">
			{if $multisitesList|@count ne 0}
				{formlabel label="Pick Sites" for="multisites"}
				{forminput}
					{if $multisitesList|@count < $gBitSystem->getConfig( 'multisites_limit_member_number' )}
						{foreach from=$multisitesList key=multisiteId item=site}
							<label>
								<input type="checkbox" value="{$multisiteId}" {if $site.0.selected}checked="checked" {/if}name="multisites[multisite][]" /> {$site.server_name|escape}
								<br />
							</label>
						{/foreach}
					{else}
						<select name="multisites[multisite][]" id="multisites" multiple="multiple" size="6">
							{foreach from=$multisitesList key=multisiteId item=site}
								<option value="{$multisiteId}" {if $site.0.selected}selected="selected"{/if} >
								{$site.server_name|escape}
								</option>
							{/foreach}
						</select>
					{/if}
				{/forminput}
			{else}
				{forminput}
					<p>{tr}There are no sites available at the moment.{/tr}</p>
					{if $gBitUser->isAdmin()}
						{smartlink ititle="Setup Multisites" ipackage="multisites" ifile="admin/edit_sites.php"}
					{/if}
				{/forminput}
			{/if}
		</div>
	{/legend}
{/jstab}
{/strip}
{/if}