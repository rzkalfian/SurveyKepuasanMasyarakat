<?= $this->extend('Templates/Template3'); ?>
<?= $this->section('content'); ?>
<div class="container">
    <center>
        <h2><?= $judul; ?></h2>
    </center>
    <div class="data-tables datatable-dark">

        <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0" id="table1">
            <thead>
                <tr>
                    <th>Identitas Survey</th>
                    <th>Diskripsi Survey</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Judul</th>
                    <td><?= $survey['survey_judul']; ?></td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td><?= $survey['survey_deskripsi']; ?></td>
                </tr>
                <tr>
                    <th>Tahun</th>
                    <td><?= $survey['survey_tahun']; ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= $survey['survey_status']; ?></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered" id="mauexport2" width="100%" cellspacing="0" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pertanyaan</th>
                    <th>Opsi</th>
                    <th>Total Respon</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($totalRespon as $tr) : ?>
                <?php foreach ($tr['total'] as $total) : ?>
                <tr>
                    <td>
                        <a class="text-dark text-center fw-bolder d-block mb-1 fs-6"><?= $i++; ?></a>
                    </td>
                    <td>
                        <a class="text-dark fw-bolder d-block mb-1 fs-6"><?= $total['pertanyaan_nama']; ?></a>
                    </td>
                    <td class="text-center">
                        <?= $total['opsi_nama']; ?>
                    </td>
                    <td class="text-center">
                        <a class="text-dark fw-bolder d-block mb-1 fs-6"><?= $total['total_respon']; ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
<?= $this->endSection('content'); ?>