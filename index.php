<?php
session_start();

// require login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// visitor counter (safe-ish)
$counterFile = 'counter.txt';
if (!file_exists($counterFile)) file_put_contents($counterFile, '0');
$count = (int)file_get_contents($counterFile);
$count++;
file_put_contents($counterFile, (string)$count);

// load products
include 'products.php';

// search
$search = '';
if (isset($_GET['search'])) {
    $search = strtolower(trim($_GET['search']));
}

// filter & group (products already unique by title in this list)
$filtered = [];
foreach ($products as $p) {
    if ($search === '' || strpos(strtolower($p['title']), $search) !== false) {
        $filtered[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PriceSnap — Compare (Amazon vs Flipkart)</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<style>
:root{
  --bg:#0b0b0c; --panel:#111214; --muted:#9aa0a6; --accent:#cdd6db;
  --green:#7ef0a1; --teal:#00bcd4;
}
body{background:linear-gradient(180deg,var(--bg),#070708); color:var(--accent); font-family:Segoe UI, sans-serif;}
.topbar{background:linear-gradient(90deg,#070708,#0f0f10); border-bottom:1px solid #171717; padding:14px 20px;}
.brand{font-weight:800; font-size:1.2rem;}
.search-box input{background:#0f0f10;border:1px solid #222;color:var(--accent);border-radius:25px;padding:10px 16px;width:320px;}
.search-box button{background:var(--teal);border:none;padding:9px 14px;border-radius:25px;margin-left:8px;}
.table-panel{background:var(--panel); border-radius:12px; padding:18px; border:1px solid #151515;}
.product-thumb{width:72px;height:72px;object-fit:cover;border-radius:8px; border:1px solid #1a1a1a;}
.table thead th{border-bottom:1px solid #222;color:#cfd6da;}
.price-cell{font-weight:700;color:var(--green);}
.link-btn{background:transparent;border:1px solid #202022;color:var(--accent);padding:6px 10px;border-radius:8px;}
.link-btn:hover{background:#131315;color:#fff;border-color:#2b2b2b;}
@media (max-width:900px){
  .search-box input{width:180px;}
  .product-thumb{width:56px;height:56px;}
  .table-responsive{font-size:0.95rem;}
}
</style>
</head>
<body>

<div class="topbar d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <div class="brand"> PriceSnap</div>
    <div style="margin-left:18px;color:var(--muted)">Compare Amazon vs Flipkart</div>
  </div>

  <form class="search-box d-flex" method="get" action="">
    <input name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">🔍</button>
  </form>

  <div class="text-right" style="color:var(--muted)">
    <div>Welcome, <?php echo isset($_SESSION['username'])?htmlspecialchars($_SESSION['username']):'Guest'; ?></div>
    <div style="font-size:0.9rem;margin-top:6px">Visitors: <strong style="color:#fff"><?php echo $count; ?></strong></div>
  </div>
</div>

<div class="container mt-4 mb-5">
  <div class="table-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 style="margin:0">Price Comparison</h5>
      <div>
        <a href="history.php" class="btn btn-sm btn-secondary mr-2">History</a>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-dark table-striped mb-0">
        <thead>
          <tr>
            <th style="width:40%">Product</th>
            <th style="width:18%">Amazon</th>
            <th style="width:18%">Flipkart</th>
            <th style="width:24%">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($filtered)): ?>
          <tr><td colspan="4" class="text-center text-muted">No products matched "<?php echo htmlspecialchars($search); ?>"</td></tr>
        <?php else: ?>
          <?php foreach ($filtered as $p): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" class="product-thumb mr-3">
                  <div>
                    <div style="font-weight:700;color:#fff"><?php echo htmlspecialchars($p['title']); ?></div>
                    <div style="color:var(--muted);font-size:0.92rem">Category: General</div>
                  </div>
                </div>
              </td>

              <td class="align-middle">
                <div class="price-cell"><?php echo htmlspecialchars($p['amazon_price']); ?></div>
                <div style="font-size:0.85rem;color:var(--muted);margin-top:6px">
                  <a class="link-btn" href="<?php echo htmlspecialchars($p['amazon_link']); ?>" target="_blank">View on Amazon</a>
                </div>
              </td>

              <td class="align-middle">
                <div class="price-cell"><?php echo htmlspecialchars($p['flipkart_price']); ?></div>
                <div style="font-size:0.85rem;color:var(--muted);margin-top:6px">
                  <a class="link-btn" href="<?php echo htmlspecialchars($p['flipkart_link']); ?>" target="_blank">View on Flipkart</a>
                </div>
              </td>

              <td class="align-middle">
                <a href="product.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-light">Open product</a>
                <a href="product.php?id=<?php echo (int)$p['id']; ?>&from=amazon" class="btn btn-sm btn-custom" style="background:var(--teal);color:#000;margin-left:6px">Buy</a>
                <div style="margin-top:8px;color:var(--muted);font-size:.85rem">ID: <?php echo (int)$p['id']; ?></div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

</body>
</html>
