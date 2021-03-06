<?= $this->extend('template/User/main') ?>
<?= $this->section('content') ?> 

<style>
  .figure-img:hover{
    cursor: pointer;
  }
</style>

<!-- Singleproduk -->
<div class="container bg-white mt-5 p-3">
  <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <?= $_SESSION['success']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  <div class="row justify-content-center single-produk">
      <div class="col-lg-5 ">
          <figure class="figure">
              <img src="/assets/img/<?=$img[0]['nama'];?>" class="figure-img img-fluid" alt="..." width="410px" style="height: 300px;">
              <figcaption class="figure-caption justify-content-center d-flex">
                <?php foreach($img as $key): ?>
                  <img src="/assets/img/<?=$key['nama'];?>" class="figure-img img-fluid me-2" alt="..." width="70px">
                <?php endforeach; ?>
              </figcaption>
            </figure>
      </div>
      <div class="col-lg-7">
          <h4><?=$detail['nama'];?></h4>
          <div class="garis-nama"></div>
          <p style="font-size: x-large;" id="view-harga"><?=$detail['harga'];?></p>
          <div id="jumlah" class="mt-3">
              <button onclick="kurang()" type="button" class="btn btn-light border btn-sm shadow-none" id="minus" ><i class="fa-solid fa-minus"></i></button>
              <span class="mx-2" style="font-size: larger;">1</span>
              <button onclick="tambah()" type="button" class="btn btn-light border btn-sm shadow-none" id="plus"><i class="fa-solid fa-plus"></i></button>
              <span class="mx-2">tersisa <?=$detail['stok'];?> buah</span>
          </div>
          <?= form_open('/UserController/Add_keranjang'); ?>
          <?= form_hidden('id',$detail['id_produk']); ?>
          <input type="hidden" name="status" value="beli_sekarang">
          <input type="hidden" name="jumlah" id="total" value="1">
          <div class="btn-produk mt-5">
              <button class="btn btn-danger text-white btn-lg me-2 btn-custom"><i class="fa-solid fa-cart-shopping me-2" data-bs-toggle="modal" data-bs-target="#notif"></i>Masukkan Keranjang</button>
              <button class="btn btn-primary text-white btn-lg me-2 btn-custom" id="btn_beli" >Beli Sekarang</button>
          </div>
          <div class="mt-4">
            <div class="card deskripsi border border-dark">
              <div class="card-body">
                <h5 class="card-title fw-bold border-bottom">Rincian Produk</h5>
                <?=$detail['deskripsi'];?>
              </div>
            </div>
          </div>
      </div>
  </div>
</div>

<!-- Related items section-->
  <section class="py-5 bg-light mt-5">
    <div class="container px-4 px-lg-5 mt-5">
        <h2 class="fw-bolder mb-4">Related products</h2>
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
          <?php foreach($featured_produk as $key): ?>
          <div class="col mb-5">
            <div class="card" id="card-produk">
              <img src="/assets/img/<?=$key['gambar'];?>" class="card-img-top" alt="..." height="210x">
              <div class="card-body text-center">
                <h6 class="card-title"><?=$key['nama'];?></h6>
                <div class="icon-bintang" style="color: orange;">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <p class="card-text mt-2" id="view-harga"><?=$key['harga'];?></p>
                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                  <div class="text-center"><a class="btn btn-outline-primary mt-auto d-grid" href="/detail?id=<?=$key['id_produk'];?>">Beli</a></div>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
    </div>
  </section>


  <script>
    var angka = document.querySelector('#jumlah span');
    var plus = document.querySelector('#plus');
    var total = document.querySelector('#total');
    var a = angka.textContent;   

    const container = document.querySelector('figcaption');
    const jumbo = document.querySelector('.figure-img');
    const thumbs = document.querySelectorAll('figcaption .figure-img');

    container.addEventListener('click', function (e) {
      console.log(e);
      if(e.target.className == 'figure-img img-fluid me-2'){
        jumbo.src = e.target.src;
        jumbo.classList.add('fade');
        
        setTimeout(function() {
          jumbo.classList.remove('fade');
        }, 500); 
      }
    });
    
    $('#btn_beli').on('click',function(e) {
      e.preventDefault();
      $('form').attr('action','/checkout')
      $('form').submit();
    })
    

    //untuk format view harga
    function view(harga){
      let a = String(harga).split("").reverse();
      for (let b = 0; b < a.length; b++) {
          if ((b + 1) % 3 == 0 && b != a.length - 1) {
              a[b] = `.${a[b]}`;
          }
      }
      hasil = a.reverse().join("");
      return `Rp ${hasil}`
    }

    function change(){
      angka.textContent=a;
      total.value = a;
      $('#jum_hidden').val(a);
    }

    function tambah(){
      a++;
      change();
    }

    function kurang(){
      if(a > 1){
        a--;
        change();  
      }
    }
  </script>

<?= $this->endSection() ?>