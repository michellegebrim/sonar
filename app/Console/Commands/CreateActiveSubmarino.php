<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Urlbox\Screenshots\Facades\Urlbox;
use DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;


class CreateActiveSubmarino extends Command
{

    protected $signature = 'CreateActiveSubmarino:insert';
    protected $description = 'Create Screenshots DB';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $urlbox = \Urlbox\Screenshots\Urlbox::fromCredentials('API_KEY', 'API_SECRET');
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
        } elseif ($horaAtual >= $horaG3) {
            $turno = "G3";
        }

        $lojadb = DB::table('lojas')->where('descricao', 'Submarino')->first();
        $loja_id = $lojadb->id;
        $pais_id = $lojadb->pais_id;
        $lojaNome = "Submarino";

        //Import. io
        //criação diretório desktop
        if(!is_dir("/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0])) {
            mkdir("/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0], 0777);
        }

        if(!is_dir("/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1])){
            mkdir("/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1], 0777);
        }
        //criacao diretório mobile
        if(!is_dir("/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0])) {
            mkdir("/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0], 0777);
        }

        if(!is_dir("/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1])){
            mkdir("/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1], 0777);
        }


        //Brasil_Submarino_Desktop_Homepage_Organic
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/85533748-b82a-44df-9a20-e82e219b570b?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2F%23%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Organic_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/jpg");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Organic_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        imagepng($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Organic_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        imagegif($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    }

                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //Brasil_Submarino_Desktop_Category_Organic
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/fbcd6449-d8a2-47b5-b279-2b5732f13693?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2Fcategoria%2Ftv-e-home-theater%2Ftv%2Ftv-led',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Desktop_Category_Organic_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/jpg");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."_Desktop_Category_Organic_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagepng($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Desktop_Category_Organic_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagegif($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    }

                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //Brasil_Submarino_Desktop_Homepage_Ads
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/8442b093-2471-44af-8075-a9d5c9d9874e?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Ads_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/jpg");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Ads_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        imagepng($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Ads_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        imagegif($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    }
                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //Brasil_Submarino_Desktop_Homepage_Carousel
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/52e67851-bb38-492c-b683-63c71aa234df?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Carousel_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/jpg");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."Desktop_Homepage_Carousel_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        imagepng($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Desktop_Homepage_Carousel_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        imagegif($im, "/var/www/html/retail/public/printshtml/brasil/desktop/".$dt[0]."/".$dt[1]."/".$name);
                    }
                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //****************************************MOBILE*********************************************
        //Brasil_Submarino_Mobile_Homepage_Organic
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/85533748-b82a-44df-9a20-e82e219b570b?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2F%23%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Organic_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/jpg");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Organic_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagepng($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Organic_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagegif($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    }

                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
                        'turno' => $turno,
                        'device' => 'Mobile',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //Brasil_Submarino_Mobile_Category_Organic
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/fbcd6449-d8a2-47b5-b279-2b5732f13693?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2Fcategoria%2Ftv-e-home-theater%2Ftv%2Ftv-led',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Mobile_Category_Organic_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."_Mobile_Category_Organic_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagepng($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Mobile_Category_Organic_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagegif($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    }

                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
                        'turno' => $turno,
                        'device' => 'Mobile',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //Brasil_Submarino_Mobile_Homepage_Ads
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/8442b093-2471-44af-8075-a9d5c9d9874e?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Ads_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Ads_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagepng($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Ads_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagegif($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    }
                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
                        'turno' => $turno,
                        'device' => 'Mobile',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;

        //Brasil_Submarino_Mobile_Homepage_Carousel
        $request = $client->request('GET', 'https://extraction.import.io/query/extractor/52e67851-bb38-492c-b683-63c71aa234df?_apikey=69b08086c8674326a53d8fd4a633d2e1a273e2ba260a9b39370e3019dd5397ec97f44c04dd3689aaca2d341ee9caab71434d3d5bae7c9f3be80fbaf63465aa2a3a1cb897066c9c10296a6181dc2dcf2e&url=https%3A%2F%2Fwww.submarino.com.br%2F',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type'=> 'application/json',
                    'Accept-Encoding' => 'gzip'
                ]
            ]);

        $request =  json_decode($request->getBody(), true);
        $dadosRequest = $request['extractorData']["data"];
        $position = 1;

        $urlTipo = $request['url'];

        foreach ( $dadosRequest as $dado):

            foreach ($dado["group"]  as $value):

                if (isset($value["Image"][0]["src"])) {

                    $imagem = $value["Image"][0]["src"];
                    $imagem = $value["Image"][0]["src"];
                    $extensao =  substr($imagem, -3);
                    $image = @file_get_contents($imagem);
                    $image= base64_encode($image);
                    $image = base64_decode($image);
                    if( $extensao = "jpg") {
                        $name = "Brasil_".$lojaNome."_Mobile_Homepage_Carousel_".$data.$turno.$position.".jpg";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagejpeg($im, "/var/www/html/retail/public/printshtml/brasil/mobiles/".$dt[0]."/".$dt[1]."/".$name);

                    } elseif( $extensao = "png") {
                        $name = "Brasil_".$lojaNome."Mobile_Homepage_Carousel_".$data.$turno.$position.".png";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagepng($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    } elseif( $extensao = "gif") {
                        $name = "Brasil_".$lojaNome."_MObile_Homepage_Carousel_".$data.$turno.$position.".gif";
                        $im = @imagecreatefromstring($image);
                        header("Content-type: image/png");
                        @imagegif($im, "/var/www/html/retail/public/printshtml/brasil/mobile/".$dt[0]."/".$dt[1]."/".$name);
                    }
                } else {
                    $imagem = "";
                    $name = "";
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
                } else {
                    $type = "";
                }

                DB::table('scraps')->insertGetId(
                    [
                        'pais_id' => $pais_id,
                        'tipo_pagina_id' => '1',
                        'categoria_id' => '1',
                        'marca_id' => '1',
                        'loja_id' => $loja_id,
                        'tipo_anuncio_id' => '1',
                        'turno' => $turno,
                        'device' => 'Mobile',
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
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                $position ++;
            endforeach;
        endforeach;
    }
}
