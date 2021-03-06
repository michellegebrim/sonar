@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
    <?php
        if(!empty($sessionBusca['data'])) {

            $dt  = explode ("-", $sessionBusca['data']);
            $d1 = explode ('/', $dt[0]);
            $mesi = intval($d1[0]);
            $diai = intval($d1[1]);
            $anoi = intval($d1[2]);

            $d2 = explode ('/', $dt[1]);
            $mesf = intval($d2[0]);
            $diaf = intval($d2[1]);
            $anof = intval($d2[2]);

            $dataInicial = mktime(0, 0, 0, $mesi, $diai, $anoi);
            $dataFinal = mktime(0, 0, 0, $mesf, $diaf, $anof);

            $diferenca = $dataFinal - $dataInicial;
            $dias = (int)floor( $diferenca / (60 * 60 * 24));

        } else {
            $dias = 29;
        }
    ?>


<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="row">
	<div class="col-md-12">
	  <ol class="breadcrumb hover" id="filtro_select">
		<li>{{$sessionBusca['nomePais']}}</li>
		<li>{{$sessionBusca['nomeLoja']}}</li>
		<li class="active">{{$sessionBusca['data']}}</li>
		<span class="right" style="float:right;">
			<i class="fa fa-filter"></i> Filter
		</span>
	  </ol>
	</div>
  </div>
</section>
  
