<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Http\Requests;
use App\TipoAnuncio;
use App\TipoPagina;
use Illuminate\Http\Request;
use App\Pais;
use App\Loja;
use App\Scrap;
use App\Marca;
use App\Produto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Screenshot;

use DB;
use Session;



class InputsController extends Controller
{

    public function index(Request $request)
    {
        $paises = $this->paises();
        $marcas = $this->marcas();
        $place = $this->getPlace();
        $tipoPagina = $this->tipoPagina();
        $categorias = $this->getCategorias();
        $tipoAnuncio = $this->getTipoAnuncio();
        $scrap = 0;
        $progresso =0;
        $totalScrapsAnalise = 0;
        $scraps = 0;
        $totalScrapProntos = 0;
        $nomePais = 'All Countries';
        $nomeloja = 'All Retailers';

        Session::put('sessao', ['pais' =>  '', 'loja' => '', 'data'=> '', 'nomePais' => $nomePais, 'nomeLoja' => $nomeloja]);
        $sessionBusca = Session::get('sessao');

        if(isset($request->scrap_id)){

            $id = $request->scrap_id;
            $scrap =  Scrap::where('id', $id)->first();

            $data = $this->explode($request->dataPesq);
            $datai = date('Y-m-d', strtotime($data[0]));
            $dataf = date('Y-m-d', strtotime($data[1]));


            $pais_id = $scrap->pais_id;
            $loja_id = $scrap->loja_id;

            $totalScrapsAnalise =  Scrap::where('pais_id', $pais_id)
                ->where('loja_id',  $loja_id)
                ->where('status', 0)
                ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                ->count();


            $totalScraps =  Scrap::where('pais_id', $pais_id)
                ->where('loja_id',  $loja_id)
                ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                ->count();



            $totalScrapProntos =  Scrap::where('pais_id', $pais_id)
                ->where('loja_id',  $loja_id)
                ->where('status','<>',  0)
                ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                ->count();

                $totalPer = ($totalScrapProntos/$totalScraps)*100;
                $progresso = round($totalPer, 2);


            $produto = Produto::where('id', $scrap->produto_id)->first();
            if(!empty($produto)) {
                $scrap->produtoNome = $produto->descricao;
            } else {
                $scrap->produtoNome = 'Não definido';
            }

            if(substr($scrap->target, 0, 4) != 'http'){
                $scrap->target = "http://".$scrap->loja->url."/".$scrap->target;
            }




            $scrap->pais_id =$scrap->pais->pais;
            $scrap->loja_id =$scrap->loja->descricao;
            $scrap->arquivo =  $this->getImage($scrap);
            $scrap->url =  "https://www.google.com/s2/favicons?domain=http://".$scrap->loja->url;
            $scrap->progresso = $progresso;

            return json_encode($scrap);
        }

        return view('inputs.index',  compact('paises'),
            [
                'paises' => $paises,
                'scrap' => $scrap,
                'marcas' => $marcas,
                'categorias' => $categorias,
                'tipoPagina' => $tipoPagina,
                'place' => $place,
                'tipoAnuncio' => $tipoAnuncio,
                'scraps' => $scraps,
                'totalScrapsAnalise'=>$totalScrapsAnalise,
                'progresso' => $progresso,
                'sessionBusca' => $sessionBusca
            ]);

    }


