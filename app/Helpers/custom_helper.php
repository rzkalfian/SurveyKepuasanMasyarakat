<?php
// function_exists('tanggal');
function tanggal($tanggal)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// function setNumber($i)
// {
//     if ($i > 6) {
//         return $i = 1;
//     }
// }

function randomColor($i)
{
    if ($i == 1) {
        return 'primary';
    } elseif ($i == 2) {
        return 'success';
    } elseif ($i == 3) {
        return 'info';
    } elseif ($i == 4) {
        return 'dark';
    } elseif ($i == 5) {
        return 'warning';
    } else {
        return 'danger';
    }
}

function getPertanyaanperHalaman($survey_id, $halaman_awal, $batas)
{
    $db = \Config\Database::connect();
    return
        $db->table('tr_pertanyaan')
        ->where('pertanyaan_survey_id', $survey_id)
        ->limit($batas, $halaman_awal)
        ->get()->getResultArray();
}

function getPertanyaanHalaman2($survey_id, $halaman_awal, $batas)
{
    $conn = mysqli_connect("localhost", "root", "", "db_survey");
    return
        $pertanyaan = mysqli_query($conn, "SELECT * FROM tr_pertanyaan WHERE pertanyaan_survey_id=$survey_id LIMIT $halaman_awal, $batas");
}

function getTotalRespon($pertanyaan_id = null)
{
    $conn = mysqli_connect("localhost", "root", "", "db_survey");
    $query = "SELECT o.opsi_nama as nama_opsi, ifnull(total, 0) as total FROM ms_opsi o
    join tr_pertanyaanopsi on tr_pertanyaanopsi.pertanyaanopsi_opsi_id=o.opsi_id
    left join (select opsi_nama, count(pertanyaanopsi_id) as total from ms_opsi
    join tr_pertanyaanopsi on tr_pertanyaanopsi.pertanyaanopsi_opsi_id=ms_opsi.opsi_id
    join tr_rpoj on tr_rpoj.rpoj_pertanyaanopsi_id=tr_pertanyaanopsi.pertanyaanopsi_id
    where pertanyaanopsi_pertanyaan_id=$pertanyaan_id
    group by pertanyaanopsi_pertanyaan_id, pertanyaanopsi_opsi_id)respon on respon.opsi_nama=o.opsi_nama
    where pertanyaanopsi_pertanyaan_id=$pertanyaan_id";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function strip_only_tags($str, $stripped_tags = null)
{
    // Tidak ada tag yang dihapus
    if ($stripped_tags == null) {
        return $str;
    }
    // Dapatkan daftar tag
    // Misal: <b><i><u> menjadi array('b','i','u')
    $tags = explode('>', str_replace('<', '', $stripped_tags));
    $result = preg_replace('#</?(' . implode('|', $tags) . ').*?>#is', '', $str);
    $endResult = substr($result, 0, 75);
    return $endResult;
}

function strip_only_tags_long($str, $stripped_tags = null)
{
    // Tidak ada tag yang dihapus
    if ($stripped_tags == null) {
        return $str;
    }
    // Dapatkan daftar tag
    // Misal: <b><i><u> menjadi array('b','i','u')
    $tags = explode('>', str_replace('<', '', $stripped_tags));
    $result = preg_replace('#</?(' . implode('|', $tags) . ').*?>#is', '', $str);
    $endResult = substr($result, 0, 175);
    return $endResult;
}
