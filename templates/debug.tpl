			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td width="120">
							<strong>TreeId</strong>
						</td>
						<td>{$tuksi_debug.info.treeid}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>Errors</strong>
						</td>
						<td>{$tuksi_debug.info.num_errors}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>Warnings</strong>
						</td>
						<td>{$tuksi_debug.info.num_warnings}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>SQL's</strong>
						</td>
						<td>{$tuksi_debug.info.num_sql}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>Time used on SQL</strong>
						</td>
						<td>{math|round:"5" equation="x" x=$tuksi_debug.info.time_sql}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>Template's</strong>
						</td>
						<td>{$tuksi_debug.info.num_tpl}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>Time used on tpl</strong>
						</td>
						<td>{math|round:"5" equation="x" x=$tuksi_debug.info.time_tpl}</td>
					</tr>
					<tr>
						<td width="120">
							<strong>Time used total</strong>
						</td>
						<td>{math|round:"5" equation="x" x=$tuksi_debug.info.time_total}</td>
					</tr>
				</tbody>
			</table>