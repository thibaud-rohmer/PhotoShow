<?php
/*
    This file is part of PhotoShow.

    PhotoShow is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PhotoShow is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once realpath(dirname(__FILE__).'/../../src/secu.php');
require_once realpath(dirname(__FILE__).'/../../src/settings.php');
require_once realpath(dirname(__FILE__).'/../../src/layout.php');
require_once realpath(dirname(__FILE__).'/../../src/stats.php');

// If we aren't logged, or aren't an admin, we go back to index.
if(!admin()){
	echo "You aren't supposed to be there.";
	exit();
}
?>


<div class="admin_box">
	<div class="admin_box_title">Stats</div>
	<div class="admin_box_content">
		<table class='admin_table'>
			<tr><td>Accounts</td><td><?php echo count_accounts(); ?></td></tr>
			<tr><td>Groups</td><td><?php echo count_groups(); ?></td></tr>
			<tr><td>Photos</td><td><?php echo count_photos(); ?></td></tr>
		</table>
	</div>
</div>