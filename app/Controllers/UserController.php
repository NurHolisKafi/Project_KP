<?php

namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class UserController extends BaseController
{
    use ResponseTrait;
    protected $model;
    public function __construct() {
        $this->model = new UserModel();
        helper('form');
    }

    public function tes(){
        // $id = $this->request->getPost('id');
        // $jumlah = $this->request->getPost('jumlah');
        
        // $a = array();
        // dd(\Midtrans\Transaction::status('395756181'));
        
    }


    

    //VIEW HOME PAGE
    public function index(){
        $result = [
            'all_produk' => $this->model->view_produk(),
            'featured_produk' => $this->model->view_featuredproduk(),
            'category' => $this->model->view_category()
        ];
        return view('Pages/User/Main_page/home',$result);
    }
    
    public function Login(){
        return view('Pages/User/Main_page/login',['session'=>$this->session]);
    }
    
    public function Register(){

        return view('Pages/User/Main_page/register',['validation' => $this->validation]);
    }

    public function Single_produk(){
        $id = $this->request->getGet('id');
        $result = [
            'img' => $this->model->view_img($id),
            'detail' => $this->model->detail_produk($id),
            'featured_produk' => $this->model->view_featuredproduk(),
        ];
        return view('Pages/User/Main_page/single_produk',$result);
    }

    public function Keranjang(){
        $result = [
            'data' => $this->model->view_keranjang($this->session->get('data')['id_user'])
        ];
        return view('Pages/User/Main_page/keranjang',$result);
    }
    
    public function Checkout(){
        
        $id = $this->request->getPost('id');
        $total_harga = 0;
        $berat = 0;
        if ($this->request->getPost('status') != null) {
            $produk =  $this->model->view_produkById($id);
            $jumlah = $this->request->getPost("jumlah");
            $total_harga += $produk['harga'] * $jumlah;
            $berat += $produk['berat'];
            $data_pesanan = [
                [
                    'id' => $produk['id_produk'],
                    'nama' => $produk['nama'],
                    'jumlah' => $jumlah,
                    'total' => $produk['harga'] * $jumlah
                ]
            ];
            $result = [
                'data_pesanan' => $data_pesanan,
                'provinsi' => $this->model->getAddress("province"),
                'total_harga' => $total_harga,
                'berat' => $berat,
            ];
    
            return view('Pages/User/Main_page/order',$result);
        }else {

            if ($id == null) {
                return redirect()->back()->with('error','Checkbox tidak boleh kosong');
            }
            $data_pesanan = array();
            for ($b=0; $b<count($id); $b++){
                $produk =  $this->model->view_keranjangCheckout($id[$b]);
                $jumlah = $this->request->getPost("jumlah".$produk['id_keranjang']);
                $total_harga += $produk['harga'] * $jumlah;
                $berat += $produk['berat'];
                $data = [
                    'id' => $produk['id_produk'],
                    'nama' => $produk['nama'],
                    'jumlah' => $jumlah,
                    'total' => $produk['harga'] * $jumlah
                ];
                array_push($data_pesanan,$data);
            };
            $result = [
                'data_pesanan' => $data_pesanan,
                'provinsi' => $this->model->getAddress("province"),
                'total_harga' => $total_harga,
                'berat' => $berat,
            ];
    
            return view('Pages/User/Main_page/order',$result);
        }
    }
    
    public function Search(){
        $nama = $this->request->getPost('nama');
        if ($nama == "") {
            $nama = 0;
        }
        // dd($nama);
        $result = [
            'all_produk' => $this->model->view_produkByName($nama)
        ];
        return view('Pages/User/search',$result);
    }
    

    // VIEW AKUN PAGE
    public function Profile(){
        $data = [
            'session' => $this->session,
            'user' => $this->model->view_user($this->session->get('data')['id_user'])
        ];
        return view('Pages/User/Profile_page/home',$data);
    }

    public function History(){
        return view('Pages/User/Profile_page/history');
    }

    public function Order_list(){
        $data =[
            'order' => $this->model->view_order($this->session->get('data')['id_user']),
        ];
        return view('Pages/User/Profile_page/order',$data);
    }

    public function Edit_profile(){
        $data = [
            'validate' => $this->validation,
            'session' => $this->session,
            'user' => $this->model->view_user($this->session->get('data')['id_user'])
        ];
        return view('Pages/User/Profile_page/form_profile',$data);
    }

    public function Edit_pass(){
        $data = [
            'session' => $this->session
        ];
        return view('Pages/User/Profile_page/change_pass',$data);
    }

    public function Invoice(){
        $id = $this->request->getGet('id');
        $status = \Midtrans\Transaction::status($id);
        $data = [
            'id' => $id,
            'pemesan' => $this->model->get_invoice($id),
            'produk' => $this->model->view_detailOrder($id),
            'waktu' => $status->transaction_time,
            'metode' => $status->payment_type,
        ];
        return view('pages/User/Profile_page/invoice',$data);
    }

    //Insert
    public function Add_User(){
        if(!$this->validate([
            'username' => [
                'rules' => 'is_unique[users.nama]',
                'errors' => [
                    'is_unique' => '{field} sudah ada'
                ]
            ],
            'email' => [
                'rules' => 'valid_emails',
                'errors' => [
                    'valid_emails' => '{field} tidak valid'
                ]
            ]
        ])){
            return redirect()->back()->withInput();
        };

        $nama = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $notelp = $this->request->getPost('notelp');
        $pass = $this->request->getPost('pass');
        $this->model->add_User($nama,$email,$notelp,$pass);
        return redirect()->to('/login');
    }

    public function Add_keranjang(){
        $id_produk = $this->request->getPost('id');
        $total = $this->request->getPost('jumlah');
        $id_user = $this->session->get('data')['id_user'];
        $hasil = $this->model->add_keranjang($id_produk,$total,$id_user);
        return redirect()->back()->with('success','Berhasil ditambahkan ke keranjang');
    }

    //UPDATE
    public function Update_profile(){
        if(!$this->validate([
            'gambar' => [
                'rules' => 'is_image[gambar]|max_size[gambar,5120]|mime_in[gambar,image/png,image/jpg,image/jpeg]',
                'errors' => [
                    'is_image' => 'File upload bukan gambar',
                    'nime_in' => 'File upload bukan gambar',
                    'max_size' => 'Ukuran gambar terlalu besar'
                ]
            ]
        ])){
            // dd($this->validation);
            return redirect()->back()->withInput();
        };
        $id = $this->request->getpost('id');
        $file_gambar = $this->model->view_user($id)['gambar'];
        if ($file_gambar != 'user.png') {
            unlink('assets/img2/profile/' .$file_gambar);
        }
        $nama = $this->request->getpost('nama');
        $alamat = $this->request->getpost('alamat');
        $email = $this->request->getpost('email');
        $notelp = $this->request->getpost('notelp');
        $gambar = $this->request->getfile('gambar');
        if ($gambar->getName() == '') {
            $this->model->update_profile($id,$nama,$email,$notelp,$alamat,'');
        }else {
            $nama_gambar = $gambar->getName();
            $gambar->move('assets/img2/profile',$nama_gambar);
            $this->model->update_profile($id,$nama,$email,$notelp,$alamat,$nama_gambar);
        }

        return redirect()->to('/myprofile')->with('success','Data berhasil diperbarui');
    }

    public function Update_pass(){
        $id = $this->request->getpost('id');
        $currpass = $this->request->getpost('currpass');
        $newpass = $this->request->getpost('newpass');
        $this->model->update_pass($id,$currpass,$newpass);
        return redirect()->to('/editprofile')->with('success','Password berhasil diubah');
    }

    //DELETE
    public function Delete_keranjang(){
        $id = $this->request->getGet('id');
        $this->model->delete_keranjang($id);
        return redirect()->back();
    }

    public function Delete_order($id_order){
        $hasil = \Midtrans\Transaction::cancel($id_order);
        $this->model->update_order($id_order,5);
        return redirect()->back();
    }

    //Data
    public function DataCity(){
        $id = $this->request->getPost('id');
        $data = $this->model->getAddress("city?province=" . $id);
        
        echo "<option selected='selected' disabled>-- Pilih --</option>";
        foreach ($data as $key) {
            echo "<option value=".$key['city_id']." postal_code=".$key['postal_code']." nama=".$key['city_name'].">";
            echo $key['type']." ".$key['city_name'];
            echo "</option>";
        }
    }

    public function DataKurir(){
        echo"<option selected='selected' disabled>-- Pilih --</option>
        <option value='jne'>JNE</option>
        <option value='pos'>POS Indonesia</option>
        <option value='tiki'>TIKI</option>";
    }

    public function DataCost(){
        $tujuan = $this->request->getPost('tujuan');
        $berat = $this->request->getPost('berat');
        $kurir = $this->request->getPost('kurir');
        $data = $this->model->getCost($tujuan,$berat,$kurir);
        foreach ($data['costs'] as $key) {
            echo "<div class='col-sm-6 mb-3'>
                    <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$key['description']. "</h5>
                        <p class='card-text value'>".$key['cost'][0]['value']."</p>
                        <p class='card-text'>Estimasi : ";
            if ($data['code'] == 'pos'){
                echo $key['cost'][0]['etd']."</p>";
            }else{
                echo $key['cost'][0]['etd']." Hari </p>";
            }
            
            echo "</div>
                    </div>
                  </div>";
        }
    }

    public function Data_detailOrder($id_order){
        $detail = $this->model->view_detailOrder($id_order);
        $no=1;
        foreach ($detail as $key) {
            echo "<tr>
                  <td>".$no."</td>
                  <td>".$key['nama']."</td>
                  <td>".$key['jumlah']."</td>
                </tr>";
            $no++;
        }
    }

    public function Data_statusOrder($id_order){
        $result = [
            'status' => \Midtrans\Transaction::status($id_order)->transaction_status,
        ];

        return $this->respond($result);
    }

    public function Data_ProdukByCategory(){
        $id = $this->request->getPost('id');
        if ($id !=0) {
            $hasil = $this->model->view_produkByCategory($id);
        } else {
            $hasil = $this->model->view_featuredproduk();
        }
        foreach($hasil as $key){
            echo "<div class='col mb-5'>
                    <div class='card' id='card-produk'>
                    <img src='/assets/img/".$key['gambar']."' class='card-img-top' height='210x'>
                    <div class='card-body text-center'>
                        <h6 class='card-title'>".$key['nama']."</h6>
                        <div class='icon-bintang' style='color: orange;'>
                        <i class='fa-solid fa-star'></i>
                        <i class='fa-solid fa-star'></i>
                        <i class='fa-solid fa-star'></i>
                        <i class='fa-solid fa-star'></i>
                        <i class='fa-solid fa-star-half-stroke'></i>
                        </div>
                        <p class='card-text mt-2'>Rp. ".$key['harga']."</p>
                        <div class='card-footer p-4 pt-0 border-top-0 bg-transparent'>
                        <div class='text-center'><a class='btn btn-outline-primary mt-auto d-grid' href='/detail?id=".$key['id_produk']."'>Beli</a></div>
                        </div>
                    </div>
                    </div>
                </div>";
        }
    }
}