<!-- widget Statistik -->
<style type="text/css">
  #grafik-judul{
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    padding-bottom: 6px;
  }

  .graph-sub {
    font-family: 'Courier New', monospace;
    font-size: 10px;
    color: #333;
    font-weight: bold;
  }

  #widget-keuangan-container h3{
    font-size: 16px;
    padding-top: 10px;
  }

  #grafik-container{
    overflow-y: auto;
    overflow-x: auto;
    max-height: 500px;
    padding-bottom: 20px;
  }

  .graph-sub{
    text-align: left;
    padding-top: 5px;
    padding-bottom: 5px;
    white-space: nowrap;
    /*height: 100px;*/
    /*overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;*/
  }

  .graph, .graph-sub{
    padding: 0 12px;
    padding-top: 8px;
  }

  .keuangan-selector{
    font-size: 12px;
  }

  span.icon-bar{
    display: block;
    height: 3px;
    margin-bottom: 2px;
    width: 20px;
    background: #333;
    border-radius: 1px;
  }

  .dropdown-toggle{
    border: none;
    background: transparent;
  }

  .keuangan-selector{
    text-align: left;
    padding-left: 0;
  }

  .graph-not-available{
    text-align: center;
    font-family: 'Courier New', monospace;
    font-size: 12px;
  }

  #grafik-tahun{
    font-size: 12px;
    margin-bottom: 10px;
  }
</style>
<div class="box box-info box-solid">
  <div class="box-header">
    <h3 class="box-title"><a href="<?= site_url("first/keuangan/1")?>"><i class="fa fa-bar-chart"></i> Statistik Keuangan Desa</a></h3>
  </div>
  <div class="box-body">
    <div id="widget-keuangan-container">
      <div class="dropdown" style="position: absolute;">
        <button class="dropdown-toggle btn btn-default" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="sr-only">Toogle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-left">
          <?php 
            foreach ($widget_keuangan['tahun'] as $key):
          ?>
          <li><a class="dropdown-item"><b><?= $key ?></b></a></li>
          <li><a class="dropdown-item" onclick="gantiTipe('pelaksanaan'); gantiTahun('<?= $key ?>')">Realisasi Pelaksanaan APBDesa</a></li>
          <li><a class="dropdown-item" onclick="gantiTipe('pendapatan'); gantiTahun('<?= $key ?>')">Realisasi Pendapatan Desa</a></li>
          <li><a class="dropdown-item" onclick="gantiTipe('belanja'); gantiTahun('<?= $key ?>')">Realisasi Belanja Desa</a></li>
          <?php 
            endforeach;
          ?>
        </ul>
      </div>
      <div id="grafik-judul" style="width: 100%">
        <h3></h3>
        <p id="grafik-tahun"></p>
      </div>
      <div id="grafik-container">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var rawData = <?= $widget_keuangan['data']; ?>;

  var year = "<?= $widget_keuangan['tahun_terbaru'] ?>";
  var type = "pelaksanaan"

  function displayChart(tahun, tipe){
    resetContainer();
    switch(tipe){
      case "pelaksanaan":
        var judulGrafik = 'Pelaksanaan APBDesa';
        var tipeGrafik = 'res_pelaksanaan';
        break;

      case "belanja":
        var judulGrafik = 'Belanja Desa';
        var tipeGrafik = 'res_belanja';
        break;

      case "pendapatan":
        var judulGrafik = 'Pendapatan Desa';
        var tipeGrafik = 'res_pendapatan';
        break;
    }
    var chartData = rawData[tahun][tipeGrafik];
    $("#widget-keuangan-container h3").text(judulGrafik);
    //Eksekusi chart dengan for loop
    chartData.forEach(function(subData, idx){
      if(subData['nama']){
        if((!subData['realisasi'] && !subData['anggaran'])){
          $("#grafik-container").append(
              "<div class='graph-sub' id='graph-sub-"+ idx +"'>"+ subData['nama'] + "</div><div id='graph-"+ idx +"' class='graph-not-available'>Data tidak tersedia.</div>");
        }else{
          var persentase = parseInt(subData['realisasi']) / (parseInt(subData['realisasi']) + parseInt(subData['anggaran'])) * 100;
          if(isNaN(persentase)){
            persentase = 0;
          }
          persentase = persentase.toFixed(2);
          $("#grafik-container").append(
              "<div class='graph-sub' id='graph-sub-"+ idx +"'>"+ subData['nama'] + "</div><div id='graph-"+ idx +"' class='graph'></div>");
          Highcharts.chart("graph-"+ idx, {
              chart: {
                type: 'bar',
                margin: 0,
                height: 30,
                backgroundColor: "rgba(0,0,0,0)",
                spacingBottom: 0,
              },

              title: {
                text: ''
              },

              subtitle: {
                y: -2,
                style: {"color" : "#000"},
                text: '',
              },

              xAxis: {
                visible: false,
                categories: [''],
              },
              
              tooltip: {
                valueSuffix: ''
              },
              
              plotOptions: {
                bar: {
                  dataLabels: {
                    enabled: true
                  }
                },

                series: {
                  pointPadding: 0,
                  groupPadding: 0,
                  dataLabels: {
                    align: 'left',
                    inside: true,
                    shadow: false,
                    color: '#000',
                  },
                },
              },
              
              credits: {
                enabled: false
              },
              
              yAxis: {
                visible: false
              },
              
              exporting: {
                enabled: false
              },
              
              legend: {
                enabled: false
              },
              
              series: [{
                name: 'Anggaran',
                color: '#2E8B57',
                data: [parseInt(subData['anggaran'])],
                dataLabels: {
                  style: {"textOutline": "1px contrast"},
                  color: "#ffffff",
                },
              }, {
                name: 'Realisasi',
                color: '#FFD700',
                dataLabels: {
                  formatter: function(){
                    if(parseInt(subData['realisasi']) > parseInt(subData['anggaran'])){
                      return parseInt(subData['realisasi']);
                    }else{
                      return parseInt(subData['realisasi']) + " (Realisasi : " + persentase + "%)";
                    }
                  },
                  style: {"textOutline": "none",}
                },
                data: [parseInt(subData['realisasi'])],
              }]
          });
        }
      }
    });
    $("p#grafik-tahun").text("Tahun " + year);
  }

  function resetContainer(){
    $("#grafik-container").html("");
  }

  function gantiTahun(newThn){
    year = newThn;
    displayChart(year, type);
  }

  function gantiTipe(newType){
    type = newType;
    displayChart(year, type);
  }

  $("#keuangan-selector").change(function(){
    gantiTahun($("#keuangan-selector").val());
  })

	$(document).ready(function (){
    //Realisasi Pelaksanaan APBD
    $("#keuangan-selector").val("<?= $widget_keuangan['tahun_terbaru']?>")
    displayChart(year, type);
	});
</script>
<!-- Highcharts -->
<script src="<?= base_url()?>assets/js/highcharts/highcharts.js"></script>
<script src="<?= base_url()?>assets/js/highcharts/exporting.js"></script>
<script src="<?= base_url()?>assets/js/highcharts/highcharts-more.js"></script>
