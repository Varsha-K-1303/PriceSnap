<?php
// product.php
session_start();

// require login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Load products
include 'products.php';

// Get product id from query
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$from = isset($_GET['from']) ? $_GET['from'] : '';

// Find product by id
$found = null;
foreach ($products as $p) {
    if (isset($p['id']) && (int)$p['id'] === $id) {
        $found = $p;
        break;
    }
}

if (!$found) {
    // product not found
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Product not found</title></head><body style='background:#111;color:#fff;font-family:Segoe UI;padding:40px;'><h2>Product not found</h2><p>The requested product ID ({$id}) was not found.</p><p><a href='index.php' style='color:#00bcd4'>Back to PriceSnap</a></p></body></html>";
    exit;
}

// Record to session history (store only useful fields)
$entry = [
    'id' => $found['id'],
    'title' => $found['title'],
    'image' => isset($found['image']) ? $found['image'] : '',
    'amazon_price' => isset($found['amazon_price']) ? $found['amazon_price'] : '',
    'flipkart_price' => isset($found['flipkart_price']) ? $found['flipkart_price'] : '',
    'amazon_link' => isset($found['amazon_link']) ? $found['amazon_link'] : '',
    'flipkart_link' => isset($found['flipkart_link']) ? $found['flipkart_link'] : '',
    'viewed_at' => date('Y-m-d H:i:s'),
    'from' => $from
];

// Initialize history array if missing
if (!isset($_SESSION['history']) || !is_array($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

// append (keep last 50 only)
array_unshift($_SESSION['history'], $entry);
if (count($_SESSION['history']) > 50) {
    array_splice($_SESSION['history'], 50);
}

// Helper: compare prices (attempt numeric comparison by stripping non-digits)
function price_to_number($s) {
    if (!$s) return null;
    // remove rupee symbol, commas, non-numeric
    $n = preg_replace('/[^\d\.]/', '', $s);
    if ($n === '') return null;
    return (float)$n;
}

$amazon_price_raw = isset($found['amazon_price']) ? $found['amazon_price'] : 'N/A';
$flipkart_price_raw = isset($found['flipkart_price']) ? $found['flipkart_price'] : 'N/A';
$amazon_link = isset($found['amazon_link']) ? $found['amazon_link'] : '#';
$flipkart_link = isset($found['flipkart_link']) ? $found['flipkart_link'] : '#';

$amp = price_to_number($amazon_price_raw);
$ffp = price_to_number($flipkart_price_raw);

$amazon_is_lower = ($amp !== null && $ffp !== null) ? ($amp < $ffp) : false;
$flipkart_is_lower = ($amp !== null && $ffp !== null) ? ($ffp < $amp) : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($found['title']); ?> — PriceSnap</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<style>
body{background:#0b0b0c;color:#e6eef3;font-family:Segoe UI, sans-serif}
.container{margin-top:36px}
.card{background:#111213;border:1px solid #1b1b1c;border-radius:12px}
.price {font-size:1.3rem;font-weight:700}
.price.amazon {color:<?php echo $amazon_is_lower ? '#7ef0a1' : '#cfd6da'; ?>}
.price.flipkart {color:<?php echo $flipkart_is_lower ? '#7ef0a1' : '#cfd6da'; ?>}
.btn-buy{background:#00bcd4;color:#000;border:none;border-radius:8px;padding:8px 14px}
.btn-buy:hover{background:#0097a7}
.back-link{color:#cfd6da}
.meta{color:#9aa0a6;font-size:0.9rem}
.thumb{max-width:300px;border-radius:10px;border:1px solid #151515}
</style>
</head>
<body>
<div class="container">
  <a href="index.php" class="back-link">&larr; Back to PriceSnap</a>
  <div class="row mt-3">
    <div class="col-md-5">
      <div class="card p-3 text-center">
        <img src="<?php echo htmlspecialchars($found['image']); ?>" alt="" class="thumb mb-3">
        <h4 style="color:#fff"><?php echo htmlspecialchars($found['title']); ?></h4>
        <div class="meta">Viewed at <?php echo htmlspecialchars($entry['viewed_at']); ?></div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card p-3">
        <h5>Price Comparison</h5>
        <div class="row mt-3">
          <div class="col-sm-6">
            <div class="mb-2">Amazon</div>
            <div class="price amazon"><?php echo htmlspecialchars($amazon_price_raw); ?></div>
            <div class="mt-2">
              <a href="<?php echo htmlspecialchars($amazon_link); ?>" target="_blank" class="btn-buy">Buy on Amazon</a>
            </div>
            <?php if ($amazon_is_lower): ?>
              <div class="meta mt-2">Cheaper on Amazon ✅</div>
            <?php endif; ?>
          </div>

          <div class="col-sm-6">
            <div class="mb-2">Flipkart</div>
            <div class="price flipkart"><?php echo htmlspecialchars($flipkart_price_raw); ?></div>
            <div class="mt-2">
              <a href="<?php echo htmlspecialchars($flipkart_link); ?>" target="_blank" class="btn-buy">Buy on Flipkart</a>
            </div>
            <?php if ($flipkart_is_lower): ?>
              <div class="meta mt-2">Cheaper on Flipkart ✅</div>
            <?php endif; ?>
          </div>
        </div>

        <hr style="border-color:#151515">

        <div class="meta">
          Product ID: <?php echo (int)$found['id']; ?> &nbsp; | &nbsp;
          <?php if ($from): ?>
            Came from: <?php echo htmlspecialchars($from); ?>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>
</body>
</html>