    public function update(Request $request)
    {
        $id = $request->id;

        $data = $this->explode($request->dataPesq);
        $datai = date('Y-m-d', strtotime($data[0]));
        $dataf = date('Y-m-d', strtotime($data[1]));

        $scrap = Scrap::find($id);

        $scrap->id = $id;
        $scrap->tipo_pagina_id = $request->tipo_pagina;
        $scrap->tipo_anuncio_id = $request->tipo_anuncio;
        $scrap->categoria_id = $request->categoria_id;
        $scrap->marca_id = $request->marca_id;
        $scrap->device = $request->device;
        $scrap->place = $request->place;
        $scrap->target = $request->target;
        $scrap->type = $request->type;
        $scrap->titulo = $request->titulo;
        $scrap->produto = $request->produto;
        $scrap->detalhe = $request->detalhe;
        $scrap->call_action = $request->call_action;
        $scrap->preco = $request->preco;
        $scrap->price_from = $request->price_from;
        $scrap->price_install = $request->price_install;
        $scrap->detalhe = $request->detalhe;
        $scrap->status = 1;
        $scrap->user_id = Auth::user()->id;
        $scrap->ad_type = $request->adType;
        $scrap->detalhe_tipo_anuncio = $request->ad_type_detail;
        $scrap->produto_id = $request->product;

        $scrap->save();


        $id += 1;
        $scrap =  Scrap::where('id', $id)
                        ->where('status', '<>', 5)
                        ->where('status', '<>', 1)
                        ->first();




        $paisNome =$scrap->pais->pais;
        $lojaNome =$scrap->loja->descricao;
        $scrap->arquivo =  $this->getImage($scrap);

        $pais_id = $scrap->pais_id;
        $loja_id = $scrap->loja_id;

        $scrap->url =  "https://www.google.com/s2/favicons?domain=http://".$scrap->loja->url;

        $totalScrapsAnalise =  Scrap::where('pais_id', $pais_id)
            ->where('loja_id',  $loja_id)
            ->where('status', 0)
            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
            ->count();

        $scrap->totalScrapsAnalise = $totalScrapsAnalise;

        $totalScraps =  Scrap::where('pais_id', $pais_id)
            ->where('loja_id',  $loja_id)
            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
            ->count();




        $totalScrapProntos =  Scrap::where('pais_id', $pais_id)
            ->where('loja_id',  $loja_id)
            ->where('status','<>',  0)
            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
            ->count();


        $totalPer = ($totalScrapProntos/$totalScraps)*100;
        $progresso = round($totalPer, 2);

        $scrap->progresso = $progresso;

        $produto = Produto::where('id', $scrap->produto_id)->first();
        if(!empty($produto)) {
            $scrap->produtoNome = $produto->descricao;
        } else {
            $scrap->produtoNome = 'Não definido';
        }


        return json_encode($scrap);

    }

    public function  descart(Request $request)
    {
        $id = $request->scrap_id;

        $data = $this->explode($request->dataPesq);
        $datai = date('Y-m-d', strtotime($data[0]));
        $dataf = date('Y-m-d', strtotime($data[1]));

        $scrap = Scrap::find($id);
        $loja_id = $scrap->loja_id;
        $pais_id = $scrap->pais_id;

        $scrap->id = $id;
        $scrap->status = 5;
        $scrap->user_id = Auth::user()->id;
        $scrap->save();


        $scrap =  Scrap::where('pais_id', $pais_id)
                        ->where('loja_id', $loja_id)
                        ->where('status', '<>', 5)
                        ->where('status', '<>', 1)
                        ->first();



        $data = $this->explode($request->dataPesq);

        $datai = date('Y-m-d', strtotime($data[0]));
        $dataf = date('Y-m-d', strtotime($data[1]));


        $pais_id = $scrap->pais_id;
        $loja_id = $scrap->loja_id;

        $totalScrapsAnalise =  Scrap::where('pais_id', $pais_id)
            ->where('loja_id',  $loja_id)
            ->where('status', '=', 0)
            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
            ->count();


        $totalScraps =  Scrap::where('pais_id', $pais_id)
            ->where('loja_id',  $loja_id)
            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
            ->count();


        $totalScrapProntos =  Scrap::where('pais_id', $pais_id)
            ->where('loja_id',  $loja_id)
            ->where('status','<>',  0)
            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
            ->count();


        $totalPer = ($totalScrapProntos/$totalScraps)*100;
        $progresso = round($totalPer, 2);

        $scrap->pais_id =$scrap->pais->pais;
        $scrap->loja_id =$scrap->loja->descricao;
        $scrap->arquivo =  $this->getImage($scrap);
        $scrap->url =  "https://www.google.com/s2/favicons?domain=http://".$scrap->loja->url;
        $scrap->progresso = $progresso;
        $scrap->totalScrapsAnalise = $totalScrapsAnalise;

        if(substr($scrap->target, 0, 4) != 'http'){
            $scrap->target = "http://".$scrap->loja->url."/".$scrap->target;
        }


        //$request->session()->flash('status', 'Input descartado.');

        return json_encode($scrap);

    }


