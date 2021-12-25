<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?><span id="subtitle"></span></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group">
                        <label>TMK</label>
                        <input name="nama_pengaturan" type="text" class="form-control" value="<?=set_value('nama_pengaturan') ? set_value('nama_pengaturan') : (isset($pengaturanabsensi['nama_pengaturan']) ? $pengaturanabsensi['nama_pengaturan'] : null);?>" />
                        <?= form_error('nama_pengaturan', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>TMK</label>
                            <div class="input-group">
                                <input name="TMK" type="text" class="form-control" value="<?=set_value('TMK') ? set_value('TMK') : (isset($pengaturanabsensi['TMK']) ? $pengaturanabsensi['TMK'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMK', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>TAU</label>
                            <div class="input-group">
                                <input name="TAU" type="text" class="form-control" value="<?=set_value('TAU') ? set_value('TAU') : (isset($pengaturanabsensi['TAU']) ? $pengaturanabsensi['TAU'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TAU', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">

                        <div class="form-group col-md-2">
                            <label>TLP1</label>
                            <div class="input-group">
                                <input name="TM1" type="text" class="form-control" value="<?=set_value('TM1') ? set_value('TM1') : (isset($pengaturanabsensi['TM1']) ? $pengaturanabsensi['TM1'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM1', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-2">
                            <label>TLP2</label>
                            <div class="input-group">
                                <input name="TM2" type="text" class="form-control" value="<?=set_value('TM2') ? set_value('TM2') : (isset($pengaturanabsensi['TM2']) ? $pengaturanabsensi['TM2'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM2', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-2">
                            <label>TLP3</label>
                            <div class="input-group">
                                <input name="TM3" type="text" class="form-control" value="<?=set_value('TM3') ? set_value('TM3') : (isset($pengaturanabsensi['TM3']) ? $pengaturanabsensi['TM3'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM3', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>TLP4</label>
                            <div class="input-group">
                                <input name="TM4" type="text" class="form-control" value="<?=set_value('TM4') ? set_value('TM4') : (isset($pengaturanabsensi['TM4']) ? $pengaturanabsensi['TM4'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM4', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>TLP5 (TDHE)</label>
                            <div class="input-group">
                                <input name="TM5" type="text" class="form-control" value="<?=set_value('TM5') ? set_value('TM5') : (isset($pengaturanabsensi['TM5']) ? $pengaturanabsensi['TM5'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM5', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">

                        <div class="form-group col-md-2">
                            <label>ISW1</label>
                            <div class="input-group">
                                <input name="ILA1" type="text" class="form-control" value="<?=set_value('ILA1') ? set_value('ILA1') : (isset($pengaturanabsensi['ILA1']) ? $pengaturanabsensi['ILA1'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('ILA1', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-2">
                            <label>ISW2</label>
                            <div class="input-group">
                                <input name="ILA2" type="text" class="form-control" value="<?=set_value('ILA2') ? set_value('ILA2') : (isset($pengaturanabsensi['ILA2']) ? $pengaturanabsensi['ILA2'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('ILA2', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-2">
                            <label>ISW3</label>
                            <div class="input-group">
                                <input name="ILA3" type="text" class="form-control" value="<?=set_value('ILA3') ? set_value('ILA3') : (isset($pengaturanabsensi['ILA3']) ? $pengaturanabsensi['ILA3'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('ILA3', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>ISW4</label>
                            <div class="input-group">
                                <input name="ILA4" type="text" class="form-control" value="<?=set_value('ILA4') ? set_value('ILA4') : (isset($pengaturanabsensi['ILA4']) ? $pengaturanabsensi['ILA4'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('ILA4', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>ISW5 (TDHE)</label>
                            <div class="input-group">
                                <input name="ILA5" type="text" class="form-control" value="<?=set_value('ILA5') ? set_value('ILA5') : (isset($pengaturanabsensi['ILA5']) ? $pengaturanabsensi['ILA5'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('ILA5', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>TLS1</label>
                            <div class="input-group">
                                <input name="TMSI1" type="text" class="form-control" value="<?=set_value('TMSI1') ? set_value('TMSI1') : (isset($pengaturanabsensi['TMSI1']) ? $pengaturanabsensi['TMSI1'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMSI1', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        
                        <div class="form-group col-md-2">
                            <label>TLS2</label>
                            <div class="input-group">
                                <input name="TMSI2" type="text" class="form-control" value="<?=set_value('TMSI2') ? set_value('TMSI2') : (isset($pengaturanabsensi['TMSI2']) ? $pengaturanabsensi['TMSI2'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMSI2', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-2">
                            <label>TLS3</label>
                            <div class="input-group">
                                <input name="TMSI3" type="text" class="form-control" value="<?=set_value('TMSI3') ? set_value('TMSI3') : (isset($pengaturanabsensi['TMSI3']) ? $pengaturanabsensi['TMSI3'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMSI3', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>TLS4</label>
                            <div class="input-group">
                                <input name="TMSI4" type="text" class="form-control" value="<?=set_value('TMSI4') ? set_value('TMSI4') : (isset($pengaturanabsensi['TMSI4']) ? $pengaturanabsensi['TMSI4'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMSI4', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>TLS5 (TDHE)</label>
                            <div class="input-group">
                                <input name="TMSI5" type="text" class="form-control" value="<?=set_value('TMSI5') ? set_value('TMSI5') : (isset($pengaturanabsensi['TMSI5']) ? $pengaturanabsensi['TMSI5'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMSI5', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">

                        <div class="form-group col-md-2">
                            <label>PSW1</label>
                            <div class="input-group">
                                <input name="PLA1" type="text" class="form-control" value="<?=set_value('PLA1') ? set_value('PLA1') : (isset($pengaturanabsensi['PLA1']) ? $pengaturanabsensi['PLA1'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA1', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-2">
                            <label>PSW2</label>
                            <div class="input-group">
                                <input name="PLA2" type="text" class="form-control" value="<?=set_value('PLA2') ? set_value('PLA2') : (isset($pengaturanabsensi['PLA2']) ? $pengaturanabsensi['PLA2'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA2', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-2">
                            <label>PSW3</label>
                            <div class="input-group">
                                <input name="PLA3" type="text" class="form-control" value="<?=set_value('PLA3') ? set_value('PLA3') : (isset($pengaturanabsensi['PLA3']) ? $pengaturanabsensi['PLA3'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA3', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label>PSW4</label>
                            <div class="input-group">
                                <input name="PLA4" type="text" class="form-control" value="<?=set_value('PLA4') ? set_value('PLA4') : (isset($pengaturanabsensi['PLA4']) ? $pengaturanabsensi['PLA4'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA4', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>PSW5 (TDHE)</label>
                            <div class="input-group">
                                <input name="PLA5" type="text" class="form-control" value="<?=set_value('PLA5') ? set_value('PLA5') : (isset($pengaturanabsensi['PLA5']) ? $pengaturanabsensi['PLA5'] : null);?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA5', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                    </div>

                </li>
                <li class="list-group-item">
                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pengaturan/absensipegawai?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>
