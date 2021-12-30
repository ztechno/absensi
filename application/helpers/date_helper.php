<?php

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

function formatTanggal($datetime, $array=false){
    $hari           = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    $bulan          = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    $date_created = explode(' ', $datetime);
    $tanggal = explode('-', $date_created[0]);

    $r_hari = $date_created[0]&&$date_created[0]!="" ? $hari[date("w", strtotime($date_created[0]))]:null;
    $r_tanggal = $date_created[0]&&$date_created[0]!="" ? $tanggal[2]." ".$bulan[(int) $tanggal[1]]." ".$tanggal[0] : null;
    $r_jam = $date_created[1]&&$date_created[1]!="" ? date("H:i", strtotime($date_created[1])) : null;

    if ($array) {
        return array(
            "hari"       => $r_hari,
            "tanggal"    => $r_tanggal,
            "jam"        => $r_jam
        );
    }
    
    return $r_tanggal." <em class='text-info'>".$r_jam."</em>";

}