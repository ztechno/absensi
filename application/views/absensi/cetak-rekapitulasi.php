<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi <?=$opd->nama_opd?></title>
    <style>
    body {
        font-size:12px;
    }
    h2 {
        margin:0;
        padding:0;
    }
    </style>
</head>
<body onload="window.print()">
    <h2 align="center">REKAPITULASI DAFTAR HADIR PEGAWAI</h2>
    <h2 align="center"><?=strtoupper($opd->nama_opd)?></h2>
    <p align="center">PERIODE KERJA : <?=formatTanggal($_GET['from'])?> - <?=formatTanggal($_GET['to'])?></p>
    <hr>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">NAMA / NIP</th>
                <th colspan="<?=$diffdays+1?>">KALENDER</th>
                <th rowspan="2">JHK</th>
                <th rowspan="2">H</th>
                <th rowspan="2">POTONGAN</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <?php foreach($daterange as $date){?>
                <th><?=$date->format('d')?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pegawai as $index => $p): ?>
            <tr>
                <td><?=$index+1?></td>
                <td><?=$p->nama?> / <?=$p->nip?></td>
                <?php foreach($daterange as $date){?>
                <td align="center">
                    <?=$pegawai_date[$p->id][$date->format('Y-m-d')][0] != '' ? $pegawai_date[$p->id][$date->format('Y-m-d')][0].'='.$pegawai_date[$p->id][$date->format('Y-m-d')][1] : ''?></td>
                <?php } ?>
                <td><?=$jumlah_hari_kerja?></td>
                <td><?=$pegawai_kerja[$p->id]?></td>
                <td><?=$pegawai_total[$p->id]?>%</td>
                <td><?=100-$pegawai_total[$p->id]?>%</td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <br>
    <p align="center">TANGGAL LIBUR PERIODE KERJA : <?=formatTanggal($_GET['from'])?> - <?=formatTanggal($_GET['to'])?></p>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?php $exists = false; $no=1;foreach($bukan_hari_kerja as $index => $b): if($b == 'Weekend') continue; $exists = true;?>
            <tr>
                <td><?=$no++?></td>
                <td><?=$b?></td>
                <td></td>
            </tr>
            <?php endforeach ?>
            <?php if(!$exists): ?>
            <tr>
                <td colspan="3" style="text-align:center;"><i>Tidak ada libur pada periode ini</i></td>
            </tr>
            <?php endif ?>
        </tbody>
    </table>

    <br><br>
    <table width="300" style="text-align:center;margin-left:auto;">
        <tr>
            <td>Kepala <?=$opd->nama_opd?></td>
        </tr>
        <tr>
            <td>
                <br><br><br><br><br>
            </td>
        </tr>
        <tr>
            <td>
                <b>
                    <?=$kepala->nama?><br>
                    NIP . <?=$kepala->nip?>
                </b>
            </td>
        </tr>
    </table>
</body>
</html>