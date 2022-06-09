<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{

    //VIEW

    public function view_produk(){
        $table = $this->db->table('produk');
        $table->select('produk.id_produk,produk.nama as nama,harga,gambar.nama as gambar');
        $table->join('gambar', 'gambar.id_produk = produk.id_produk');
        $table->groupBy("produk.id_produk");
        $query = $table->get()->getResultArray();
        return $query;
    }

    public function view_produkById($id){
        $table = $this->db->table('produk');
        $table->select('produk.id_produk,produk.nama as nama,harga,berat');
        $table->where('id_produk',$id);
        return $table->get()->getRowArray();
    }

    public function view_category(){
        $table = $this->db->table('category');
        return $table->get()->getResultArray();
    }

    public function view_featuredproduk(){
        $table = $this->db->table('produk');
        $table->select('produk.id_produk,produk.nama as nama,harga,gambar.nama as gambar');
        $table->join('gambar', 'gambar.id_produk = produk.id_produk');
        $table->groupBy("produk.id_produk");
        $table->limit(5);
        $query = $table->get()->getResultArray();
        return $query;
    }

    public function view_img($id){
        $table = $this->db->table('gambar');
        $table->select('id_gambar,nama');
        $table->where('id_produk',$id);
        $query = $table->get()->getResultArray();
        return $query;
    }

    public function view_keranjang($id){
        $table = $this->db->table('keranjang');
        $table->select('id_keranjang,produk.id_produk,produk.nama as nama,gambar.nama as gambar,jumlah,produk.harga as harga');
        $table->join('produk','keranjang.id_produk = produk.id_produk');
        $table->join('gambar', 'gambar.id_produk = produk.id_produk');
        $table->where('keranjang.id_user',$id);
        $table->groupBy("keranjang.id_keranjang");
        $query = $table->get()->getResultArray();
        return $query;
    }

    public function detail_produk($id){
        $table = $this->db->table('produk');
        $table->select('id_produk,nama,harga,stok,deskripsi');
        $table->where('id_produk',$id);
        $query = $table->get()->getRowArray();
        return $query;
    }

    public function view_order(){
        return 'halo';
    }

    // INSERT
    public function add_user($nama,$email,$notelp,$pass){
        $table = $this->db->table('users');
        $data = [
            'nama' => $nama,
            'email' => $email,
            'notelp' => $notelp,
            'password' => $pass
        ];
        $table->set($data);
        $query = $table->insert();
        return $query;
    }

    public function add_keranjang($id,$jumlah,$id_user){
        $table = $this->db->table('keranjang');
        $data = [
            'id_produk' => $id,
            'jumlah' => $jumlah,
            'id_user' => $id_user
        ];
        $table->set($data);
        $query = $table->insert();
        return $query;
    }

    public function add_detailOrder($id_order,$id_produk,$jum){
        $table = $this->db->table('detail_order');
        $data = [
            'id_order' => $id_order,
            'id_produk' => $id_produk,
            'jumlah' => $jum
        ];
        $table->set($data);
        return $table->insert();
    }

    public function add_order($id_order,$id_user,$total,$waktu,$metode,$status,$va_number = 'Tidak ada',$pdf = 'Tidak ada'){
        $table = $this->db->table('orders');
        $data = [
            'id_users' => $id_user,
            'id_order' => $id_order,
            'total' => $total,
            'waktu' => $waktu,
            'metode_pembayaran' => $metode,
            'status' => $status,
            'va_number' => $va_number,
            'pdf_url' => $pdf,
        ];
        $table->set($data);
        return $table->insert();
    }

    //DELETE
    public function delete_keranjang($id){
        $table = $this->db->table('keranjang');
        $table->where('id_keranjang',$id);
        return $table->delete();
    }



    //DATA
    public function getAddress($param){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.rajaongkir.com/starter/" . $param,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key: fb456c59e23859f60ef66b8c599081de"
        ),
        ));

        $response = json_decode(curl_exec($curl),true);
        $err = curl_error($curl);
        curl_close($curl);

        $result = $response['rajaongkir']['results'];
        return $result;
    }

    public function getCost($destinasi,$berat,$kurir){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "origin=133&destination={$destinasi}&weight={$berat}&courier={$kurir}",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: fb456c59e23859f60ef66b8c599081de"
        ),
        ));

        $response = json_decode(curl_exec($curl),true);
        $err = curl_error($curl);
        curl_close($curl);

        $result = $response['rajaongkir']['results'][0];
        return $result;
    }

    public function get_keranjang($id){
        $table = $table = $this->db->table('keranjang');
        $table->selectCount('id_produk');
        $table->where('id_user',$id);
        return $table->get()->getRowArray();
    }

    
}