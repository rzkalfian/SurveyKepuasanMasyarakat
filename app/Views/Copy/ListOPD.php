<?= $this->extend('Templates/Template'); ?>
<?= $this->section('content'); ?>
<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
    <div id="kt_modal_new_target" tabindex="-1" aria-hidden="true">
        <!--begin::Wrapper-->
        <div class="d-flex d-lg-none align-items-center ms-n2 me-2">
            <!--begin::Aside mobile toggle-->
            <div class="btn btn-icon btn-active-icon-primary" id="kt_aside_toggle">
                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
            <!--end::Aside mobile toggle-->
        </div>
        <!--end::Wrapper-->
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content rounded">
                <div class="modal-header pb-0 border-0 justify-content">
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <!--begin::Heading-->
                    <div class="mb-6 text-center">
                        <!--begin::Title-->
                        <h1 class="mb-6">List OPD</h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Heading-->
                    <div class="card-header border-0 pt-5 position-relative mb-6">
                        <!--begin::Search-->
                        <div id="kt_header_search" class="d-flex align-items-center w-75 position-absolute top-0 start-50 translate-middle-x" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-search-responsive="false" data-kt-menu-trigger="auto" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">
                            <!--begin::Form(use d-none d-lg-block classes for responsive search)-->
                            <form data-kt-search-element="form" class="w-100 position-relative mb-5 mb-lg-0" autocomplete="off">
                                <!--begin::Hidden input(Added to disable form autocomplete)-->
                                <input type="hidden" />
                                <!--end::Hidden input-->
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-2 svg-icon-lg-3 svg-icon-gray-800 position-absolute top-50 translate-middle-y ms-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Input-->
                                <input type="text" class="search-input form-control form-control-solid ps-13" name="search" value="" placeholder="Cari" data-kt-search-element="input" id="myFilter" onkeyup="myFunction()" />
                                <!--end::Input-->
                                <!--begin::Spinner-->
                                <span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5" data-kt-search-element="spinner">
                                    <span class="spinner-border h-15px w-15px align-middle text-gray-400"></span>
                                </span>
                                <!--end::Spinner-->
                                <!--begin::Reset-->
                                <span class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4" data-kt-search-element="clear">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                    <span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </span>
                                <!--end::Reset-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin:Form-->
                    <form id="kt_modal_new_target_form" class="form" action="#">
                        <!--begin::Row-->
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9" id="myProducts">
<<<<<<< HEAD
                            <?php
                            $halaman = isset($getHalaman) ? $getHalaman : 1;
                            $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                            $previous = $halaman - 1;
                            $next = $halaman + 1;

                            $data = $listopd;
                            $jumlah_data = count($data);
                            $total_halaman = ceil($jumlah_data / $batas);

                            $conn = mysqli_connect("localhost", "404", "fullo12", "db_survey");
                            $listopd = mysqli_query($conn, "SELECT * FROM ms_opd LIMIT $halaman_awal, $batas");
                            $nomor = $halaman_awal + 1;
                            while ($d = mysqli_fetch_array($listopd)) :
                            ?>
                                <?php foreach ($listopd as $opd) : ?>
                                    <!--begin::Col-->
                                    <div class="col col-md-6 col-lg-4 col-xl-3">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                            <!--begin::Name-->
                                            <a href="<?= site_url('grafik/listtahun/' . $opd['opd_kode']); ?>" class="text-gray-800 text-hover-primary d-flex flex-column">
                                                <!--begin::Image-->
                                                <div class="symbol symbol-75px mb-5">
                                                    <img src="<?= base_url(); ?>/assets/media/svg/files/folder-document.svg" alt="" />
=======
                            <?php 
                            foreach ($listopd as $opd) : ?>
                                <!--begin::Col-->
                                <div class="col col-md-6 col-lg-4 col-xl-3">
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                        <!--begin::Name-->
                                        <a href="<?= site_url('grafik/listtahun/' . $opd['opd_kode']); ?>" class="text-gray-800 text-hover-primary d-flex flex-column">
                                            <!--begin::Image-->
                                            <div class="symbol symbol-75px mb-5">
                                                <img src="<?= base_url(); ?>/assets/media/svg/files/folder-document.svg" alt="" />
                                            </div>
                                            <!--end::Image-->
                                            <!--begin::Title-->
                                            <div class="fs-5 fw-bolder mb-2">
                                                <div class="card-title">
                                                    <?= $opd['opd_nama']; ?>
>>>>>>> b0c5bcbe75558e56f59c5ee5dad315353bda1ef0
                                                </div>
                                            </div>
                                            <!--end::Title-->
                                        </a>
                                        <!--end::Name-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Col-->
                            <?php endforeach; ?>
                        </div>
                        <!--end:Row-->
                        <?php
                        echo $pager->links('ms_opd', 'custom_pagination');
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(''); ?>