<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="da">
<head>
<link rel="SHORTCUT ICON" href="/favicon.ico">
	<title>Tuksi 4.0 - Login</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="robots" content="all">
	<meta name="revisit-after" content="7">
	<meta http-equiv="imagetoolbar" content="no">

	<meta http-equiv="imagetoolbar" content="false">
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<style type="text/css">
		@import url("/themes/default/stylesheet/style.css");
	</style>
	<title>tuksi login</title>
	<script src="/core/javascript/libs/md5.js" type="text/javascript"></script>
	<script src="/core/javascript/libs/prototype.js" type="text/javascript"></script>
	<script src="/core/javascript/libs/tuksi.login.js" type="text/javascript"></script>

</head>
<body>
<div id="height">
	<div class="main">

		<div class="mainHeader">
			<a href="?logo" class="TuksiLogo"><img src="/images/TuksiLogo.png" alt="Tuksi"></a>
			<div class="headerInfo">
				<span>Du er logget ind som: Andreas Mailand </span><img src="/images/icons/ic_headerInfo.png" alt="icon" title="">
				<a href="kontrolpanel.htm">Kontrolpanel</a>
			</div>
		</div><!--//End mainHeader-->
		<div class="mainInner">

			<div class="mainLeft">
				<div class="leftFrame">
					<div class="leftMenu">
						{treestructure nodes=$nodes}

					</div><!--//End leftMenu-->
				</div><!--//End leftFrame-->
			</div><!--//End mainLeft-->
			<div class="mainRight">
				<div class="rightTop">
					<div class="virtTopTabsMenu"><!--Virtual placeholder--> &nbsp;</div>
					<div class="virtTopActionButtons"><!--Virtual placeholder--> &nbsp;</div>
					<div class="breadcrumbs">

						<strong>Du er her:</strong> <a href="#">Forside</a><span>&gt;</span><a href="#">Moduler</a><span>&gt;</span> Standardfunktionalitet
					</div>
				</div><!--//End rightTop-->
				<div class="theFrame">
					<div class="innerFrame">
						<div id="scrollFrame" class="scrollFrame">
							<div class="frameContent">

							
							
							<div class="pTwoColumnsLayout">
								<div class="pColum1">
									
									<div class="mBoxItem">
										<div class="boxItemHeader">
											<h6>Nyeste sider</h6>
										</div>
										<table>
											<tr class="link">

												<th>Dato oprettet</th>
												<th>Navn</th>
												<th>Bruger</th>
											</tr>
											<tr class="link">
												<td>Idag - 18:01</td>
												<td>Webshop</td>

												<td>MWA</td>
											</tr>
											<tr class="link">
												<td>19.09.07 - 15:21</td>
												<td>Lorem ipsum</td>
												<td>AMA</td>
											</tr>

											<tr class="link">
												<td>11.10.07 - 20:22</td>
												<td>Lorem regneark</td>
												<td>ALE</td>
											</tr>
											<tr class="link">
												<td>19.10.07 - 12:21</td>

												<td>Lorem dolor ipsum</td>
												<td>RPE</td>
											</tr>
											<tr class="link">
												<td>12.09.07 - 12:30</td>
												<td>Lorem ipsum</td>
												<td>MWA</td>

											</tr>
										</table>
										<div class="boxItemFooter"></div>
									</div><!--//End mBoxItem-->
									
									<div class="mBoxItem">
										<div class="boxItemHeader">
											<h6>Seneste released sider</h6>
										</div>

										<table>
											<tr class="link">
												<th>Dato oprettet</th>
												<th>Navn</th>
												<th>Bruger</th>
											</tr>
											<tr class="link">

												<td>Idag - 18:01</td>
												<td>Webshop</td>
												<td>MWA</td>
											</tr>
											<tr class="link">
												<td>19.09.07 - 15:21</td>
												<td>Lorem ipsum</td>

												<td>AMA</td>
											</tr>
											<tr class="link">
												<td>11.10.07 - 20:22</td>
												<td>Lorem regneark</td>
												<td>ALE</td>
											</tr>

										</table>
										<div class="boxItemFooter"></div>
									</div><!--//End mBoxItem-->
									
									<div class="mBoxItem">
										<div class="boxItemHeader">
											<h6>Nyeste brugere</h6>
										</div>
										<table>

											<tr class="link">
												<th>Dato oprettet</th>
												<th>Navn</th>
												<th>Bruger</th>
											</tr>
											<tr class="link">
												<td>Idag - 18:01</td>

												<td>Webshop</td>
												<td>MWA</td>
											</tr>
										</table>
										<div class="boxItemFooter"></div>
									</div><!--//End mBoxItem-->
								
								</div><!--//End pColum1-->
								<div class="pColum2">

									
									<div class="mBoxItem">
										<div class="boxItemHeader">
											<h6>Seneste redigerede sider</h6>
										</div>
										<table>
											<tr>
												<th>Dato oprettet</th>
												<th>Navn</th>

												<th>Bruger</th>
											</tr>
											<tr>
												<td>Idag - 18:01</td>
												<td>Webshop</td>
												<td>MWA</td>
											</tr>

											<tr>
												<td>19.09.07 - 15:21</td>
												<td>Lorem ipsum</td>
												<td>AMA</td>
											</tr>
											<tr>
												<td>11.10.07 - 20:22</td>

												<td>Lorem regneark</td>
												<td>ALE</td>
											</tr>
											<tr>
												<td>19.10.07 - 12:21</td>
												<td>Lorem dolor ipsum</td>
												<td>RPE</td>

											</tr>
											<tr>
												<td>12.09.07 - 12:30</td>
												<td>Lorem ipsum</td>
												<td>MWA</td>
											</tr>
											<tr>

												<td>20.01.07 - 15:11</td>
												<td>Lorem at ipsum</td>
												<td>HJO</td>
											</tr>
											<tr>
												<td>19.10.07 - 12:21</td>
												<td>Lorem dolor ipsum</td>

												<td>RPE</td>
											</tr>
											<tr>
												<td>12.09.07 - 12:30</td>
												<td>Lorem ipsum</td>
												<td>MWA</td>
											</tr>

										</table>
										<div class="boxItemFooter"></div>
									</div><!--//End mBoxItem-->
									
									<div class="mBoxItem">
										<div class="boxItemHeader">
											<h6>Nyeste besøgende</h6>
										</div>
										<table>

											<tr class="link">
												<th>Dato oprettet</th>
												<th>Navn</th>
												<th>Bruger</th>
											</tr>
											<tr class="link">
												<td>Idag - 18:01</td>

												<td>Webshop</td>
												<td>MWA</td>
											</tr>
											<tr class="link">
												<td>19.09.07 - 15:21</td>
												<td>Lorem ipsum</td>
												<td>AMA</td>

											</tr>
											<tr class="link">
												<td>11.10.07 - 20:22</td>
												<td>Lorem regneark</td>
												<td>ALE</td>
											</tr>
											<tr class="link">

												<td>19.10.07 - 12:21</td>
												<td>Lorem dolor ipsum</td>
												<td>RPE</td>
											</tr>
											<tr class="link">
												<td>12.09.07 - 12:30</td>
												<td>Lorem ipsum</td>

												<td>MWA</td>
											</tr>
											<tr class="link">
												<td>20.01.07 - 15:11</td>
												<td>Lorem at ipsum</td>
												<td>HJO</td>
											</tr>

										</table>
										<div class="boxItemFooter"></div>
									</div><!--//End mBoxItem-->
								
								</div><!--//End pColum2-->
								<div class="clr"><!--clearfloat--></div>
							</div><!--//End pTwoColumnsLayout-->
								
							
								<div class="clr"><!--clearfloat--></div>
							</div><!--//End frameContent-->
							<div class="clr"><!--clearfloat--></div>

						</div><!--//End scrollFrame-->
					</div><!--//End innerFrame-->
				</div><!--//End theFrame-->
				<!-- MainTopelementer Start -->
				<div class="topActionButtons">
					<ul class="ul">
						<li class="li"><a href="#" class="buttonType1"><span><span>Gem</span></span></a></li>
						<li class="li"><a href="#" class="buttonType1"><span><span>Gem og publicer</span></span></a></li>

						<li class="li actionMenuPosition">
							<ul class="actionMenu">
								<li><a href="#"><span>Vælg handling</span></a>
									<ul>
										<li><a href="#"><span class="iconType1">Lav kopi</span></a></li>
										<li><a href="#"><span class="iconType2">Konverter side</span></a></li>
										<li><a href="#"><span class="iconType3">Slet</span></a></li>

										<li><a href="#"><span class="iconType4">Søg</span></a></li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="li previewPosition"><a href="#" class="buttonType2"><span><span>Preview</span></span></a></li>
					</ul>
				</div>

				<div class="topTabsMenu">
					<ul>
						<li class="dropDownItem">
							<a href="#">History</a>
							<br class="clr">
							<ul>
								<li><a href="#">4. Afstemning</a></li>
								<li><a href="#">3. Fohandlerliste</a></li>

								<li><a href="#">2. Brugere</a></li>
								<li><a href="#">1. Forside</a></li>
							</ul>
						</li>
						<li><a href="#">Indstillinger</a></li>
						<li><a href="#">Preview</a></li>
						<li class="active"><a href="#">Redigering</a></li>

					</ul>
				</div>
				<!-- //End MainTopelementer -->
			</div><!--//End mainRight-->
			<br class="clr">
		</div><!--//End mainInner-->
		
		
		<!-- MainTopelementer Start -->
		<div class="headerTabs">
			<ul>

				<li><a href="#" class="active">Home</a></li>
				<li><a href="/">Tuksi.com</a>
					<div class="headerTabDropdown">
						<ul>
							<li><a href="standardindholdmodul.htm"><span>Standard indhold modul</span></a></li>
							<li><a href="brugerliste.htm"><span>Liste visning modul</span></a></li>
							<li><a href="statistik.htm"><span>Statistik visning modul</span></a></li>

							<li><a href="kontrolpanel.htm"><span>Kontrolpanel visning/ template (ny bg farve)</span></a></li>
							<li><a href="nyhedsbrevudsending.htm"><span>Nyhedsbrev tilmelding modul</span></a></li>
							<li><a href="login.htm"><span>Login i Tuksi side</span></a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">Nyhedsbrev</a>

					<div class="headerTabDropdown">
						<ul>
							<li><a href="#"><span>Nyhedsbrev 1</span></a></li>
							<li><a href="#"><span>Nyhedsbrev 2</span></a></li>
							<li><a href="#"><span>Meget meget langt nyhedsbrev på 2 linier</span></a></li>
							<li><a href="#"><span>Nyhedsbrev fire </span></a></li>
							<li><a href="#"><span>Nyhedsbrev fem </span></a></li>

							<li><a href="#"><span>Nyhedsbrev seks </span></a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">Afstemning</a>
					<div class="headerTabDropdown">
						<ul>
							<li><a href="#"><span>Afstemning et</span></a></li>

							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning 3</span></a></li>
							<li><a href="#"><span>Afstemning Fire</span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>

							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum dolor long long vote</span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>

							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
							<li><a href="#"><span>Afstemning lorem ipsum </span></a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">Webshop One</a></li>
				<li><a href="#">Intranet</a></li>

			</ul>
		</div><!--//End headerTabs-->
		<!-- //End MainTopelementer -->
	<!-- ** START popup boxe ** -->
		<div id="popupWindow" class="mPopupWindow" style="display:none;">
			<div class="windowHeader">
				<h5>Opret sideskabelon</h5>
				<div class="headerButton"><a href="#" class="buttonTypeX" onclick="$('popupWindow').toggle();"><!--CloseButton--></a></div>

			</div>
			<div class="windowInner">
				<div class="windowInnerPadding">
					<table class="moduleElementRow">
						<tr>
							<td><label>Indtast navn på ny side:</label></td>
						</tr>
						<tr>

							<td><input type="text" class="text" size="60"></td>
						</tr>
					</table>
					<table class="moduleElementRow">
						<tr>
							<td><label>Vælg hvor siden skal placeres:</label></td>
						</tr>
						<tr>

							<td>
								<select>
									<option>Niveau under</option>
									<option>Niveau over</option>
									<option>Et andet niveau</option>
									<option>Et tredje niveau</option>
								</select>

								<select>
									<option>Root</option>
									<option>Root/forside/underside1</option>
									<option>Root/forside/underside2</option>
									<option>Root/forside/underside3</option>
								</select>
							</td>

						</tr>
						<tr>
							<td><input type="checkbox" class="checkbox positionLeft"><label>Medtag undersider</label></td>
						</tr>
					</table>
					<table class="moduleElementRow">
						<tr>
							<td><label>Vælg noget andet her:</label></td>

						</tr>
						<tr>
							<td><input type="radio" class="radio positionLeft"><label>Ja</label></td>
						</tr>
						<tr>
							<td><input type="radio" class="radio positionLeft"><label>Nej</label></td>
						</tr>
					</table>

					<br class="clr">
					<table class="moduleElementRow" align="right">
						<tr>
							<td>
								<a href="#" class="buttonType3 iconNegative" onclick="$('popupWindow').toggle();"><span><span>Fortryd</span></span></a>
							</td>
							<td>
								<a href="#" class="buttonType3 iconPositive" onclick="$('popupAlertBox').toggle();"><span><span>Ok</span></span></a>

							</td>
						</tr>
					</table>
					<br class="clr">
				</div>
			</div>
		</div><!--//End mPopupWindow-->
		
		<div id="popupAlertBox" class="mPopupAlertBox" style="display:none;">
			<div class="alertBoxHeader">

				<h5>Bekræft handling</h5>
				<div class="headerButton"><a href="#" class="buttonTypeX" onclick="$('popupAlertBox').toggle();"><!--CloseButton--></a></div>
			</div>
			<div class="alertBoxInner">
				<div class="alertBoxInnerPadding">
					<strong class="alertMessage">
						Er du sikker på du ønsker at udføre denne handling?
					</strong>
					<ul class="alertButtons">

						<li><a href="#" class="buttonType3 iconNegative" onclick="$('popupAlertBox').toggle();"><span><span>Nej</span></span></a></li>
						<li><a href="#" class="buttonType3 iconPositive" onclick="$('popupAlertBox').toggle(); $('popupWindow').toggle();"><span><span>Ja</span></span></a></li>
					</ul>	
					<br class="clr">
				</div>
			</div>
		</div><!--//End mPopupWindow-->
		
	<!-- ** END popup boxe ** -->

		
	</div><!--//End main-->
	
<br class="clr">	
</div><!--//End #height-->
<script type="text/javascript">
	tuksi.postInit();
</script>	
</body>
</html>
