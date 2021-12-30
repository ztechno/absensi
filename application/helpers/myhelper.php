<?php

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
        
        return $r_hari.", ".$r_tanggal." <em class='text-info'>".$r_jam."</em>";

    }

    function formatUkuranFile($bytes){
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    function ruleAbsensi($pegawai_id, $from, $to)
    {
        // counter
        // get absensi by pegawai_id and hari
        // if record not exists
        //   get izin kerja by pegawai_id and hari
        //   if record not exists
        //     return 3
        //   get izin kerja tidak masuk by pegawai_id and hari
        //   if record exists
        //     return 1
        //   get izin kerja cuti besar by pegawai_id and hari
        //   if record exists
        //     counter + 2.5
        //   get izin kerja cepat pulang by pegawai_id and hari
        //   if record exists
        //     counter + 1
        //   get izin kerja sakit by pegawai_id and hari
        //   if record exists and tanggal_akhir - tanggal_awal > 3
        //     return 0.5
        //   get izin kerja cuti bersalin by pegawai_id and hari
        //   if record exists and tanggal_akhir - tanggal_awal > 3
        //     counter + 2

        //

        // get absensi masuk by pegawai_id and hari
        // if record not exists or jam > 10
        // counter + 3

        // else jam between 9 - 10
        // counter + 2

        // get absensi pulang by pegawai_id and hari
        // if jam ketentuan - jam absensi between 1 and 30 minute
        // counter + 0.5

        // elseif jam ketentuan - jam absensi between 31 and 60 minute
        // counter + 1.5

        // elseif jam ketentuan - jam absensi between 61 and 90 minute
        // counter + 2

        // elseif jam ketentuan - jam absensi between 91 and 120 minute
        // counter + 2.5

        // elseif jam ketentuan - jam absensi > 120 minute
        // counter + 3

        // return counter
    }

    function getAbsensiUpacara($pegawai_id, $from, $to)
    {

    }

    function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }
    