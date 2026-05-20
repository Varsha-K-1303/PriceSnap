<?php
// history.php
session_start();

// Require login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$history = isset($_SESSION['history']) ? $_SESSION['history'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View History - PriceSnap</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<style>
body {
  background: #0b0b0c;
  color: #e6eef3;
  font-family: Segoe UI, sans-serif;
}
.container {
  margin-top: 40px;
}
.card {
  background: #111213;
  border: 1px solid #1b1b1c;
  border-radius: 12px;
}
.thumb {
  max-width: 80px;
  border-radius: 8px;
  border: 1px solid #151515;
}
.table-dark th, .table-dark td {
  vertical-align: middle;
}
.btn-back {
  background: #00bcd4;
  color: #000;
  border: none;
  border-radius: 8px;
  padding: 6px 12px;
}
.btn-back:hover {
  background: #0097a7;
}
</style>
</head>
<body>
<div class="container">
  <h2 class="mb-4">Browsing History</h2>
  <a href="index.php" class="btn-back mb-3">← Back to Home</a>

  <?php if (empty($history)): ?>
    <div class="alert alert-secondary">No history yet. View some products to see them here!</div>
  <?php else: ?>
    <div class="card p-3">
      <table class="table table-dark table-striped">
        <thead>
          <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Amazon Price</th>
            <th>Flipkart Price</th>
            <th>Viewed At</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($history as $item): ?>
          <tr>
            <td>
              <?php if (!empty($item['image'])): ?>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" class="thumb">
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($item['title']); ?></td>
            <td><?php echo isset($item['amazon_price']) ? htmlspecialchars($item['amazon_price']) : 'N/A'; ?></td>
            <td><?php echo isset($item['flipkart_price']) ? htmlspecialchars($item['flipkart_price']) : 'N/A'; ?></td>
            <td><?php echo htmlspecialchars($item['viewed_at']); ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
