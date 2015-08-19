<div class="loginBox">
		<div class="loginHeader">
			<a href="/{$conf.setup.admin}/"><img src="{$path.theme}/images/graphics/gx_loginLogo.png" alt="Tuksi" title="Tuksi.com"></a>
		</div>
		<div id="loginContent" class="loginContent" {if $showGetPassword}style="display:none;"{/if}>
		<form id="login-form" method="POST" onsubmit="doLogin();return false;">
		<input type="hidden" name="userAction" id="action_login" value="">
	
		{if $passwordsent}
			<table>
				<tr>
					<th>
						<h5>{cmstext value="lost_password"}</h5>
					</th>
				</tr>
				<tr>
					<td>
						{cmstext value="lost_password_text"}
					</td>
				</tr>
				<tr>
					<td class="column1" valign="bottom">
					<td class="column2" valign="bottom">
							<a href="/{$conf.setup.admin}/?treeid={$conf.link.login_treeid}" class="buttonType3 iconArrow"><span><span>{cmstext value="login_again"}</span></span></a>
							<input type="submit" style="visibility:hidden;">
					</td>
				</tr>
			</table>
			{else}
		<table>
				<tr>
					<th>
						<h5>{cmstext value=headline}</h5>
					</th>
					<th>
						{if $error}<em class="loginError">{cmstext value=loginerror}</em>{/if}
					</th>
				</tr>
				<tr>
					<td class="column1">
						<label>{cmstext value="login"}:</label>
					</td>
					<td class="column2">
						<div><input type="text" class="text" id="username" name="username" tabindex="1"></div>
					</td>
				</tr>
				<tr>
					<td class="column1">
						<label>{cmstext value="password"}:</label>
					</td>
					<td class="column2">
						<div><input type="password" class="password" name="password" id="password" tabindex="2"></div>
					</td>
				</tr>
				<tr>
					<td class="column1">
						<label>{cmstext value="remember"}:</label>
					</td>
					<td class="column2">
						<input type="checkbox" class="checkbox" name="remember" id="remember" tabindex="3">
					</td>
				</tr>
				<tr>
					<td class="column1" valign="bottom">
						<ul>
							<li><a href="#" onclick="$('passHelp').show(); $('loginContent').hide(); return false;">{cmstext value="lost_password"}?</a></li>
							<li><a href="#" onclick="$('loginHelp').toggle();return false;">{cmstext value="help"}?</a></li>
						</ul>
					</td>
					<td class="column2" valign="bottom">
							<a tabindex="4" href="javascript:void(0);" onclick="doLogin();" class="buttonType3 iconArrow"><span><span>{cmstext value="signin"}</span></span></a>
							<input type="submit" style="visibility:hidden;">
					</td>
				</tr>
			</table>
			{/if}
			</form>
		</div>
		<div id="passHelp" class="loginContent passHelp" {if !$showGetPassword}style="display:none;"{/if}>
			<form id="lostpassword-form" method="POST" onsubmit="sendPassword();return false;">	
				<input type="hidden" name="userAction" id="action_lostpassword" value="">
			<table>
				<tr>
					<th colspan="2">
						<h5>{cmstext value="lost_password"}?</h5>	{if $emailerror}<em class="loginError">{cmstext value=text_loginemailnotfound}</em>{/if}
					</th>
					<th>
						
					</th>
				</tr>
				<tr>
					<td colspan="2">
					{cmstext value="text_lostpassword"}:
					</td>
				</tr>
				<tr>
					<td class="column1">
						<label>{cmstext value="email"}:</label>
					</td>
					<td class="column2">
						<div><input type="text" class="text" name="email" id="email"></div>
					</td>
				</tr>
				<tr>
					<td class="column1" valign="bottom">
						<ul>
							<li><a href="#" onclick="$('loginHelp').toggle();return false;">{cmstext value="help"}?</a></li>
						</ul>
					</td>
					<td class="column2" valign="bottom">
						<a href="javascript:void(0);" onclick="sendPassword();" class="buttonType3 iconEmail"><span><span>Send</span></span></a>
						<input type="submit" style="visibility:hidden;">
					</td>
				</tr>
			</table>
			</form>
		</div>
		<div id="loginHelp" class="loginHelp" style="display:none;">
			<div class="helpContent">
				{cmstext value="text_loginhelp"}
			</div>
		</div>
	</div>
