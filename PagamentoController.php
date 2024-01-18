<?php

namespace App\Http\Controllers;
use App\Models\Servico;

use Illuminate\Http\Request;

class PagamentoController extends Controller
{


    public function __construct(Servico $servico){
        $this->servico = $servico;
    }

    public function pagamentoPix(){

        $id_user = auth()->user();
        $curl = curl_init();

        $dados["transaction_amount"] = 0.1; 
        $dados["description"] = "Caderno de imagens"; 
        $dados["external_reference"] = "2"; 
        $dados["payment_method_id"] = "pix"; 
        $dados["notification_url"] = "https://moola.com.br/enquete/retorno/pix"; 
        $dados["payer"]["email"] = "teste@gmail.com"; 
        $dados["payer"]["first_name"] = "teste"; 
        $dados["payer"]["last_name"] = "User"; 
        $dados["payer"]["identification"]["type"] = "CPF"; 
        $dados["payer"]["identification"]["number"] = "28519203833"; 
    
        $dados["payer"]["address"]["zip_code"] = "17800000"; 
        $dados["payer"]["address"]["street_name"] = "Rua teste"; 
        $dados["payer"]["address"]["street_number"] = "17800000"; 
        $dados["payer"]["address"]["city"] = "Adamantina";
        $dados["payer"]["address"]["federal_unit"] = "SP"; 
    
        curl_setopt_array($curl,array(
            CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dados),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'content_type: application/json',
           
             //   'Authorization: Bearer TEST-6115832814602517-120700-6cc6bec11eba43ddf7e19adf6040d027-8520504'
                'Authorization: MEU TOKEN'
            ),
        ));
    
        $response = curl_exec($curl);
        $resultado = json_decode($response);
        curl_close($curl);
       // dd($resultado->status, $resultado->id);

        $servico = $this->servico->create([
            'id_user'=>$id_user->id,
            'descricao'=>'CADIMG',
            'tipo'=>'im',
            'status'=>$resultado->status,
            'id_transaction'=>$resultado->id
          
        ]);
       $servico->save();
       return view('pagamento')->with(compact('resultado'));
    }

    public function retornoPix(){
           
       $collector_id = $_POST["id"];
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/'.$collector_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",

        CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'Authorization: MEU TOKEN'
        ),
    ));
        $response = curl_exec($curl);
        $resultado = json_decode($response);
        curl_close($curl);

        //Atualizando o banco
        $notifica = Servico::where('id_transaction',$resultado->id)->first();
        $notifica->status = $resultado->status;
        $notifica->save();
    }
}