    public function search(Request $request)
    {
        $paises = $this->paises();
        $marcas = $this->marcas();
        $tipoPagina = $this->tipoPagina();
        $getPageType = $this->getPageType();
        $categorias = $this->getCategorias();
        $tipoAnuncio = $this->getTipoAnuncio();
        $place = $this->getPlace();
        $produtos = $this->getProdutos();

        $scrap = 0;
        $progresso =0;
        $totalScrapsAnalise = 0;
        $scraps = 0;

        if(!empty($request['pais_id'])) {
            $nomePais = Pais::where('id', $request['pais_id'])->first();
            $nomePais = $nomePais->pais;
        } else {
            $nomePais = 'All Countries';
        }

        $nomeLoja = 'All Retailers';

        $dataPesq = $request['data'];


        Session::put('sessao', ['pais' =>  $request['pais_id'], 'loja' => 0, 'data'=> $request['data'], 'nomePais' => $nomePais, 'nomeLoja' => $nomeLoja]);
        $sessionBusca = Session::get('sessao');

        if(!empty($request['data'])) {

            $pais_id = $request['pais_id'];
            $loja_id = $request['loja_id'];


            $data = $this->explode($request['data']);
            $datai = date('Y-m-d', strtotime($data[0]));
            $dataf = date('Y-m-d', strtotime($data[1]));


            if($loja_id !=  'All Retailers' && $nomePais != 'All Countries') {

                $nomeLoja = Loja::where('id', $request['loja_id'])->first();
                $nomeLoja = $nomeLoja->descricao;

                Session::put('sessao', ['pais' =>  $request['pais_id'], 'loja' => $loja_id, 'data'=> $request['data'], 'nomePais' => $nomePais, 'nomeLoja' => $nomeLoja]);
                $sessionBusca = Session::get('sessao');


                $totalScrapsAnalise =  Scrap::where('pais_id', $pais_id)
                    ->where('loja_id',  $loja_id)
                    ->where('status', 0)
                    ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                    ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                    ->count();

                if($totalScrapsAnalise > 0) {


                    $totalScraps =  Scrap::where('pais_id', $pais_id)
                        ->where('loja_id',  $loja_id)
                        ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                        ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                        ->count();

                    $totalScrapProntos =  Scrap::where('pais_id', $pais_id)
                        ->where('loja_id',  $loja_id)
                        ->where('status','<>',  0)
                        ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                        ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                        ->count();

                    $totalPer = ($totalScrapProntos/$totalScraps)*100;
                    $progresso = round($totalPer, 2);

                    $scrap =  Scrap::where('pais_id', $pais_id)
                        ->where('loja_id',  $loja_id)
                        ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                        ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                        ->first();


                    $scrap->arquivo =  $this->getImage($scrap);

                    if(substr($scrap->target, 0, 4) != 'http'){
                        $scrap->target = "http://".$scrap->loja->url."/".$scrap->target;
                    }


                    $scraps =  Scrap::where('pais_id', $pais_id)
                        ->where('loja_id',  $loja_id)
                        ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                        ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                        ->select('id')->get();

                    return view('inputs.index',  compact('paises'),
                        [
                            'paises' => $paises,
                            'scrap' => $scrap,
                            'marcas' => $marcas,
                            'categorias' => $categorias,
                            'tipoPagina' => $tipoPagina,
                            'tipoAnuncio' => $tipoAnuncio,
                            'scraps' => $scraps,
                            'totalScrapsAnalise'=>$totalScrapsAnalise,
                            'progresso' => $progresso,
                            'getPageType' => $getPageType,
                            'place' => $place,
                            'sessionBusca' => $sessionBusca,
                            'totalScrapsAnalise' => $totalScrapsAnalise,
                            'produtos' => $produtos,
                            'dataPesq' => $dataPesq
                        ]);
                } else {

                    $request->session()->flash('status', 'Sem dados para o período selecionado.');

                    return view('inputs.index',  compact('paises'),
                        [
                            'paises' => $paises,
                            'scrap' => $scrap,
                            'marcas' => $marcas,
                            'categorias' => $categorias,
                            'tipoPagina' => $tipoPagina,
                            'tipoAnuncio' => $tipoAnuncio,
                            'scraps' => $scraps,
                            'place' => $place,
                            'totalScrapsAnalise'=>$totalScrapsAnalise,
                            'progresso' => $progresso,
                            'sessionBusca' => $sessionBusca,
                            'totalScrapsAnalise' => $totalScrapsAnalise,
                            'produtos' => $produtos,
                            'dataPesq' => $dataPesq
                        ]);

                }

            } elseif ($nomePais == 'All Countries' && $nomeLoja == 'All Retailers') {

                $request->session()->flash('status', 'Tempo de procesamento excedido, ajuste o filtro.');

                return view('inputs.index',  compact('paises'),
                    [
                        'paises' => $paises,
                        'scrap' => $scrap,
                        'marcas' => $marcas,
                        'categorias' => $categorias,
                        'tipoPagina' => $tipoPagina,
                        'tipoAnuncio' => $tipoAnuncio,
                        'scraps' => $scraps,
                        'place' => $place,
                        'totalScrapsAnalise'=>$totalScrapsAnalise,
                        'progresso' => $progresso,
                        'sessionBusca' => $sessionBusca,
                        'totalScrapsAnalise' => $totalScrapsAnalise,
                        'produtos' => $produtos,
                        'dataPesq' => $dataPesq
                    ]);


            }else {

                $pais_id = $request['pais_id'];

                $data = $this->explode($request['data']);
                $datai = date('Y-m-d', strtotime($data[0]));
                $dataf = date('Y-m-d', strtotime($data[1]));

                $totalScrapsAnalise =  Scrap::where('pais_id', $pais_id)
                    ->where('status', 0)
                    ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                    ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                    ->count();



                if($totalScrapsAnalise > 0) {



                        $totalScraps =  Scrap::where('pais_id', $pais_id)
                            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                            ->count();


                        $totalScrapProntos =  Scrap::where('pais_id', $pais_id)
                            ->where('loja_id',  $loja_id)
                            ->where('status','<>',  0)
                            ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                            ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                            ->count();

                        $totalPer = ($totalScrapProntos/$totalScraps)*100;
                        $totalPer = number_format($totalPer, 2, ',', '');
                        $progresso = round($totalPer, 2);


                        $scrap =  Scrap::where('pais_id', $pais_id)
                                                ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                                                ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                                                ->first();

                        $scrap->arquivo =  $this->getImage($scrap);
                        if(substr($scrap->target, 0, 4) != 'http'){
                            $scrap->target = "http://".$scrap->loja->url."/".$scrap->target;
                        }



                    $scraps =  Scrap::where('pais_id', $pais_id)
                                        ->where('status', 0)
                                        ->where(DB::raw("DATE(created_at)"),'>=',$datai)
                                        ->where(DB::raw("DATE(created_at)"),'<=',$dataf)
                                        ->select('id')->get();


                        return view('inputs.index',  compact('paises'),
                            [
                                'paises' => $paises,
                                'scrap' => $scrap,
                                'marcas' => $marcas,
                                'categorias' => $categorias,
                                'tipoPagina' => $tipoPagina,
                                'tipoAnuncio' => $tipoAnuncio,
                                'scraps' => $scraps,
                                'totalScrapsAnalise'=>$totalScrapsAnalise,
                                'progresso' => $progresso,
                                'getPageType' => $getPageType,
                                'place' => $place,
                                'sessionBusca' => $sessionBusca,
                                'totalScrapsAnalise' => $totalScrapsAnalise,
                                'produtos' => $produtos,
                                'dataPesq' => $dataPesq
                            ]);

                 } else {

                        $request->session()->flash('status', 'Sem dados para o período selecionado.');

                        return view('inputs.index',  compact('paises'),
                            [
                                'paises' => $paises,
                                'scrap' => $scrap,
                                'marcas' => $marcas,
                                'categorias' => $categorias,
                                'tipoPagina' => $tipoPagina,
                                'tipoAnuncio' => $tipoAnuncio,
                                'scraps' => $scraps,
                                'place' => $place,
                                'totalScrapsAnalise'=>$totalScrapsAnalise,
                                'progresso' => $progresso,
                                'sessionBusca' => $sessionBusca,
                                'totalScrapsAnalise' => $totalScrapsAnalise,
                                'produtos' => $produtos,
                                'dataPesq' => $dataPesq
                            ]);

                    }

            }

        }
    }

