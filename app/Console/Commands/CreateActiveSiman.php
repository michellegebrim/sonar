<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Urlbox\Screenshots\Facades\Urlbox;
use DB;
use App\Marca;
use App\Categoria;
use App\Produto;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class CreateActiveSiman extends Command
{

    protected $signature = 'CreateActiveSiman:insert';
    protected $description = 'Create Screenshots DB';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $marcas = Marca::all();
        $produtos = Produto::all();
        $categorias = Categoria::all();

        $marca_id = 0;
        $produto_id = 0;
        $categoria_id = 0;

        $urlbox = Urlbox::fromCredentials('API_KEY', 'API_SECRET');

        $client = new Client();

        $data = date('Y_m_d H:i:s');
        $dt = explode('_', $data);

        $mes = (int) $dt[1];
        $dh = explode(" ", $dt[2]);
        $dia = (int) $dh[0];
        $data = $dt[0]."_". $mes ."_". $dia;

        $horaG1 = strtotime('03:00');
        $horaG2 = strtotime('11:00');
        $horaG3 = strtotime('19:00');
        $horaAtual = strtotime(date('H:i'));

        if ($horaAtual >= $horaG1 && $horaAtual < $horaG2) {
            $turno = "G1";
        } elseif ($horaAtual >= $horaG2 && $horaAtual < $horaG3){
            $turno = "G2";
        } else {
            $turno = "G3";
        }

        //criacao de diretorio desktop
        if(!is_dir("/var/www/html/retail/public/screenshots/sela/desktop/".$dt[0])) {
            mkdir("/var/www/html/retail/public/screenshots/sela/desktop/".$dt[0], 0777);
        }

        if(!is_dir("/var/www/html/retail/public/screenshots/sela/desktop/".$dt[0]."/".$dt[1])){
            mkdir("/var/www/html/retail/public/screenshots/sela/desktop/".$dt[0]."/".$dt[1], 0777);
        }
        //criacao de desktop mobile
        if(!is_dir("/var/www/html/retail/public/screenshots/sela/mobile/".$dt[0])) {
            mkdir("/var/www/html/retail/public/screenshots/sela/mobile/".$dt[0], 0777);
        }

        if(!is_dir("/var/www/html/retail/public/screenshots/sela/mobile/".$dt[0]."/".$dt[1])){
            mkdir("/var/www/html/retail/public/screenshots/sela/mobile/".$dt[0]."/".$dt[1], 0777);
        }

        $lojadb = DB::table('lojas')->where('descricao', 'Siman')->first();
        $loja_id = $lojadb->id;
        $pais_id = $lojadb->pais_id;
        $lojaNome = 'Siman';

        $urlDesktopSela = [
            'siman.com/guatemala/',
            'siman.com/guatemala/tecnologia/televisores/tv.html',
            'siman.com/guatemala/electrodomesticos/linea-blanca/refrigeracion.html',
            'siman.com/guatemala/electrodomesticos/linea-blanca/lavado-y-secado/lavadoras.htm',
        ];

        $contDesktopSela = 0;
        //      $options["proxy"] = 'sonar:sonar@2017@panama.wonderproxy.com:10000';

        foreach ( $urlDesktopSela as $value):

            if( $contDesktopSela==0 ) {
                $tipo = 'Homepage_';
            } elseif( $contDesktopSela == 1 ) {
                $tipo = 'TVs_';
            }elseif( $contDesktopSela == 2 ) {
                $tipo = 'Refrigerators_';
            }elseif ( $contDesktopSela == 3) {
                $tipo = 'Washing Machines_';
            }

            $options["url"] = $value;
            $options["format"] = "jpg";
            $options["quality"] = 70;
            $options["full_page"] = true;
            $urlboxUrl = Urlbox::generateUrl($options);

            $url = $urlboxUrl;
            $image = @file_get_contents($url);
            if ($image !== false) {

                $image= base64_encode($image);
                $image = base64_decode($image);
                $name = "Sela_".$lojaNome."_Desktop_".$tipo.$data.$turno.".jpg";
                $im = @imagecreatefromstring($image);
                header('Content-Type: image/jpg');
                @imagejpeg($im, "/var/www/html/retail/public/screenshots/sela/desktop/".$dt[0]."/".$dt[1]."/".$name);

                DB::table('screenshots')->insert(
                    [
                        'pais_id' => $pais_id,
                        'loja_id' =>$loja_id,
                        'device' => 'Desktop',
                        'tipo_pagina' => $tipo,
                        'link' => $urlboxUrl,
                        'arquivo'=> $name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );

            } else {
                echo 'Ocorreu um erro.';

            }
            $contDesktopSela ++;
        endforeach;

        $urlMobileSela = [
            'siman.com/guatemala/',
            'siman.com/guatemala/tecnologia/televisores/tv.html',
            'siman.com/guatemala/electrodomesticos/linea-blanca/refrigeracion.html',
            'siman.com/guatemala/electrodomesticos/linea-blanca/lavado-y-secado/lavadoras.htm',
        ];

        $contMobileSela = 0;

        foreach ( $urlMobileSela as $value):

            $grupo = explode(".", $value);
            $loja = $grupo[0];
            $loja = ucfirst($loja);

            if( $contMobileSela==0 ) {
                $tipo = 'Homepage_';
            } elseif( $contMobileSela == 1 ) {
                $tipo = 'TVs_';
            }elseif( $contMobileSela == 2 ) {
                $tipo = 'Refrigerators_';
            }elseif ( $contMobileSela == 3) {
                $tipo = 'Washing Machines_';
            }

            $options["url"] = $value;
            $options["format"] = "jpg";
            $options["quality"] = 70;
            $options["full_page"] = true;
            $urlboxUrl = Urlbox::generateUrl($options);

            $url = $urlboxUrl;
            $image = @file_get_contents($url);
            if ($image !== false) {

                $image= base64_encode($image);
                $image = base64_decode($image);
                $name = "Sela_".$lojaNome."_Mobile_".$tipo.$data.$turno.".jpg";
                $im = @imagecreatefromstring($image);
                header('Content-Type: image/jpg');
                @imagejpeg($im, "/var/www/html/retail/public/screenshots/sela/mobile/".$dt[0]."/".$dt[1]."/".$name);

                DB::table('screenshots')->insert(
                    [
                        'pais_id' => $pais_id,
                        'loja_id' =>$loja_id,
                        'device' => 'Mobile',
                        'tipo_pagina' => $tipo,
                        'link' => $urlboxUrl,
                        'arquivo'=> $name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );

            } else {
                echo 'Ocorreu um erro.';

            }
            $contMobileSela ++;
        endforeach;


        //import.io
        //criacao de diretorio desktop
        if(!is_dir("/var/www/html/retail/public/printshtml/sela/desktop/".$dt[0])) {
            mkdir("/var/www/html/retail/public/printshtml/sela/desktop/".$dt[0], 0777);
        }

        if(!is_dir("/var/www/html/retail/public/printshtml/sela/desktop/".$dt[0]."/".$dt[1])){
            mkdir("/var/www/html/retail/public/printshtml/sela/desktop/".$dt[0]."/".$dt[1], 0777);
        }
        //criacao de desktop mobile
        if(!is_dir("/var/www/html/retail/public/printshtml/sela/mobile/".$dt[0])) {
            mkdir("/var/www/html/retail/public/printshtml/sela/mobile/".$dt[0], 0777);
        }

        if(!is_dir("/var/www/html/retail/public/printshtml/sela/mobile/".$dt[0]."/".$dt[1])){
            mkdir("/var/www/html/retail/public/printshtml/sela/mobile/".$dt[0]."/".$dt[1], 0777);
        }

        //SELA_Siman_Desktop_Homepage_Ads
        $requestDeskeCategoryOrganic = $client->request('GET', 'https://extraction.import.io/query/extractor/6e6c237a-5f22-4c9f-b24f-5efedf4dee7b?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=http%3A%2F%2Fwww.siman.com%2Fguatemala%2F%23%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $requestDeskeCategoryOrganic =  json_decode($requestDeskeCategoryOrganic->getBody(), true);

        $dadosDeskeCategoryOrganic = $requestDeskeCategoryOrganic['extractorData']["data"];
        $position = 1;

        $urlTipo = $requestDeskeCategoryOrganic['url'];
        $timestamp = $requestDeskeCategoryOrganic["timestamp"];
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        $date_captura = date_format($date, 'U = Y-m-d H:i:s');

        foreach ( $dadosDeskeCategoryOrganic as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    $name = "Sela_".$lojaNome."_Desktop_Homepage_Ads_".$data.$turno.$position.".jpg";
                    $im = @imagecreatefromstring($image);
                    header("Content-type: image/jpg");

                    @imagejpeg($im, '/var/www/html/retail/public/printshtml/sela/desktop/'.$dt[0]."/".$dt[1]."/".$name, 100);

                } else {
                    $imagem = "";
                }

                if (isset($value["Target URL"][0]["text"])){
                    $target = $value["Target URL"][0]["text"];
                } else {
                    $target = "";
                }

                if (isset($value["Place"][0]["text"])){
                    $place = $value["Place"][0]["text"];
                } else {
                    $place = "";
                }

                if (isset($value["Title"][0]["text"])) {
                    $titulo = $value["Title"][0]["text"];

                } else {
                    $titulo = "";
                }

                if (isset($value["Call to Action"][0]["text"])){
                    $callEction = $value["Call to Action"][0]["text"];
                } else {
                    $callEction = "";
                }

                if (isset($value["Price"][0]["text"])){
                    $preco = $value["Price"][0]["text"];
                } else {
                    $preco = "";
                }

                if(isset($value["Price From"][0]["text"])) {
                    $price_from = @$value["Price From"][0]["text"];
                } else {
                    $price_from = "";
                }

                if(isset($value["Price Install"][0]["text"])) {
                    $price_install = $value["Price Install"][0]["text"];
                } else {
                    $price_install = "";
                }

                if(isset($value["Product"][0]["text"])) {
                    $produto = $value["Product"][0]["text"];
                } else {
                    $produto = "";
                }

                if(isset($value["Type"][0]["text"])) {
                    $type = $value["Type"][0]["text"];

                    if($type == "Organic") {
                        $type = 9;
                    } elseif( $type == "Ad") {
                        $type = 3;
                    } elseif( $type == "Carousel") {
                        $type = 2;
                    }
                } else {
                    $type = "";
                }

                $string = $titulo.$produto.$target.$urlTipo;

                foreach ($marcas as $marca) {
                    $buscaMarca = @strstr($string, $marca->descricao);
                    $buscaMarcaDesc = @strstr($string, $marca->descricao_outros);
                    if(!empty($buscaMarca)){
                        $marca_id = (int)$marca->id;

                    } elseif (!empty($buscaMarcaDesc)){
                        $marca_id = (int)$marca->id;
                    }

                }

                foreach ($produtos as $value) {
                    $buscaProduto = @strstr($string, $value->descricao);

                    if(!empty($buscaProduto)){
                        $produto_id = (int)$value->id;
                    }
                }

                foreach ($categorias as $categoria) {
                    $buscaCategotia = @strstr($string, $categoria->descricao);
                    $buscaCategotiaDescricao = @strstr($string, $categoria->descricao_outros);
                    if(!empty($buscaCategotia)){
                        $categoria_id = (int)$categoria->id;
                    }elseif (!empty($buscaCategotiaDescricao)){
                        $categoria_id = (int)$categoria->id;
                    }
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => 1,
                        'categoria_id' => $categoria_id,
                        'marca_id' => $marca_id,
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => 3,
                        'turno' => $turno,
                        'device' => 'Desktop',
                        'place' => $place,
                        'position' => $position,
                        'imagem' => $imagem,
                        'arquivo' => $name,
                        'target' => $target,
                        'type' => $type,
                        'titulo' => $titulo,
                        'produto' => $produto,
                        'detalhe' => '',
                        'call_action' => $callEction,
                        'preco' => $preco,
                        'price_from' => $price_from,
                        'price_install' => $price_install,
                        'url' => $urlTipo,
                        'detalhe_tipo_anuncio' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'status' => 0,
                        'produto_id' => $produto_id
                    ]);
                $position ++;
            endforeach;
        endforeach;


        //SELA_Siman_Desktop_Homepage_Carousel
        $requestDeskeCategoryOrganic = $client->request('GET', 'https://extraction.import.io/query/extractor/6e6c237a-5f22-4c9f-b24f-5efedf4dee7b?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=http%3A%2F%2Fwww.siman.com%2Fguatemala%2F%23%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $requestDeskeCategoryOrganic =  json_decode($requestDeskeCategoryOrganic->getBody(), true);

        $dadosDeskeCategoryOrganic = $requestDeskeCategoryOrganic['extractorData']["data"];
        $position = 1;

        $urlTipo = $requestDeskeCategoryOrganic['url'];
        $timestamp = $requestDeskeCategoryOrganic["timestamp"];
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        $date_captura = date_format($date, 'U = Y-m-d H:i:s');

        foreach ( $dadosDeskeCategoryOrganic as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    $name = "Sela_".$lojaNome."_Desktop_Homepage_Carousel_".$data.$turno.$position.".jpg";
                    $im = @imagecreatefromstring($image);
                    header("Content-type: image/jpg");

                    @imagejpeg($im, '/var/www/html/retail/public/printshtml/sela/desktop/'.$dt[0]."/".$dt[1]."/".$name, 100);

                } else {
                    $imagem = "";
                }

                if (isset($value["Target URL"][0]["text"])){
                    $target = $value["Target URL"][0]["text"];
                } else {
                    $target = "";
                }

                if (isset($value["Place"][0]["text"])){
                    $place = $value["Place"][0]["text"];
                } else {
                    $place = "";
                }

                if (isset($value["Title"][0]["text"])) {
                    $titulo = $value["Title"][0]["text"];

                } else {
                    $titulo = "";
                }

                if (isset($value["Call to Action"][0]["text"])){
                    $callEction = $value["Call to Action"][0]["text"];
                } else {
                    $callEction = "";
                }

                if (isset($value["Price"][0]["text"])){
                    $preco = $value["Price"][0]["text"];
                } else {
                    $preco = "";
                }

                if(isset($value["Price From"][0]["text"])) {
                    $price_from = @$value["Price From"][0]["text"];
                } else {
                    $price_from = "";
                }

                if(isset($value["Price Install"][0]["text"])) {
                    $price_install = $value["Price Install"][0]["text"];
                } else {
                    $price_install = "";
                }

                if(isset($value["Product"][0]["text"])) {
                    $produto = $value["Product"][0]["text"];
                } else {
                    $produto = "";
                }

                if(isset($value["Type"][0]["text"])) {
                    $type = $value["Type"][0]["text"];

                    if($type == "Organic") {
                        $type = 9;
                    } elseif( $type == "Ad") {
                        $type = 3;
                    } elseif( $type == "Carousel") {
                        $type = 2;
                    }
                } else {
                    $type = "";
                }

                $string = $titulo.$produto.$target.$urlTipo;

                foreach ($marcas as $marca) {
                    $buscaMarca = @strstr($string, $marca->descricao);
                    $buscaMarcaDesc = @strstr($string, $marca->descricao_outros);
                    if(!empty($buscaMarca)){
                        $marca_id = (int)$marca->id;

                    } elseif (!empty($buscaMarcaDesc)){
                        $marca_id = (int)$marca->id;
                    }

                }

                foreach ($produtos as $value) {
                    $buscaProduto = @strstr($string, $value->descricao);

                    if(!empty($buscaProduto)){
                        $produto_id = (int)$value->id;
                    }
                }

                foreach ($categorias as $categoria) {
                    $buscaCategotia = @strstr($string, $categoria->descricao);
                    $buscaCategotiaDescricao = @strstr($string, $categoria->descricao_outros);
                    if(!empty($buscaCategotia)){
                        $categoria_id = (int)$categoria->id;
                    }elseif (!empty($buscaCategotiaDescricao)){
                        $categoria_id = (int)$categoria->id;
                    }
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => 1,
                        'categoria_id' => $categoria_id,
                        'marca_id' => $marca_id,
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => 2,
                        'turno' => $turno,
                        'device' => 'Desktop',
                        'place' => $place,
                        'position' => $position,
                        'imagem' => $imagem,
                        'arquivo' => $name,
                        'target' => $target,
                        'type' => $type,
                        'titulo' => $titulo,
                        'produto' => $produto,
                        'detalhe' => '',
                        'call_action' => $callEction,
                        'preco' => $preco,
                        'price_from' => $price_from,
                        'price_install' => $price_install,
                        'url' => $urlTipo,
                        'detalhe_tipo_anuncio' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'status' => 0,
                        'produto_id' => $produto_id
                    ]);
                $position ++;
            endforeach;
        endforeach;





    }
}
