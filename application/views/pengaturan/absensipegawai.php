<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=base_url('pengaturan/buatpengaturanabsensi?token='.$_GET['token']);?>" class="btn btn-sm btn-outline-primary"><em class="ti-plus"></em> BUAT BARU</a>
                <br>
                <br>
                <table class="table table-striped table-bordered table-responsive" id="tablePengaturanAbsensi" cellspacing="0">
                    <thead>
                        <tr>
                            <th valign="middle" rowspan="2">Pengaturan Absensi</th>
                            <th valign="middle" rowspan="2">TMK</th>
                            <th valign="middle" rowspan="2">TAU</th>
                            <th colspan="5" class="text-center">TLP</th>
                            <th colspan="5" class="text-center">ISW</th>
                            <th colspan="5" class="text-center">TLS</th>
                            <th colspan="5" class="text-center">PSW</th>
                            <th valign="middle" rowspan="2" width="30">Aksi</th>
                        </tr>
                        <tr>
                            <th>I</th>
                            <th>II</th>
                            <th>III</th>
                            <th>IV</th>
                            <th>TDHE</th>
                            <th>I</th>
                            <th>II</th>
                            <th>III</th>
                            <th>IV</th>
                            <th>TDHE</th>
                            <th>I</th>
                            <th>II</th>
                            <th>III</th>
                            <th>IV</th>
                            <th>TDHE</th>
                            <th>I</th>
                            <th>II</th>
                            <th>III</th>
                            <th>IV</th>
                            <th>TDHE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?=isset($pengaturanabsensi['nama_pengaturan']) ? $pengaturanabsensi['nama_pengaturan'] : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMK']) ? $pengaturanabsensi['TMK']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TAU']) ? $pengaturanabsensi['TAU']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM1']) ? $pengaturanabsensi['TM1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM2']) ? $pengaturanabsensi['TM2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM3']) ? $pengaturanabsensi['TM3']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM4']) ? $pengaturanabsensi['TM4']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM5']) ? $pengaturanabsensi['TM5']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['ILA1']) ? $pengaturanabsensi['ILA1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['ILA2']) ? $pengaturanabsensi['ILA2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['ILA3']) ? $pengaturanabsensi['ILA3']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['ILA4']) ? $pengaturanabsensi['ILA4']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['ILA5']) ? $pengaturanabsensi['ILA5']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMSI1']) ? $pengaturanabsensi['TMSI1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMSI2']) ? $pengaturanabsensi['TMSI2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMSI3']) ? $pengaturanabsensi['TMSI3']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMSI4']) ? $pengaturanabsensi['TMSI4']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMSI5']) ? $pengaturanabsensi['TMSI5']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA1']) ? $pengaturanabsensi['PLA1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA2']) ? $pengaturanabsensi['PLA2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA3']) ? $pengaturanabsensi['PLA3']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA4']) ? $pengaturanabsensi['PLA4']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA5']) ? $pengaturanabsensi['PLA5']."%" : null;?></td>
                            <td class="text-center">
                                <a href="pengaturan/setpengaturanabsensipegawai?token=<?=$_GET['token'];?>" class="btn btn-sm btn-primary"><em class="ti-settings"></em> SET</a>
                            </td>
                        </tr>
                        <?php foreach($pengaturanabsensis as $pengaturanabsensi):?>
                        <tr>
                            <td><div style="max-width: 400px;white-space: pre-wrap;"><?=$pengaturanabsensi['nama_pengaturan'];?></div></td>
                            <td class="text-center"><?=$pengaturanabsensi['TMK']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TAU']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TM1']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TM2']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TM3']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TM4']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TM5']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['ILA1']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['ILA2']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['ILA3']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['ILA4']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['ILA5']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TMSI1']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TMSI2']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TMSI3']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TMSI4']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['TMSI5']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['PLA1']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['PLA2']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['PLA3']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['PLA4']."%";?></td>
                            <td class="text-center"><?=$pengaturanabsensi['PLA5']."%";?></td>
                            <td class="text-center">
                                <a href="pengaturan/setpengaturanabsensipegawai/<?=$pengaturanabsensi['id'];?>?token=<?=$_GET['token'];?>" class="btn btn-sm btn-primary"><em class="ti-settings"></em> SET</a>
                            </td>
                        </tr>
                        <?php endforeach;?>

                    </tbody>
                 </table>

            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>

