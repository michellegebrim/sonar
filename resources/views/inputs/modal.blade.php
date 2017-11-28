<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sonar | Scraps</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('/bootstrap/dist/css/bootstrap.css') }}" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('/Ionicons/css/ionicons.min.css') }}" type="text/css" />
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" type="text/css" />
    <!-- Colorbox -->
    <link rel="stylesheet" href="{{ asset('/dist/css/colorbox.css') }}" type="text/css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/css/all.css') }}" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper fixed-header">

    <div class="content-wrapper">

        <!-- Main content -->
        <section>
            <div class="row">
                <div class="col-xs-12">

                    <div>
                        <!-- /.box-header -->
                        <div>

                            <!-- MODAL -->
                            <div>
                                <div>
                                    <div class="modal-content">
                                        <div class="modal-header" style="width:98%;">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span></button>
                                            <h4 class="modal-title"><img src="https://www.google.com/s2/favicons?domain=http://{{$loja->url}}" class="favicon-left">{{$loja->pais->pais}} - {{$loja->descricao}} - {{$data}}</h4>
                                        </div>
                                        <div class="modal-body">

                                            <!-- START CUSTOM TABS -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <!-- Custom Tabs -->
                                                    <div class="nav-tabs-custom">

                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tab_homepage">
                                                                <br />
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <!-- tabs up -->
                                                                        <div class="tabs-up">
                                                                            <div class="tab-content">
                                                                                <div class="col-md-2">

                                                                                    <div class="box box-default">
                                                                                        <div class="box-header with-border">
                                                                                            <h3 class="box-title">Screenshots</h3>
                                                                                        </div>
                                                                                        <!-- /.box-header -->
                                                                                        <div class="box-body">
                                                                                            <div class="gal">
                                                                                                @if($screenshots)
                                                                                                    @foreach($screenshots as $value)
                                                                                                        <?php
                                                                                                        $dt = explode(" ", $value->created_at);
                                                                                                        $datapasta = explode("-", $dt[0]);
                                                                                                        $anopasta = $datapasta[0];
                                                                                                        $mespasta = $datapasta[1];


                                                                                                        if($value->device == 'Desktop') {
                                                                                                            $diretorioUrlbox = '/screenshots/'.$pais.'/desktop/'.$anopasta.'/'.$mespasta.'/';
                                                                                                        }  elseif($value->device == 'Mobile') {

                                                                                                            $diretorioUrlbox = '/screenshots/'.$pais.'/mobile/'.$anopasta.'/'.$mespasta.'/';
                                                                                                        }

                                                                                                        ?>

                                                                                                        <a href="javascript:void(0)" id="{{$value->id}}" arquivo="{{$value->arquivo}}" diretorio="{{$diretorioUrlbox}}" data="{{$value->created_at}}" onclick="carregarImagem(this)"><img class="img-responsive active" src="{{$diretorioUrlbox.$value->arquivo}}" alt="Photo"></a>
                                                                                                    @endforeach
                                                                                                 @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- /.box-body -->
                                                                                    </div>

                                                                                </div>
                                                                                <div class="col-md-10">
                                                                                    @if($screenshot)

                                                                                    <div class="box box-default">
                                                                                        <div class="box-header with-border">
                                                                                            <h3 class="box-title">Screenshot {{$loja->descricao}} <span id="data">{{$screenshot->created_at}}</span></h3>
                                                                                        </div>
                                                                                        <!-- /.box-header -->
                                                                                        <div class="box-body">
                                                                                            <div class="margin-bottom">
                                                                                                <?php
                                                                                                $dt = explode(" ", $screenshot->created_at);
                                                                                                $datapasta = explode("-", $dt[0]);
                                                                                                $anopasta = $datapasta[0];
                                                                                                $mespasta = $datapasta[1];


                                                                                                if($value->device == 'Desktop') {
                                                                                                    $diretorioUrlbox = '/screenshots/'.$pais.'/desktop/'.$anopasta.'/'.$mespasta.'/';
                                                                                                }  elseif($value->device == 'Mobile') {

                                                                                                    $diretorioUrlbox = '/screenshots/'.$pais.'/mobile/'.$anopasta.'/'.$mespasta.'/';
                                                                                                }

                                                                                                ?>
                                                                                                <img class="img-responsive" id="carrega_imagem" style="border:1px solid #CCC; -webkit-box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5); -moz-box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5); box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5);" src="{{$diretorioUrlbox.$screenshot->arquivo}}" alt="Photo">
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- /.box-body -->
                                                                                    </div>
                                                                                        @else
                                                                                        <div style="text-align: center">
                                                                                            <h2 style="color: #9e0505">Sem Screenshots para {{$loja->descricao}}</h2>
                                                                                        </div>
                                                                                 @endif

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- /tabs -->
                                                                    </div>
                                                                </div>
                                                                <br />
                                                                <!-- /row -->
                                                            </div>
                                                            <!-- /.tab-pane -->
                                                            <div class="tab-pane" id="tab_screenshots">
                                                                <a href="#" target="_blank"><img class="img-responsive" src="https://iprospectmonitor.com.br/dev/testes/_apagar/screenshot_sonar.jpg" alt="Photo"></a>
                                                            </div>
                                                            <!-- /.tab-pane -->
                                                        </div>
                                                        <!-- /.tab-content -->
                                                    </div>
                                                    <!-- nav-tabs-custom -->
                                                </div>
                                                <!-- /.col -->
                                            </div>
                                            <!-- /.row -->
                                            <!-- END CUSTOM TABS -->

                                        </div>

                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- / MODAL -->


                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->

<script src="{{ asset('/jquery/dist/jquery.js') }}"></script>
<script src="{{ asset('/bootstrap/js/bootstrap.js') }}"></script>
<!-- page script -->
<script type="text/javascript" charset="utf-8">
    jQuery().ready(function() {
        /* Automatically resize to content */
        var y = jQuery(document.body).height();
        var x = jQuery(document).width();
        parent.jQuery.colorbox.resize({innerWidth:x, innerHeight:y});

        jQuery(".close").click(function() {
            parent.jQuery.colorbox.close();
            return false;
        })
    });

    function carregarImagem(elem){
        var id = $(elem).attr("id");
        var data = $(elem).attr("data");
        $('#data').html(data);
        var imagem = $(elem).attr("arquivo");
        var diretorio = $(elem).attr("diretorio");
        var file = diretorio+imagem;
        $("#carrega_imagem").attr('src', file);

    }
</script>

</body>
</html>