    public function showScreenshots($id, $data, $device)
    {

        $loja = Loja::where('id', $id)->first();

        $pais = strtolower($loja->pais->pais);
        $pais = $this->tirarAcentos($pais);

        $screenshots = Screenshot::where('loja_id', $id)->where('device', $device)->orderBy('id', 'desc')->limit(20)->get();
        $screenshot = Screenshot::where('loja_id', $id)->where('device', $device)->orderBy('id', 'desc')->first();


        return view('inputs.modal', ['screenshots' => $screenshots, 'loja' => $loja, 'pais' => $pais, 'screenshot' =>  $screenshot]);
    }



    public function getImage($scrap)
    {
        $paisBusca = strtolower($scrap->pais->pais);
        $paisBusca = $this->tirarAcentos($paisBusca);


        $dt = explode(" ", $scrap->created_at);
        $datapasta = explode("-", $dt[0]);
        $anopasta = $datapasta[0];
        $mespasta = $datapasta[1];


        if($scrap->device == 'Desktop') {
            $diretorioImportio = '/printshtml/'.$paisBusca.'/desktop/'.$anopasta.'/'.$mespasta.'/';
        } elseif ($scrap->device == 'Mobile') {
            $diretorioImportio = '/printshtml/'.$paisBusca.'/mobile/'.$anopasta.'/'.$mespasta.'/';

        }

        return $arquivo = $diretorioImportio.$scrap->arquivo;

    }

