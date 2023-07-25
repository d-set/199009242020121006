<?php
/*
// cek login
if ( !isset($_SESSION['log_id']) ) {
	// header("Location: ".BASE_URL."login");
	print '<script> window.location = "'.BASE_URL.'login" </script>';
	die;
}

// notif error
if ( isset( $_SESSION['log_fail'] ) ) {
	print '<div class="container"><div class="alert alert-danger alert-dismissible" role="alert"> 
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	tidak memiliki hak akses ke halaman <strong>' . ucwords( str_replace('-',' ',$_SESSION['log_fail']) ) . '</strong></div></div>';
	unset( $_SESSION['log_fail'] );
}
*/

// create both cURL resources
$ch1 = curl_init();
$ch2 = curl_init();

// set URL and other appropriate options
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_URL, 'http://103.226.55.159/json/data_rekrutmen.json');
curl_setopt($ch2, CURLOPT_URL, 'http://103.226.55.159/json/data_attribut.json');

//create the multiple cURL handle
$mh = curl_multi_init();

//add the two handles
curl_multi_add_handle($mh,$ch1);
curl_multi_add_handle($mh,$ch2);

//execute the multi handle
/* $running = null;
do {
	curl_multi_exec($mh, $running);
} while ($running);
*/
$mrc = curl_multi_exec($mh, $active);
if ($active) {
	curl_multi_select($mh);
}
while ($active && $mrc == CURLM_OK) {
    if (curl_multi_select($mh) == -1) {
        usleep(100);
    }
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
};

// ambil data JSON
$result1 = curl_multi_getcontent($ch1);
$result2 = curl_multi_getcontent($ch2);

//close the handles
curl_multi_remove_handle($mh, $ch1);
curl_multi_remove_handle($mh, $ch2);
curl_multi_close($mh);

$obj_data_rekrutmen = json_decode($result1);
$obj_data_attribut = json_decode($result2);

// html-header and navbar
include "base-header.php";
include "base-nav.php";

// container box for menu-item
?>

<div class="container-fluid fixed-height">
	<div class="panel fixed-height flex-container">
		<div class="panel-heading clearfix">
			<h3 class="pull-left">Daftar Surat Cuti</h3>
		</div>
<?php 
/*
print "<pre>";
print_r($obj_data_attribut);
print "</pre><hr/><pre>";
print_r($obj_data_rekrutmen);
print "</pre>";
*/
?>
		<div class="panel-body table-responsive flex-item sticky-container pt-0 px-0 bg-warning">
		<table class="table table-condensed mb-5">
			<thead class="header-row bg-primary" scope="col">
			<tr>
				<th style="vertical-align: top">No.</th>
				<th style="vertical-align: top">Pendaftaran</th>
				<th style="vertical-align: top">Nama&nbsp;//<br/>NIP</th>
				<th style="vertical-align: top">Satker&nbsp;//<br/>Posisi</th>
				<th style="vertical-align: top">Keahlian</th>
				<th style="vertical-align: top">Tools</th>
				<th style="vertical-align: top">MobileApps</th>
			</tr>
			</thead>
			<tbody>
				<?php
				$no_urut = 0;
				foreach ( $obj_data_rekrutmen as $arr_data ){
				foreach ( $arr_data as $data ){
					$no_urut++;
					print '<tr><a href="'.BASE_URL.$controller.'/'.$data['id'].'">';
					print '<td>'.$no_urut.'</td>';
					print '<td><span class="label label-info">'.$data['timestamp'].'</span></td>';
					print '<td><span class="label label-success">'.$data['nama'].'</span><br/><span class="label label-info">'.$data['nip'].'</span></td>';
					print '<td><span class="label label-success">'.$data['satuan_kerja'].'</span><br/><span class="label label-info">'.$data['posisi_yang_dipilih'].'</span></td>';
					print '<td>bahasa: <span class="label label-success">'.$data['bahasa_pemrograman_yang_dikuasai'].'</span><br/>framework:<span class="label label-info">'.$data['framework_bahasa_pemrograman_yang_dikuasai'].'</span><br/>database:<span class="label label-info">'.$data['database_yang_dikuasai'].'</span></td>';
					print '<td><span class="label label-success">'.$data['tools_yang_dikuasai'].'</span></td>';
					print '<td><span class="label label-success">'.$data['pernah_membuat_mobile_apps'].'</span></td>';
					print '</tr></a>';
				}}
				?>
			</tbody>
		</table>
		</div><!-- panel-body -->
	</div><!-- panel -->
</div><!-- container -->
</body></html>