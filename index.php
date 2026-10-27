
<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop | Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar">
        <a class="brand" href="index.php">
            coffee<span>shop.</span>
        </a>

        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="customers/view_customers.php">Customers</a>
            <a href="orders/view_orders.php">Orders</a>

            <?php if ($isLoggedIn): ?>
                <a href="logout.php">Log out</a>
            <?php else: ?>
                <a href="login.php">Log in</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="container">
            <p class="eyebrow">Your coffee shop management system</p>

            <div class="hero-copy">
                <h1>A little more order<br>behind every cup.</h1>

                <p>
                    Keep customer information and orders organized
                    in one place, so you can spend less time sorting
                    through records and more time running the shop.
                </p>

                <div class="hero-actions">
                    <a class="btn" href="customers/view_customers.php">
                        View customers
                    </a>

                    <a class="btn btn-outline" href="orders/view_orders.php">
                        View orders
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">The essentials</p>
                <h2>Everything in its place.</h2>
                <p>Simple tools for the everyday work of a coffee shop.</p>
            </div>

            <div class="service-grid">
                <article class="service-item">
                    <span class="service-number">01 / Customers</span>
                    <h3>Know your customers</h3>
                    <p>
                        Keep customer names and contact details
                        organized and easy to find.
                    </p>
                    <a href="customers/view_customers.php">
                        Browse customers →
                    </a>
                </article>

                <article class="service-item">
                    <span class="service-number">02 / Orders</span>
                    <h3>Keep orders together</h3>
                    <p>
                        Review order dates, products, and the
                        customers connected to each order.
                    </p>
                    <a href="orders/view_orders.php">
                        Browse orders →
                    </a>
                </article>

                <article class="service-item">
                    <span class="service-number">03 / Account</span>
                    <h3>Your workspace</h3>
                    <p>
                        <?php if ($isLoggedIn): ?>
                            You are signed in. Continue managing
                            your shop's records.
                        <?php else: ?>
                            Sign in to your account to access
                            your personalized workspace.
                        <?php endif; ?>
                    </p>

                    <?php if ($isLoggedIn): ?>
                        <a href="customers/add_customers.php">
                            Add a customer →
                        </a>
                    <?php else: ?>
                        <a href="login.php">Sign in to continue →</a>
                    <?php endif; ?>
                </article>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <p>coffee shop. &mdash; Made for the daily routine.</p>
        <p>Customer &amp; order management</p>
    </div>
</footer>

</body>
</html>
