<?php
include('../../includes/header.php');

// Ambil parameter halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Ambil parameter pencarian
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Jumlah item per halaman
$itemsPerPage = 10;

// Hitung offset
$offset = ($page - 1) * $itemsPerPage;

// Query untuk mengambil data produk dengan filter pencarian
$query = "SELECT * FROM produk WHERE
          Nama_Produk LIKE :searchTerm OR
          Kategori LIKE :searchTerm OR
          Deskripsi LIKE :searchTerm
          LIMIT $offset, $itemsPerPage";

// Hitung total hasil pencarian (tanpa LIMIT)
$countQuery = "SELECT COUNT(*) as total FROM produk WHERE
               Nama_Produk LIKE :searchTerm OR
               Kategori LIKE :searchTerm OR
               Deskripsi LIKE :searchTerm";

// Prepare statements
$stmt = $conn->prepare($query);
$countStmt = $conn->prepare($countQuery);

// Bind parameter pencarian
$searchParam = "%$searchTerm%";
$stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
$countStmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);

// Eksekusi query untuk mendapatkan hasil pencarian
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eksekusi query untuk mendapatkan total hasil pencarian
$countStmt->execute();
$totalResults = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Hitung total halaman
$totalPages = ceil($totalResults / $itemsPerPage);

// Tampilkan hasil pencarian
?>

<!-- Formulir Pencarian -->
<form action="index.php" method="GET">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Cari produk..." name="search" value="<?= $searchTerm ?>">
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </div>
</form>

<!-- Tabel Produk -->
<table class="table table-bordered">
    <!-- Header Tabel -->
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Deskripsi</th>
            <!-- Tambahkan kolom lain sesuai kebutuhan -->
        </tr>
    </thead>
    <!-- Data Produk -->
    <tbody>
        <?php foreach ($products as $index => $product) : ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= $product['Nama_Produk'] ?></td>
                <td><?= $product['Kategori'] ?></td>
                <td><?= $product['Deskripsi'] ?></td>
                <!-- Tambahkan kolom lain sesuai kebutuhan -->
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination -->
<ul class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="index.php?page=<?= $i ?>&search=<?= $searchTerm ?>"><?= $i ?></a>
        </li>
    <?php endfor; ?>
</ul>

<?php include('../../includes/footer.php'); ?>
