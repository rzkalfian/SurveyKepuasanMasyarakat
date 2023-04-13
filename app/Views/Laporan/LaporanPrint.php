<?= $this->extend('Templates/TemplatePrint'); ?>
<?= $this->section('content'); ?>
<style>
    table {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<body onload="window.print()">
    <!--begin::Heading-->
    <div class="text-center mt-5">
        <!--begin::Title-->
        <h1 class="mb-3">Laporan</h1>
        <!--end::Title-->
    </div>
    <!--end::Heading-->
    <!--begin::Form-->
    <form id="kt_project_settings_form" class="form">
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!--begin::Row-->
            <div class="row mb-3">
                <div class="col-2 fs-4 fw-bold">Judul</div>
                <div class="col fs-4"><?= $survey['survey_judul']; ?></div>
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-3">
                <div class="col-2 fs-4 fw-bold">Deskripsi</div>
                <div class="col fs-4"><?= $survey['survey_deskripsi']; ?></div>
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-3">
                <div class="col-2 fs-4 fw-bold">Tahun</div>
                <div class="col fs-4"><?= $survey['survey_tahun']; ?></div>
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-3">
                <div class="col-2 fs-4 fw-bold">Status</div>
                <div class="col fs-4"><?= $survey['survey_status']; ?></div>
            </div>
            <!--end::Row-->

        </div>
    </form>
    <!--begin::Body-->

    <div class="card-body pt-3">
        <!--begin::Table container-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle border-table" id="" style="border: 1px solid blackborder; page-break-inside: avoid;">
                <!--begin::Table head-->
                <thead>
                    <tr class="fw-bolder text-muted">
                        <th class="w-10px text-center">No</th>
                        <th class="w-100px text-center">Pertanyaan</th>
                        <th class="w-10px text-center">Opsi</th>
                        <th class="w-20px text-center">Total Respon</th>
                    </tr>
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($totalRespon as $tr) : ?>
                        <tr>
                            <td class="align-middle" rowspan="<?= (count($tr['opsi']) + 1); ?>">
                                <a class="text-dark text-center fw-bolder d-block mb-1 fs-6"><?= $i++; ?></a>
                            </td>
                            <td class="align-middle" rowspan="<?= (count($tr['opsi']) + 1); ?>">
                                <a class="text-dark fw-bolder d-block mb-1 fs-6"><?= $tr['pertanyaan']['pertanyaan_nama']; ?></a>
                            </td>
                            <?php foreach ($tr['opsi'] as $total) : ?>
                        <tr>
                            <td class="align-top text-center">
                                <a class="text-dark fw-bolder d-block mb-1 fs-6"><?= $total['opsi_nama']; ?></a>
                            </td>
                            <td class="align-top text-center">
                                <a class="text-dark fw-bolder d-block mb-1 fs-6"><?= $total['total_respon']; ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table container-->
    </div>
    <!--begin::Body-->
</body>
<?= $this->endSection('content'); ?>