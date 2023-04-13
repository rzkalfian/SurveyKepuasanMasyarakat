<?= $this->extend('Templates/TemplatePrint'); ?>
<?= $this->section('content'); ?>
<style>
    table {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<script>
    function printgrafik() {
        tes = window.onload = function() {
            window.print()
        }
        setTimeout(tes, 2000)
    }
</script>

<body onload="printgrafik()">
    <!--begin::Wrapper-->
    <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
        <!--begin::Content-->
        <div class="content container-xxl d-flex flex-column flex-column-fluid" id="kt_content">
            <!--begin::Container-->
            <div class="container-xxl" id="kt_content_container">
                <div class="mb-xl-12 mt-12">
                    <?php $i = 1 ?>
                    <?php foreach ($dataGrafiks as $grafik) : ?>
                        <!--begin::Charts-->
                        <div class="mb-xl-12 mt-12">
                            <!--begin::Charts Widget 1-->
                            <div class="card card-xl-stretch mb-xl-12 mt-12">
                                <!--begin::Header-->
                                <div class="card-header border-0 mt-8 mb-4">
                                    <!--begin::Title-->
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-3 mb-1"><?= $i++ . '. ' . $grafik['pertanyaan']['pertanyaan_nama']; ?></span>
                                    </h3>
                                    <!--end::Title-->
                                </div>
                                <!--end::Header-->
                                <!--begin::Body-->
                                <div class="card-body justify-content-center mt-4 mb-8">
                                    <!--begin::Chart-->
                                    <canvas id="myChart<?= $grafik['pertanyaan']['pertanyaan_id'] ?>" id="polar" style="width:100%;max-width:750px"></canvas>
                                    <!-- <div id="myChart" style="width:100%; max-width:600px; height:500px;"></div> -->
                                    <!--end::Chart-->
                                </div>
                                <!--end::Body-->
                            </div>
                        </div>
                        <!--end::Charts Widget 1-->
                    <?php endforeach ?>
                </div>
                <!--end::Row-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Wrapper-->
    <!-- begin::ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <!-- end::ChartJS -->
    <!-- begin::Logic Chart -->
    <script>
        <?php foreach ($dataGrafiks as $grafik) : ?>
            var xValues = [];
            var yValues = [];
            var color = [
                '#04c8c8', //primary
                '#ffc700', //warning
                '#f1416c', //danger
                '#7239ea', //info
                '#fd7e14', //orange
                '#181c32', //dark
                '#009ef7' //success
            ];

            var dynamicColors = function() {
                var r = Math.floor(Math.random() * 255);
                var g = Math.floor(Math.random() * 255);
                var b = Math.floor(Math.random() * 255);
                return "rgb(" + r + "," + g + "," + b + ")";
            };

            <?php foreach ($grafik['total'] as $total) : ?>

                xValues.push("<?= $total['opsi_nama'] ?>");
                yValues.push(<?= $total['total_respon'] ?>);
                color.push(dynamicColors());

            <?php endforeach ?>

            var options = {
                tooltips: {
                    enabled: true
                },
                plugins: {
                    datalabels: {
                        formatter: (value, categories) => {

                            let sum = 0;
                            let dataArr = categories.chart.data.datasets[0].data;
                            dataArr.map(data => {
                                sum += data;
                            });
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                },
            };

            new Chart("myChart<?= $grafik['pertanyaan']['pertanyaan_id'] ?>", {
                type: "pie",
                data: {
                    labels: xValues,
                    datasets: [{
                        data: yValues,
                        backgroundColor: color,
                    }],
                },
                options: options


            })
        <?php endforeach; ?>
    </script>
    <!-- end::Logic Chart -->

    <!-- <script>
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Opsi', 'Respon'],
            ['Italy', 55],
            ['France', 49],
            ['Spain', 44],
            ['USA', 24],
            ['Argentina', 15]
        ]);

        var options = {
            title: 'World Wide Wine Production'
        };

        var chart = new google.visualization.BarChart(document.getElementById('myChart'));
        chart.draw(data, options);
    }
</script> -->

    <!-- begin::Pagination 2 -->
    <script type="text/javascript">
        function getPageList(totalPages, page, maxLength) {
            function range(start, end) {
                return Array.from(Array(end - start + 1), (_, i) => i + start);
            }

            var sideWidth = maxLength < 9 ? 1 : 2;
            var leftWidth = (maxLength - sideWidth * 2 - 3) >> 1;
            var rightWidth = (maxLength - sideWidth * 2 - 3) >> 1;

            if (totalPages <= maxLength) {
                return range(1, totalPages);
            }

            if (page <= maxLength - sideWidth - 1 - rightWidth) {
                return range(1, maxLength - sideWidth - 1).concat(0, range(totalPages - sideWidth + 1, totalPages));
            }

            if (page >= totalPages - sideWidth - 1 - rightWidth) {
                return range(1, sideWidth).concat(0, range(totalPages - sideWidth - 1 - rightWidth - leftWidth,
                    totalPages));
            }

            return range(1, sideWidth).concat(0, range(page - leftWidth, page + rightWidth), 0, range(totalPages -
                sideWidth +
                1, totalPages));
        }

        $(function() {
            var numberOfItem = $(".kartu-konten .kartu").length;
            var limitPerPage = 3; //Banyak item pada satu halaman
            var totalPages = Math.ceil(numberOfItem / limitPerPage);
            var paginationSize = 5; //Banyak element pagination yang terlihat
            var currentPage;

            function showPage(whichPage) {
                if (whichPage < 1 || whichPage > totalPages) return false;

                currentPage = whichPage;

                $(".kartu-konten .kartu").hide().slice((currentPage - 1) * limitPerPage, currentPage * limitPerPage)
                    .show();

                $(".pagination li").slice(1, -1).remove();

                getPageList(totalPages, currentPage, paginationSize).forEach(item => {
                    $("<li>").addClass("page-item").addClass("m-1").addClass(item ? "current-page" : "dots")
                        .toggleClass("active", item === currentPage).append($("<a>").addClass("page-link")
                            .attr({
                                href: "javascript:void(0)"
                            }).text(item || "...")).insertBefore(".next-page");
                });

                $(".previous-page").toggleClass("disabled", currentPage === 1);
                $(".next-page").toggleClass("disabled", currentPage === totalPages);
                return true;
            }

            $(".pagination").append(
                $("<li>").addClass("page-item").addClass("previous-page").addClass("m-1").append($("<a>")
                    .addClass(
                        "page-link").attr({
                        href: "javascript:void(0)"
                    }).append($("<i>").addClass("previous"))),
                $("<li>").addClass("page-item").addClass("next-page").addClass("m-1").append($("<a>").addClass(
                    "page-link").attr({
                    href: "javascript:void(0)"
                }).append($("<i>").addClass("next")))
            );

            $(".kartu-konten").show();
            showPage(1);

            $(document).on("click", ".pagination li.current-page:not(.active)", function() {
                return showPage(+$(this).text());
            });

            $(".next-page").on("click", function() {
                return showPage(currentPage + 1);
            });

            $(".previous-page").on("click", function() {
                return showPage(currentPage - 1);
            });
        });
    </script>
    <!-- end::Pagination 2 -->

    <!-- <script>
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Opsi', 'Respon'],
            ['Italy', 55],
            ['France', 49],
            ['Spain', 44],
            ['USA', 24],
            ['Argentina', 15]
        ]);

        var options = {
            title: 'World Wide Wine Production'
        };

        var chart = new google.visualization.BarChart(document.getElementById('myChart'));
        chart.draw(data, options);
    }
</script> -->
</body>
<?= $this->endSection(''); ?>