    public function getCategorias()
    {
        $categorias = Categoria::where('id', '<>', 7)
                                ->where('cliente_id', 1)
                                ->Where('id', '<>', 8)->get();
        return $categorias;
    }

    public function getTipoAnuncio()
    {
        $tipoAnuncios = TipoAnuncio::orderBy('descricao', 'ASC')->get();
        return $tipoAnuncios;
    }

    public function marcas(){
        $marcas = Marca::where('cliente_id', 1)
                        ->orderBy('descricao', 'ASC')
            ->get();
        return $marcas;
    }

    public function lojasList(Request $request)
    {
        $lojas = Loja::where('pais_id', $request->country_id)
                 ->where('status',1)
                 ->orderBy('descricao','ASC')
                 ->pluck('descricao', 'id');

        return response()->json($lojas);
    }

    public function paises()
    {
        $paises = Pais::orderby('pais')->get()->pluck('pais', 'id');
        return $paises;
    }

    public function tipoPagina()
    {
        $tiposPaginas = TipoPagina::where('tipo_pagina', 'category')
            ->orWhere('tipo_pagina', 'homepage')
            ->orWhere('tipo_pagina', 'search')
            ->orderBy('tipo_pagina')
            ->get();
        return $tiposPaginas;
    }

    public function getProdutos()
    {
        $produtos = Produto::orderBy('descricao')->get();
        return $produtos;
    }

    public function getPlace()
    {
        $place = TipoPagina::where('tipo_pagina', 'Carousel')
            ->orWhere('tipo_pagina', 'Ad Placement')
            ->orWhere('tipo_pagina', 'Listagem')
            ->orderBy('tipo_pagina')
            ->get();

        return $place;
    }

     public function getPageType()
     {
        $getPageType = TipoPagina::where('tipo_pagina', 'Ad')
            ->orWhere('tipo_pagina', 'Organic')
            ->orderBy('tipo_pagina')
            ->get();
        return $getPageType;
    }
    public function explode($data)
    {
        $data = explode('-', $data);
        return $data;
    }

    function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }


    public function destroy($id)
    {

    }
}

