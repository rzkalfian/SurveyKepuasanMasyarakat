<?= $this->extend('Templates/TemplateExport'); ?>
<?= $this->section('content'); ?>
<!--begin::Heading-->
<div class="border-table">
    <!--begin::Title-->
    <h1 class="mb-3">Laporan</h1>
    <!--end::Title-->
</div>
<!--end::Heading-->
<!--begin::table-->
<table class="border-table" id="">
    <!--begin::Table head-->
    <thead>
        <tr>
            <th class="text-center1">Identitas Survey</th>
            <th class="text-center1">Deskripsi Survey</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="text-center1">Judul</th>
            <td><?= $survey['survey_judul']; ?></td>
        </tr>
        <tr>
            <th class="text-center1">Deskripsi</th>
            <td><?= $survey['survey_deskripsi']; ?></td>
        </tr>
        <tr>
            <th class="text-center1">Tahun</th>
            <td><?= $survey['survey_tahun']; ?></td>
        </tr>
        <tr>
            <th class="text-center1">Status</th>
            <td><?= $survey['survey_status']; ?></td>
        </tr>
    </tbody>
    </tbody>
    <!--end::Table body-->
</table>
<br>
<!--begin::table-->
<table class="border-table" id="">
    <!--begin::Table head-->
    <thead>
        <tr>
            <th class="text-center1">No</th>
            <th class="text-center1">Pertanyaan</th>
            <th class="text-center1">Opsi</th>
            <th class="text-center1">Total Respon</th>
        </tr>
    </thead>
    <!--end::Table head-->
    <!--begin::Table body-->
    <tbody>
        <?php $i = 1; ?>
        <?php foreach ($totalRespon as $tr) : ?>
            <tr>
                <td rowspan="<?= (count($tr['opsi']) + 1); ?>">
                    <?= $i++; ?>
                </td>
                <td rowspan="<?= (count($tr['opsi']) + 1); ?>">
                    <?= $tr['pertanyaan']['pertanyaan_nama']; ?>
                </td>
                <?php foreach ($tr['opsi'] as $total) : ?>
            </tr>
            <tr>
                <td>
                    <?= $total['opsi_nama']; ?>
                </td>
                <td>
                    <?= $total['total_respon']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
    <!--end::Table body-->
</table>
<!--end::Table-->
<script src="<?= base_url(); ?>/assets/js/script.js"></script>
<?= $this->endSection(''); ?>