<section class="content">
    <div class="box box-default" id="filtro">
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <form method="POST" action="/search">
                    {{ csrf_field() }}
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::select('pais_id',$paises,null, ['placeholder' => 'All Countries', 'id'=>'country', 'class'=>'form-control countries select2'])!!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- /.form-group -->
                        <div class="form-group">
                            <select name="loja_id" id="loja_id" class="form-control countries select2" style="width:300px">
                                <option value="all">All Retailers</option>
                            </select>
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <!-- /.form-group -->
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="form-control pull-right" name="data" id="period" type="text">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-1">
                        <!-- /.form-group -->
                        <div class="form-group">
                            <div class="input-group btn-block">
                                <button type="submit" class="btn btn-default btn-block" value="submit">OK</button>
                            </div>
                            <!-- /.input group -->
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                </form>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    <div class="row">
        <div class="col-md-12">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title">Progress: <strong id="progressoID1">{{$progresso}}%</strong></h3>
                    </div>
                    <div class="col-md-6">
                        <h3 class="box-title pull-right">
                            <strong>
                                <select class="form-control select2" name="scrap_id" id="scrap_id" tabindex="1">
                                   @if(!empty($scraps))
                                        <?php $i=1;?>
                                        @foreach($scraps as $value)
                                            <option value="{{ $value->id }}">{{$i}}(#{{ $value->id }})</option>
                                                <?php $i++;?>
                                        @endforeach
                                   @endif
                                </select>
                            </strong>
                            <strong>&nbsp; <?php if(!empty($totalScrapsAnalise)){ echo $totalScrapsAnalise; }?> </strong> records
                        </h3>
                    </div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: {{$progresso}}%"></div>
            </div>
        </div>
    </div>
    @if(session()->has('status'))
        <div class="alert alert-danger">
            {{session()->get('status')}}
        </div>
    @endif


    <!-- /.row -->
    @if($scrap)

        @if ($scrap->pais->pais == 'Brasil')
            <?php  $icon = "br";?>
        @elseif ($scrap->pais->pais == 'Argentina')
            <?php $icon = "ar";?>
        @elseif ($scrap->pais->pais == 'México')
            <?php $icon = "mx";?>
        @elseif ($scrap->pais->pais == 'Peru')
            <?php $icon = "pe";?>
        @elseif ($scrap->pais->pais == 'Chile')
            <?php $icon = "cl";?>
        @elseif ($scrap->pais->pais == 'Colômbia')
            <?php $icon = "co";?>
        @elseif ($scrap->pais->pais == 'Sela')
            <?php $icon = "pa";?>
        @endif

    @else
        <?php  $icon = "br";?>
    @endif

    <?php
        $countProgress = 0;
        $array[] = $scrap;
        foreach( $array as $value){
          $dados = explode(":", $value);
           foreach ($dados as $dado){
              if(!empty($dado)) {
                  $countProgress++;
              }
           }

        }
    ?>

    <?php if($scrap) {?>

    <div class="box box-solid" id="panel-fullscreen">
        {!! Form::open(['id' => 'formRegister', 'method' => 'post']) !!}
        <input type="hidden" name="id" id="id" value="@if($scrap){{ $scrap->id }} @endif" >
        <input type="hidden" name="usuario_id" value="{{Auth::user()->id}}" >
        <input type="hidden" name="paisid" id="paisid" value="@if($scrap){{ $scrap->pais_id }} @endif" >
        <input type="hidden" name="lojaid" id="lojaid" value="@if($scrap){{ $scrap->loja_id }} @endif" >
        <input type="hidden" name="statusId" id="statusId" value="@if($scrap){{ $scrap->status }} @endif" >
        <div class="box-header with-border">
				<div class="row">
					<div class="col-md-2">
						<div class="btn-group">
							<button type="button" value="{{$scrap->id-1}}" class="btn btn-default btn-flat" id="scrapIdPaginacao1" disabled><i class="fa fa-angle-left"></i></button>
							<button type="button" value="{{$scrap->id}}" style="padding-left:3px;padding-right:3px;" class="btn btn-default btn-flat disabled" id="scrapIdPaginacao">#{{$scrap->id}}</button>
							<button type="button" value="{{$scrap->id+1}}" class="btn btn-default btn-flat" id="scrapIdPaginacao2"><i class="fa fa-angle-right"></i></button>
							<br /><small style="font-size:12px;"><div id="datescrap">{{$scrap->created_at}}</div></small>
						</div>
					</div>
					<div class="col-md-4" style="text-align:left;">
						<div id="mensagemSalvo" style="display: none">
                            <h3><i class="fa fa-fw fa-save text-green"></i> Este Registro já está salvo.</h3>
                        </div>

                        <div id="mensagemDescarte" style="display: none">
                            <h3><i class="fa fa-fw fa-remove text-red"></i> Este registro já foi descartado.</h3>
                        </div>


					</div>

					<div class="col-md-4">
						<span id="progress-bar2" style="display:none;">
						<p>Progress: <strong id="progressoID">{{$progresso}}%</strong></p>
						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{$progresso}}%"></div>
						</div>
						</span>
					</div>
					<div class="col-md-2">
						<div class="box-tools pull-right">
							<button type="button" style="z-index: 9999" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							&nbsp;
							<button type="button" style="z-index: 9999" class="btn btn-box-tool" id="panel-actions" title="Fullscreen"><i class="glyphicon glyphicon-resize-full"></i></button>
						</div>
					</div>
				</div>
        </div>
        <div class="box-body">

            <div class="col-md-2">

                <div class="row">

                    <!-- /.col -->
                    <div class="col-md-12">
                        <a href="#" data-toggle="modal" data-target="#modal-ver" data-backdrop="static" data-keyboard="false" target="_blank"><img class="img-responsive img-popover" src="{{$scrap->arquivo}}" alt="" id="imagem" ></a>
                        <p><a href="{{$scrap->target}}" target="_blank" id="text_target">{{$scrap->produto}} <small class="fa fa-fw fa-external-link"></small></a></p>
                        <br />
                        <div class="bg-gray-light box collapsed-box box-solid"><div class="box-header with-border"><a href="#" class="colorbox"><i class="fa fa-fw fa-file"></i> Screenshots</a></div></div>
                        <div class="bg-gray-light box collapsed-box box-solid">
                            <div class="box-header with-border">
                                <span class="flag-icon flag-icon-left flag-icon-{{$icon}}"></span><a href="javascript:void(0);" id="pais_id">{{$scrap->pais->pais}}</a>
                                <i class="fa fa-fw fa-angle-right text-gray"></i>
                                <span><img src="https://www.google.com/s2/favicons?domain=http://{{$scrap->loja->url}}" class="favicon-left" id="lojaUrl"><a href="javascript:void(0);" id="lojaid">{{$scrap->loja->descricao}}</a></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-10">

                    <fieldset>
                        <legend>Page:</legend>
                        <div class="row">
                            <div class="col-md-3">
                                <!-- /.form-group -->
                                <div class="form-group">
                                    <label>Device</label>
                                    <select class="form-control" name="device" id="device" style="width: 100%;" tabindex="1">
                                        <option value="Mobile" <?php if($scrap->device=='Mobile'){ echo 'selected'; }?>>Mobile</option>
                                        <option value="Desktop" <?php if($scrap->device=='Desktop'){ echo 'selected'; }?>>Desktop</option>
                                    </select>
                                </div>
                                <!-- /.form-group -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Page Type</label>
                                    <select class="form-control" id="tipo_pagina" name="tipo_pagina" style="width: 100%;" tabindex="2">
                                        {{--  @if($scrap->tipo_pagina==0) <option value=0  selected>Other</option> @endif--}}
                                        @foreach($tipoPagina as $value)
                                            <option value="{{$value->id}}" <?php if($scrap->tipo_pagina_id==$value->id){ echo 'selected';}?>>{{$value->tipo_pagina}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-3">
                                <!-- /.form-group -->
                                <div class="form-group">
                                    <label>Place</label>
                                    <select  name="tipo_anuncio" id="tipo_anuncio" class="form-control" style="width: 100%;" tabindex="3">
                                        @foreach($place as $value) {
                                        <option value="{{$value->id}}" <?php if($scrap->place==$value->id){ echo 'selected';}?>>{{$value->tipo_pagina}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- /.form-group -->
                            </div>

                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Offer:</legend>
                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" style="width: 100%;" tabindex="4" id="type" name="type">
                                        @foreach($getPageType as $value)
                                            <option value="{{$value->id}}"  @if($scrap){{$scrap->type==$value->id ? 'selected':'selected' }} @endif>{{$value->tipo_pagina}}</option>
                                        @endforeach
                                    </select>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 offer-ad">
                                <div class="form-group">
                                    <label>Ad Type</label>
                                    <select class="form-control" name="adType" id="adType" style="width: 100%;" tabindex="5">
                                        @if($scrap->tipo_anuncio_id==0) <option value=0  selected>Other</option> @endif
                                        @foreach($tipoAnuncio as $value) {
                                        <option value="{{$value->id}}" <?php if($scrap->tipo_anuncio_id==$value->id){ echo 'selected';}?>>{{$value->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- /.col -->
                            <div class="col-md-2 offer-ad">
                                <div class="form-group">
                                    <label>Ad Type Detail</label>
                                    <input class="form-control" name="ad_type_detail" id="adtypedetail" placeholder=" " type="text" value="@if($scrap){{$scrap->detalhe_tipo_anuncio}}@endif" tabindex="6">
                                </div>
                            </div>
                            <!-- /.col -->

                            <div class="col-md-2 offer-organic">
                                <!-- /.form-group -->
                                <div class="form-group">
                                    <label>Position</label>
                                    <select class="form-control" name="position" id="position" style="width: 100%;" tabindex="7">
                                        <?php for($i=1; $i<=50; $i++){ ?>
                                        <option <?php if($scrap->position==$i){ echo 'selected';}?>>{{$i}}<option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <!-- /.form-group -->
                            </div>
                            <!-- /.col -->

                            <div class="col-md-2 offer-organic">
                                <div class="input-group">
                                    <label>Price</label>
                                    <div class="input-group">
                                        <input class="form-control" name="preco" id="preco" placeholder=" " type="text" value="@if($scrap){{$scrap->preco}}@endif" tabindex="8">
                                    </div>
                                </div>
                            </div>

                            <!-- /.col -->
                            <div class="col-md-2 offer-organic">
                                <div class="form-group">
                                    <label>Price From</label>
                                    <div class="input-group">
                                        <input class="form-control"name="price_from" id="price_from" placeholder=" " type="text" value="@if($scrap){{$scrap->price_from}}@endif" tabindex="9">
                                    </div>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-2 offer-organic">
                                <div class="form-group">
                                    <label>Price Install</label>
                                    <input class="form-control" name="price_install" id="price_install" placeholder=" " type="text" value="@if($scrap){{$scrap->price_install}}@endif" tabindex="10">
                                </div>
                            </div>

                            <!-- /.col -->
                            <div class="col-md-2 offer-organic">
                                <div class="form-group">
                                    <label>Call to Action</label>
                                    <input class="form-control" id="cta" name="call_action" placeholder=" " type="text" value="@if($scrap){{$scrap->detalhe}}@endif" tabindex="11">
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Product:</legend>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select class="form-control select2" name="produto" id="product" style="width: 100%;" tabindex="12">
                                        @foreach($produtos as $value)
                                            <option value="{{ $value->id }}" <?php if($scrap->produto_id==$value->id){ echo 'selected';}?>>{{$value->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- /.form-group -->
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="categoria_id" id="categoria_id" class="form-control" style="width: 100%;" tabindex="13">
                                        @foreach($categorias as $value) {
                                        <option value="{{ $value->id }}" <?php if($scrap->categoria_id==$value->id){ echo 'selected'; }?>>{{$value->id}} - {{$value->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- /.form-group -->
                            </div>

                            <!-- /.col -->
                            <div class="col-md-4">
                                <!-- /.form-group -->
                                <div class="form-group">
                                    <label>Brand</label>
                                    <select class="form-control" name="marca_id" id="marca_id"  style="width: 100%;" tabindex="14">
                                        @if($scrap->marca_id==0) <option value=0  selected>0 - Other</option> @endif
                                        @foreach($marcas as $value)
                                            <option value="{{ $value->id }}" <?php if($scrap->marca_id==$value->id){ echo 'selected';}?>>{{ $value->id }} - {{$value->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- /.form-group -->
                            </div>
                            <!-- /.col -->
                        </div>

                        <div class="row">
                            <!-- /.col -->
                            <div class="col-md-0" style="display:none;">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input class="form-control" id="title" name="titulo" placeholder="Título do produto" type="text" value="@if($scrap){{$scrap->titulo}}@endif" tabindex="15">
                                </div>
                            </div>
                            <!-- /.col -->

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Detail</label>
                                    <input class="form-control" id="detail" name="detalhe" placeholder="Descrição mais detalhada do produto" type="text" value="@if($scrap){{$scrap->detalhe}}@endif" tabindex="16">
                                </div>
                            </div>

                        </div>
                    </fieldset>

				<div class="row">
					<div class="col-md-12">&nbsp;</div>
					<!-- /.col -->
				</div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group pull-right btn-block">
                            <button class="btn btn-default btn-lg btn-block" name="" id="pularBotao" value="descartar" tabindex="17" >Pular</button>
                        </div>
                    </div>

                    <!-- /.col -->
                    <div class="col-md-2">
                        <div class="form-group pull-right btn-block">
                            <button class="btn btn-danger btn-lg btn-block" name="descartar" id="descartar" value="descartar" tabindex="18" >Descartar</button>
                        </div>
                    </div>
                    <!-- /.col -->

                    <!-- /.col -->
                    <div class="col-md-2">
                        <div class="form-group pull-right btn-block">
                            <button class="btn btn-success btn-lg btn-block" name="submit" id="submit" value="enviar" tabindex="19" type="submit">Salvar</button>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

            </div>
            <!-- /.col -->
        </div>
        <!-- /.box-body -->

    </div>
    <!-- /.box -->
    <?php } ?>

</section>
<!-- /.content -->

@section('pagescript')
    <script src="{{ asset('/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/plugins/select2.full.min.js')}}"></script>
    <script src="{{ asset('/plugins/inputmask/dist/inputmask/inputmask.js')}}"></script>
    <script src="{{ asset('/plugins/inputmask/dist/inputmask/inputmask.date.extensions.js')}}"></script>
    <script src="{{ asset('/plugins/inputmask/dist/inputmask/inputmask.extensions.js')}}"></script>
    <script src="{{ asset('/plugins/moment/min/moment.min.js')}}"></script>
    <script src="{{ asset('/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ asset('/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{ asset('/jquery.validate.js')}}"></script>

    <script>
	$(document).ready(function(){

		//FOCO AUTOMÁTICO NO 1º CAMPO
		$('#device').focus()
		//$("form:not(.filter) :input:visible:enabled:first").focus();


        $('#scrapIdPaginacao1').click(function () {

            var scrap_id =  $("#scrapIdPaginacao").val()-1;
            var validaprev = ('#select2-scrap_id-container').text();
            alert(validaprev);
            $("#scrapIdPaginacao").val(scrap_id);
            var dataPesq = $('#period').val();
            var id =  $("input[name='id']");
            var target  = $("input[name='target']");
            var produto = $("input[name='produto']");
            var titulo = $("input[name='titulo']");
            var detalhe = $("input[name='detalhe']");
            var call_action = $("input[name='call_action']");
            var price_install = $("input[name='price_install']");
            var price_from = $("input[name='price_from']");
            var preco = $("input[name='preco']");

            $.ajax({
                type: "POST",
                url: "inputs",
                data: {"scrap_id" : scrap_id, "dataPesq" : dataPesq},
                processData: true,
                dateType:'json',
                cache: false,
                success: function (data) {
                    $("#id").val("");
                    $("#imagem").attr('src', "");
                    $("#lojaUrl").attr('src', "");
                    $("#device").val("");
                    $("#position").val("");
                    $("#tipo_pagina").val("");
                    $("#tipo_anuncio").val("");
                    $("#categoria_id").val("");
                    $("#marca_id").val("");
                    $("#adType").val("");
                    $("#ad_type_detail").val("");
                    $("#datescrap").html("");
                    $("#pais_id").html("");
                    $("#text_target").html("");
                    titulo.val("");
                    target.val("");
                    produto.val("");
                    detalhe.val("");
                    call_action.val("");
                    call_action.val("");
                    preco.val("");
                    price_from.val("");
                    price_install.val("");
                    $("#lojaUrl").attr('src', "");
                    $("#progressoID").html("");
                    $("#progressoID1").html("");



                    var dados =  JSON.parse(data);


                    $("#id").val(dados.id);
                    $("#imagem").attr('src', dados.arquivo);
                    $("#device").val(dados.device);
                    $("#position").val(dados.position);
                    $("#tipo_pagina").val(dados.tipo_pagina_id);
                    $("#tipo_anuncio").val(dados.tipo_anuncio);
                    $("#categoria_id").val(dados.categoria_id);
                    $("#marca_id").val(dados.marca_id);
                    $("#adType").val(dados.ad_type);
                    $("#ad_type_detail").val(dados.ad_type_detail);
                    titulo.val(dados.titulo);
                    target.val(dados.target);
                    produto.val(dados.produto);
                    $("#text_target").html(dados.produto);
                    detalhe.val(dados.detalhe);
                    call_action.val("");
                    call_action.val(dados.call_action);
                    preco.val(dados.preco);
                    price_from.val(dados.price_from);
                    price_install.val(dados.price_install);
                    datescrap.append(dados.updated_at);
                    $("#pais_id").html(dados.pais_id);
                    $("#lojaid").html(dados.loja_id);
                    $("#lojaUrl").attr('src', dados.url);
                    $("#scrapIdPaginacao").html(dados.id);
                    $("#progressoID").html(dados.progresso);
                    $("#progressoID1").html(dados.progresso+"%");
                    $("#statusId").val(dados.status);
                    $("#text_target").attr("href", dados.target);

                    var status = $("#statusId").val();

                     if( status == 1)
                     {
                        $('#mensagemSalvo').css('display', '');
                        $('#mensagemDescarte').css('display', 'none');
                     } else if ( status == 5) {

                         $('#mensagemSalvo').css('display', 'none');
                         $('#mensagemDescarte').css('display', '');
                     } else  {
                         $('#mensagemSalvo').css('display', 'none');
                         $('#mensagemDescarte').css('display', 'none');
                     }


                    $('.form-control').each(function(e,i){
                        if(this.value.trim() === ""){
                            $(this).parent().addClass('has-warning');
                        } else {
                            $(this).parent().removeClass('has-warning');
                        }
                    });
                }

            });
        });

        $('#scrapIdPaginacao2').click(function () {

            var scrap_id =  parseInt($("#scrapIdPaginacao").val()) + parseInt(1);
            var validaprev = $('#scrap_id').val();


            $("#scrapIdPaginacao").val(scrap_id);
            var dataPesq = $('#period').val();
            var id =  $("input[name='id']");
            var target  = $("input[name='target']");
            var produto = $("input[name='produto']");
            var titulo = $("input[name='titulo']");
            var detalhe = $("input[name='detalhe']");
            var call_action = $("input[name='call_action']");
            var price_install = $("input[name='price_install']");
            var price_from = $("input[name='price_from']");
            var preco = $("input[name='preco']");

            $.ajax({
                type: "POST",
                url: "inputs",
                data: {"scrap_id" : scrap_id, "dataPesq" : dataPesq},
                processData: true,
                dateType:'json',
                cache: false,
                success: function (data) {
                    $("#id").val("");
                    $("#imagem").attr('src', "");
                    $("#lojaUrl").attr('src', "");
                    $("#device").val("");
                    $("#position").val("");
                    $("#tipo_pagina").val("");
                    $("#tipo_anuncio").val("");
                    $("#categoria_id").val("");
                    $("#marca_id").val("");
                    $("#adType").val("");
                    $("#ad_type_detail").val("");
                    $("#text_target").html("");
                    $("#progressoID").html("");
                    $("#progressoID1").html("");
                    $("#datescrap").html("");
                    $("#pais_id").html("");
                    $('#scrap_id').val("");
                    titulo.val("");
                    target.val("");
                    produto.val("");
                    detalhe.val("");
                    call_action.val("");
                    call_action.val("");
                    preco.val("");
                    price_from.val("");
                    price_install.val("");
                    $("#lojaUrl").attr('src', "");


                    var dados =  JSON.parse(data);


                    $("#id").val(dados.id);
                    $("#imagem").attr('src', dados.arquivo);
                    $("#device").val(dados.device);
                    $("#position").val(dados.position);
                    $("#tipo_pagina").val(dados.tipo_pagina_id);
                    $("#tipo_anuncio").val(dados.tipo_anuncio);
                    $("#categoria_id").val(dados.categoria_id);
                    $("#marca_id").val(dados.marca_id);
                    $("#adType").val(dados.ad_type);
                    $("#ad_type_detail").val(dados.ad_type_detail);
                    titulo.val(dados.titulo);
                    target.val(dados.target);
                    produto.val(dados.produto);
                    detalhe.val(dados.detalhe);
                    call_action.val("");
                    call_action.val(dados.call_action);
                    preco.val(dados.preco);
                    price_from.val(dados.price_from);
                    price_install.val(dados.price_install);
                    datescrap.append(dados.updated_at);
                    $("#pais_id").html(dados.pais_id);
                    $("#lojaid").html(dados.loja_id);
                    $("#lojaUrl").attr('src', dados.url);
                    $("#scrapIdPaginacao").html(dados.id);
                    $("#text_target").html(dados.produto);
                    $("#progressoID").html(dados.progresso);
                    $("#text_target").attr("href", dados.target);
                    $("#progressoID1").html(dados.progresso+"%");
                    $('#scrap_id').val(dados.id);
                    $('#select2-scrap_id-container').html(dados.id);
                    var validaprev = parseInt($('#scrap_id').val()) + parseInt(1);


                     if($('#scrap_id').val() == null) {
                         $('#scrapIdPaginacao2').attr("disabled", true);
                     } else {
                         $('#scrapIdPaginacao2').attr("disabled", false);

                     }


                    if( status == 1)
                    {
                        $('#mensagemSalvo').css('display', '');
                        $('#mensagemDescarte').css('display', 'none');
                    } else if ( status == 5) {

                        $('#mensagemSalvo').css('display', 'none');
                        $('#mensagemDescarte').css('display', '');
                    } else  {
                        $('#mensagemSalvo').css('display', 'none');
                        $('#mensagemDescarte').css('display', 'none');
                    }


                    $('.form-control').each(function(e,i){
                        if(this.value.trim() === ""){
                            $(this).parent().addClass('has-warning');
                        } else {
                            $(this).parent().removeClass('has-warning');
                        }
                    });
                }

            });
        });


        $('#pularBotao').click(function () {

            var scrap_id =  parseInt($("#scrapIdPaginacao").val()) + parseInt(1);
            $("#scrapIdPaginacao").val(scrap_id);
            var dataPesq = $('#period').val();
            var id =  $("input[name='id']");
            var target  = $("input[name='target']");
            var produto = $("input[name='produto']");
            var titulo = $("input[name='titulo']");
            var detalhe = $("input[name='detalhe']");
            var call_action = $("input[name='call_action']");
            var price_install = $("input[name='price_install']");
            var price_from = $("input[name='price_from']");
            var preco = $("input[name='preco']");

            $.ajax({
                type: "POST",
                url: "inputs",
                data: {"scrap_id" : scrap_id, "dataPesq" : dataPesq},
                processData: true,
                dateType:'json',
                cache: false,
                success: function (data) {
                    $("#id").val("");
                    $("#imagem").attr('src', "");
                    $("#lojaUrl").attr('src', "");
                    $("#device").val("");
                    $("#position").val("");
                    $("#tipo_pagina").val("");
                    $("#tipo_anuncio").val("");
                    $("#categoria_id").val("");
                    $("#marca_id").val("");
                    $("#adType").val("");
                    $("#ad_type_detail").val("");
                    $("#text_target").html("");
                    $("#progressoID").html("");
                    $("#progressoID1").html("");
                    $("#datescrap").html("");
                    $("#pais_id").html("");
                    titulo.val("");
                    target.val("");
                    produto.val("");
                    detalhe.val("");
                    call_action.val("");
                    call_action.val("");
                    preco.val("");
                    price_from.val("");
                    price_install.val("");
                    $("#lojaUrl").attr('src', "");


                    var dados =  JSON.parse(data);


                    $("#id").val(dados.id);
                    $("#imagem").attr('src', dados.arquivo);
                    $("#device").val(dados.device);
                    $("#position").val(dados.position);
                    $("#tipo_pagina").val(dados.tipo_pagina_id);
                    $("#tipo_anuncio").val(dados.tipo_anuncio);
                    $("#categoria_id").val(dados.categoria_id);
                    $("#marca_id").val(dados.marca_id);
                    $("#adType").val(dados.ad_type);
                    $("#ad_type_detail").val(dados.ad_type_detail);
                    titulo.val(dados.titulo);
                    target.val(dados.target);
                    produto.val(dados.produto);
                    detalhe.val(dados.detalhe);
                    call_action.val("");
                    call_action.val(dados.call_action);
                    preco.val(dados.preco);
                    price_from.val(dados.price_from);
                    price_install.val(dados.price_install);
                    datescrap.append(dados.updated_at);
                    $("#pais_id").html(dados.pais_id);
                    $("#lojaid").html(dados.loja_id);
                    $("#lojaUrl").attr('src', dados.url);
                    $("#scrapIdPaginacao").html(dados.id);
                    $("#text_target").html(dados.produto);
                    $("#progressoID").html(dados.progresso);
                    $("#progressoID1").html(dados.progresso+"%");
                    $('#select2-scrap_id-container').html(dados.id);
                    var validaprev = parseInt($('#scrap_id').val()) + parseInt(1);


                    if($('#scrap_id').val() == null) {
                        $('#scrapIdPaginacao2').attr("disabled", true);
                    } else {
                        $('#scrapIdPaginacao2').attr("disabled", false);

                    }

                    if( status == 1)
                    {
                        $('#mensagemSalvo').css('display', '');
                        $('#mensagemDescarte').css('display', 'none');
                    } else if ( status == 5) {

                        $('#mensagemSalvo').css('display', 'none');
                        $('#mensagemDescarte').css('display', '');
                    } else  {
                        $('#mensagemSalvo').css('display', 'none');
                        $('#mensagemDescarte').css('display', 'none');
                    }


                    $('.form-control').each(function(e,i){
                        if(this.value.trim() === ""){
                            $(this).parent().addClass('has-warning');
                        } else {
                            $(this).parent().removeClass('has-warning');
                        }
                    });
                }

            });
        });



		$('#scrap_id,.scrap_id').change(function() {
			var scrap_id = $(this).val();
			var dataPesq = $('#period').val();
			var id =  $("input[name='id']");
			var target  = $("input[name='target']");
			var produto = $("input[name='produto']");
			var titulo = $("input[name='titulo']");
			var detalhe = $("input[name='detalhe']");
			var call_action = $("input[name='call_action']");
			var price_install = $("input[name='price_install']");
			var price_from = $("input[name='price_from']");
			var preco = $("input[name='preco']");

			$.ajax({
				type: "POST",
				url: "inputs",
				data: {"scrap_id" : scrap_id, "dataPesq" : dataPesq},
				processData: true,
				dateType:'json',
				cache: false,
				success: function (data) {
                    $("#id").val("");
                    $("#scrapIdPaginacao").val("");
                    $("#imagem").attr('src', "");
                    $("#lojaUrl").attr('src', "");
                    $("#device").val("");
                    $("#position").val("");
                    $("#tipo_pagina").val("");
                    $("#tipo_anuncio").val("");
                    $("#categoria_id").val("");
                    $("#marca_id").val("");
                    $("#adType").val("");
                    $("#ad_type_detail").val("");
                    $("#datescrap").html("");
                    $("#pais_id").html("");
                    titulo.val("");
                    target.val("");
                    produto.val("");
                    detalhe.val("");
                    call_action.val("");
                    call_action.val("");
                    preco.val("");
                    price_from.val("");
                    price_install.val("");
                    $("#lojaUrl").attr('src', "");
                    $("#text_target").html("");
                    $("#progressoID").html("");


					var dados =  JSON.parse(data);


					$("#id").val(dados.id);
					$("#imagem").attr('src', dados.arquivo);
					$("#device").val(dados.device);
					$("#position").val(dados.position);
					$("#tipo_pagina").val(dados.tipo_pagina_id);
					$("#tipo_anuncio").val(dados.tipo_anuncio);
					$("#categoria_id").val(dados.categoria_id);
					$("#marca_id").val(dados.marca_id);
					$("#adType").val(dados.ad_type);
					$("#ad_type_detail").val(dados.ad_type_detail);
					titulo.val(dados.titulo);
					target.val(dados.target);
					produto.val(dados.produto);
					detalhe.val(dados.detalhe);
                    call_action.val("");
					call_action.val(dados.call_action);
					preco.val(dados.preco);
					price_from.val(dados.price_from);
					price_install.val(dados.price_install);
                    datescrap.append(dados.updated_at);
                    $("#pais_id").html(dados.pais_id);
                    $("#lojaid").html(dados.loja_id);
                    $("#lojaUrl").attr('src', dados.url);
                    $("#scrapIdPaginacao").html(dados.id);
                    $("#scrapIdPaginacao").val(dados.id);
                    $("#text_target").html(dados.produto);
                    $("#text_target").attr("href", dados.target);
                    $("#progressoID").html(dados.progresso);
                    $("#progressoID1").html(dados.progresso+"%");
                    $('#select2-scrap_id-container').html(dados.id);

                    var validaprev = parseInt($('#scrap_id').val()) + parseInt(1);


                    if($('#scrap_id').val() == null) {
                        $('#scrapIdPaginacao2').attr("disabled", true);
                    } else {
                        $('#scrapIdPaginacao2').attr("disabled", false);

                    }


                    $('.form-control').each(function(e,i){
                        if(this.value.trim() === ""){
                            $(this).parent().addClass('has-warning');
                        } else {
                            $(this).parent().removeClass('has-warning');
                        }
                    });
				}

			});
		});

		setTimeout(function(){
			$("div.alert").remove();
		}, 5000 ); // 5 secs

		// Hide/Show Filter
		jQuery("#filtro").hide();
		jQuery("#filtro_select").show();
		jQuery("#filtro_select").click(function(){
		  jQuery("#filtro").slideToggle();
		});
		jQuery('.hideMe').click(function(){
		  jQuery(this).parent().slideUp();
		});

		// Fulscreen button
		jQuery('#panel-actions').click(function(e){
			var $this = $(this);
			if ($this.children('i').hasClass('glyphicon-resize-full')) {
				$this.children('i').removeClass('glyphicon-resize-full');
				$this.children('i').addClass('glyphicon-resize-small');
				jQuery('#progress-bar2').css('display','inline');
			} else if ($this.children('i').hasClass('glyphicon-resize-small')) {
				$this.children('i').removeClass('glyphicon-resize-small');
				$this.children('i').addClass('glyphicon-resize-full');
				jQuery('#progress-bar2').css('display','none');
			}
			jQuery('#panel-fullscreen').toggleClass('panel-fullscreen');
		});

		//Date range picker
		$('#period').daterangepicker({
			ranges   : {
			  'Today'       : [moment(), moment()],
			  'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			  'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
			  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			  'This Month'  : [moment().startOf('month'), moment().endOf('month')],
			  'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			startDate: moment().subtract(JSON.parse(<?php echo $dias;?>), 'days'),
			endDate  : moment(),
			autoclose: true
		  },
		  function (start, end) {
			$('#period span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
		  }
		)

		//POPOVER
		jQuery('.img-popover').popover({
		  html: true,
		  trigger: 'hover',
		  placement: 'auto left',
		  content: function () {
			return '<img src="'+$(this).attr('src') + '" width="auto" height="auto" style="max-width:600px;" />';
		  }
		 });

		$("body").addClass("sidebar-collapse");



        $('.form-control').each(function(e,i){
            if(this.value.trim() === ""){
                $(this).parent().addClass('has-warning');
            } else {
                $(this).parent().removeClass('has-warning');
            }
        });


	});

    $('#tipo_pagina').change(function () {
        var selected4 = $(this).find(':selected').text();
        if ( selected4 == "Search" ) {
            $("#tipo_anuncio").find("option:contains('Listagem')").attr('selected', 'selected');
            $("#tipo_anuncio").find("option:not(:contains('Listagem')").prop('selected', 'false');
            $("#type").find("option:contains('Organic')").attr('selected', 'selected');
            $("#type").find("option:not(:contains('Organic')").prop('selected', 'false');
            $(".offer-ad").hide();
            $(".offer-organic").show();
        } else {
        }
    });

    $('#tipo_anuncio').change(function () {
        var selected3 = $(this).find(':selected').text();

        if ( selected3 == "Ad Placement" ) {
            $("#type").find("option:contains('Ad')").attr('selected', 'selected');
            $("#type").find("option:not(:contains('Ad')").prop('selected', 'false');
            $(".offer-ad").show();
            $(".offer-organic").hide();
        } else if ( selected3 == "Listagem" ) {
            $("#type").find("option:contains('Organic')").attr('selected', 'selected');
            $("#type").find("option:not(:contains('Organic')").prop('selected', 'false');
            $(".offer-ad").hide();
            $(".offer-organic").show();
        } else {
        }
    });

    var selected = $( "#type option:selected" ).text();
    if ( selected == "Ad" ) {
        $(".offer-organic").hide();
    } else if ( selected == "Organic" ) {
        $(".offer-ad").hide();
    }
    $('#type').on('change', function() {
        var selected2 = $(this).find(':selected').text();
        if ( selected2 == "Ad" ) {
            $(".offer-ad").show();
            $(".offer-organic").hide();
        } else if ( selected2 == "Organic" ) {
            $(".offer-ad").hide();
            $(".offer-organic").show();
        }
    });





    $('#country').change(function(){
		var countryID = $(this).val();
		if(countryID){
			$.ajax({
				type:"GET",
				url:"{{url('inputs/get-loja-list')}}?country_id="+countryID,
				success:function(res){
					if(res){
						$("#loja_id").empty();
						$("#loja_id").append('<option>All Retailers</option>');
						$.each(res,function(key,value){
							$("#loja_id").append('<option value="'+key+'">'+value+'</option>');
						});

					}else{
					    $("#loja_id").empty();

					}
				}
			});
		}else{
		        $("#loja_id").empty();
		}
	});

    if('#country' != null){
        $("#country").val({{$sessionBusca['pais']}});
        var countryID =  JSON.parse({{$sessionBusca['pais']}});
        var lojaID = JSON.parse({{$sessionBusca['loja']}});
        $.ajax({
            type:"GET",
            url:"{{url('inputs/get-loja-list')}}?country_id="+countryID,
            success:function(res){
                if(res){
                    $("#loja_id").empty();
                    $("#loja_id").append('<option>All Retailers</option>');
                    $.each(res,function(key,value){
                        if(lojaID==key){
                            $("#loja_id").append('<option value="'+key+'" selected>'+value+'</option>');
                        }else {
                            $("#loja_id").append('<option value="'+key+'">'+value+'</option>');
                        }

                    });

                }else{
                    $("#loja_id").empty();

                }
            }
        });
    }

    $("#descartar").click( function (e) {
        e.preventDefault();
        var scrap_id = $('#id').val();
        var dataPesq = $('#period').val();

        var target  = $("input[name='target']");
        var produto = $("input[name='produto']");
        var titulo = $("input[name='titulo']");
        var detalhe = $("input[name='detalhe']");
        var call_action = $("input[name='call_action']");
        var price_install = $("input[name='price_install']");
        var price_from = $("input[name='price_from']");
        var preco = $("input[name='preco']");

        $.ajax({
            type: "POST",
            url: "descart",
            data: {"scrap_id" : scrap_id, "dataPesq" : dataPesq},
            processData: true,
            dateType: 'json',
            cache: false,
            success: function (data) {
                $("#result_json").append("whatever you want!");

                $("#id").val("");
                $("#scrapIdPaginacao").val("");
                $("#imagem").attr('src', "");
                $("#lojaUrl").attr('src', "");
                $("#device").val("");
                $("#position").val("");
                $("#tipo_pagina").val("");
                $("#tipo_anuncio").val("");
                $("#categoria_id").val("");
                $("#marca_id").val("");
                $("#adType").val("");
                $("#ad_type_detail").val("");
                $("#datescrap").html("");
                $("#pais_id").html("");
                titulo.val("");
                target.val("");
                produto.val("");
                detalhe.val("");
                call_action.val("");
                call_action.val("");
                preco.val("");
                price_from.val("");
                price_install.val("");
                $("#lojaUrl").attr('src', "");
                $("#progressoID").html("");
                $("#progressoID1").html("");
                $("#scrapIdPaginacao").val("");



                var dados =  JSON.parse(data);

                $("#id").val(dados.id);
                $("#scrapIdPaginacao").val(dados.id);
                $("#scrapIdPaginacao").html(dados.id);
                $("#imagem").attr('src', dados.arquivo);
                $("#device").val(dados.device);
                $("#position").val(dados.position);
                $("#tipo_pagina").val(dados.tipo_pagina_id);
                $("#tipo_anuncio").val(dados.tipo_anuncio);
                $("#categoria_id").val(dados.categoria_id);
                $("#marca_id").val(dados.marca_id);
                $("#adType").val(dados.ad_type);
                $("#ad_type_detail").val(dados.ad_type_detail);
                titulo.val(dados.titulo);
                target.val(dados.target);
                produto.val(dados.produto);
                detalhe.val(dados.detalhe);
                call_action.val("");
                call_action.val(dados.call_action);
                preco.val(dados.preco);
                price_from.val(dados.price_from);
                price_install.val(dados.price_install);
                datescrap.append(dados.updated_at);
                $("#pais_id").html(dados.pais_id);
                $("#lojaid").html(dados.loja_id);
                $("#lojaUrl").attr('src', dados.url);
                $("#progressoID").html(dados.progresso);
                $("#progressoID1").html(dados.progresso);


                $('#device').focus()

                $("#formRegister").fadeTo(800, 0.0).fadeTo(800, 1.0);

            }
        });

    });

    $("#formRegister").submit( function (e) {
        e.preventDefault();

        var formData = jQuery( this ).serialize();

        var scrap_id = $('#id').val();
        var dataPesq = $('#period').val();

        var target  = $("input[name='target']");
        var produto = $("input[name='produto']");
        var titulo = $("input[name='titulo']");
        var detalhe = $("input[name='detalhe']");
        var call_action = $("input[name='call_action']");
        var price_install = $("input[name='price_install']");
        var price_from = $("input[name='price_from']");
        var preco = $("input[name='preco']");

        $.ajax({
            type: "POST",
            url: "inputs/update",
            data: formData,
            processData: true,
            dateType: 'json',
            cache: false,
            success: function (data) {

                $("#id").val("");
                $("#scrapIdPaginacao").val("");
                $("#imagem").attr('src', "");
                $("#lojaUrl").attr('src', "");
                $("#device").val("");
                $("#position").val("");
                $("#tipo_pagina").val("");
                $("#tipo_anuncio").val("");
                $("#categoria_id").val("");
                $("#marca_id").val("");
                $("#adType").val("");
                $("#ad_type_detail").val("");
                $("#datescrap").html("");
                $("#pais_id").html("");
                titulo.val("");
                target.val("");
                produto.val("");
                detalhe.val("");
                call_action.val("");
                call_action.val("");
                preco.val("");
                price_from.val("");
                price_install.val("");
                $("#lojaUrl").attr('src', "");
                $("#progressoID").html("");
                $("#progressoID1").html("");
                $("#scrapIdPaginacao").val("");


                var dados =  JSON.parse(data);

                $("#id").val(dados.id);
                $("#imagem").attr('src', dados.arquivo);
                $("#device").val(dados.device);
                $("#position").val(dados.position);
                $("#tipo_pagina").val(dados.tipo_pagina_id);
                $("#tipo_anuncio").val(dados.tipo_anuncio);
                $("#categoria_id").val(dados.categoria_id);
                $("#marca_id").val(dados.marca_id);
                $("#adType").val(dados.ad_type);
                $("#ad_type_detail").val(dados.ad_type_detail);
                titulo.val(dados.titulo);
                target.val(dados.target);
                produto.val(dados.produto);
                detalhe.val(dados.detalhe);
                call_action.val("");
                call_action.val(dados.call_action);
                preco.val(dados.preco);
                price_from.val(dados.price_from);
                price_install.val(dados.price_install);
                datescrap.append(dados.updated_at);
                $("#pais_id").html(dados.pais_id);
                $("#lojaid").html(dados.loja_id);
                $("#lojaUrl").attr('src', dados.url);
                $("#scrapIdPaginacao").html(dados.id);
                $("#progressoID").html(dados.progresso);
                $("#progressoID1").html(dados.progresso);
                $("#scrapIdPaginacao").val(dados.id);
                $("#scrapIdPaginacao").html(dados.id);

                $('#device').focus()


                $("#formRegister").fadeTo(800, 0.0).fadeTo(800, 1.0);

            }
        });
    });

	$(document).ready(function(){
		//Initialize Select2 Elements
		//jQuery('.select2').select2()
		jQuery('.select2').select2().enable(false);
	});
</script>

@stop

@endsection
