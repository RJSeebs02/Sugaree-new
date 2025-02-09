<?php
include 'config/config.php';
include_once 'class/class.user.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

/* Define Object */
$user = new User();

/* Checks if the user is logged in */
if(!$user->get_session()){
    $user_identifier = "N/A";
} else {
    $user_identifier = $_SESSION['user_identifier'];
}

$user_id = $user->get_user_id($user_identifier);
$user_role = $user->get_user_role($user_id);


try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($category) {
        $stmtCategoryDishes = $pdo->prepare("SELECT * FROM tbl_dishes WHERE dish_category = :category");
        $stmtCategoryDishes->execute(['category' => $category]);
    } else {
        $stmtCategoryDishes = $pdo->query("SELECT * FROM tbl_dishes");
    }
    $categoryDishes = $stmtCategoryDishes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
  
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
   
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
   
    <link rel="stylesheet" href="assets/css/jquery.fancybox.min.css">
    
    <link rel="stylesheet" href="css/style3.css">
</head>
<style>
        .dish-info {
            display: none;
        }

        .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
    overflow: auto; /* Enable scroll if modal content exceeds viewport */
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fefefe;
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Adjust as necessary */
    max-width: 600px; /* Limit maximum width */
}


        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

<body>

<?php if($user_role == 'Admin'){
?>

    <section style="background-image: url(assets/images/menu-bg.png);" class="our-menu section bg-light repeat-img" id="menu">
        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec-title text-center mb-5">
                            <p class="sec-sub-title mb-3">our menu</p>
                            <h2 class="h2-title">Sweet & Sugaree Smile, <span>Welcome to Our Menu!</span></h2>
                            <div class="sec-title-shape mb-4">
                                <img src="assets/images/title-shape.svg" alt="">
                                
                            </div>
                            <a href="add_dish.php" class="button">ADD DISH</a>
                        </div>
                    </div>
                </div>
                <div class="menu-tab-wp">
                    <div class="row">
                        <div class="col-lg-12 m-auto">
                            <div class="menu-tab text-center">
                                <ul class="filters">
                                    <div class="filter-active"></div>
                                    <li class="filter" data-filter=".all">
                                        <img src="assets/images/menu-1.png" alt="">
                                        <a href="?category=">All Dishes</a>
                                    </li>
                                    <li class="filter" data-filter=".appetizers">
                                        <img src="assets/images/menu-2.png" alt="">
                                        <a href="?category=Appetizers">Appetizers</a>
                                    </li>
                                    <li class="filter" data-filter=".donuts">
                                        <img src="assets/images/menu-2.png" alt="">
                                        <a href="?category=Donuts">Donuts</a>
                                    </li>
                                    <li class="filter" data-filter=".sandwiches">
                                        <img src="assets/images/menu-3.png" alt="">
                                        <a href="?category=Sandwiches">Sandwiches</a>
                                    </li>
                                    <li class="filter" data-filter=".home">
                                        <img src="assets/images/menu-6.png" alt="">
                                        <a href="index.php">Home</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="menu-list-row">
                    <div class="row g-xxl-5 bydefault_show" id="menu-dish">
                        <?php foreach ($categoryDishes as $dish): ?>
                            <div class="col-lg-4 col-sm-6 dish-box-wp <?php echo strtolower(htmlspecialchars($dish['dish_category'])); ?>" data-cat="<?php echo strtolower(htmlspecialchars($dish['dish_category'])); ?>">
                                <div class="dish-box text-center" onclick="toggleDetails(<?php echo $dish['dish_id']; ?>)">
                                    <div class="dist-img">
                                        <img src="<?php echo htmlspecialchars($dish['dish_img']); ?>" alt="">
                                    </div>
                                    <div class="dish-title">
                                        <h3 class="h3-title"><?php echo htmlspecialchars($dish['dish_name']); ?></h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Structure -->
                            <div id="modal-<?php echo $dish['dish_id']; ?>" class="modal" onclick="closeModalOutside(event, <?php echo $dish['dish_id']; ?>)">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal(<?php echo $dish['dish_id']; ?>)">&times;</span>
                                    
                                        <img src="<?php echo htmlspecialchars($dish['dish_img']); ?>" alt="">
                                    
                                    <div class="dish-title">
                                        <h3 class="h3-title"><?php echo htmlspecialchars($dish['dish_name']); ?></h3>
                                    </div>
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b><?php echo htmlspecialchars($dish['dish_category']); ?></b>
                                        </li>
                                        <li>
                                            <p>Description</p>
                                            <b><?php echo htmlspecialchars($dish['dish_description']); ?></b>
                                        </li>
                                    </ul>
                                    <div><input type="submit" name="edit_dish" class="btn btn-primary btn-lg" value="Edit Dish"></div>
                                    <br>
                                    <div><input type="submit" name="delete_dish" class="btn btn-primary btn-lg" value="Delete Dish"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleDetails(dishId) {
            var modal = document.getElementById('modal-' + dishId);
            modal.style.display = "block";
        }

        function closeModal(dishId) {
            var modal = document.getElementById('modal-' + dishId);
            modal.style.display = "none";
        }

        // Close modal if clicked outside of modal-content
        function closeModalOutside(event, dishId) {
            var modal = document.getElementById('modal-' + dishId);
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php
}else{?>

    <section style="background-image: url(assets/images/menu-bg.png);" class="our-menu section bg-light repeat-img" id="menu">
        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec-title text-center mb-5">
                            <p class="sec-sub-title mb-3">our menu</p>
                            <h2 class="h2-title">Sweet & Sugaree Smile, <span>Welcome to Our Menu!</span></h2>
                            <div class="sec-title-shape mb-4">
                                <img src="assets/images/title-shape.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="menu-tab-wp">
                    <div class="row">
                        <div class="col-lg-12 m-auto">
                            <div class="menu-tab text-center">
                                <ul class="filters">
                                    <div class="filter-active"></div>
                                    <li class="filter" data-filter=".all">
                                        <img src="assets/images/menu-1.png" alt="">
                                        <a href="?category=">All Dishes</a>
                                    </li>
                                    <li class="filter" data-filter=".appetizers">
                                        <img src="assets/images/menu-2.png" alt="">
                                        <a href="?category=Appetizers">Appetizers</a>
                                    </li>
                                    <li class="filter" data-filter=".donuts">
                                        <img src="assets/images/menu-2.png" alt="">
                                        <a href="?category=Donuts">Donuts</a>
                                    </li>
                                    <li class="filter" data-filter=".sandwiches">
                                        <img src="assets/images/menu-3.png" alt="">
                                        <a href="?category=Sandwiches">Sandwiches</a>
                                    </li>
                                    <li class="filter" data-filter=".home">
                                        <img src="assets/images/menu-6.png" alt="">
                                        <a href="index.php">Home</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="menu-list-row">
                    <div class="row g-xxl-5 bydefault_show" id="menu-dish">
                        <?php foreach ($categoryDishes as $dish): ?>
                            <div class="col-lg-4 col-sm-6 dish-box-wp <?php echo strtolower(htmlspecialchars($dish['dish_category'])); ?>" data-cat="<?php echo strtolower(htmlspecialchars($dish['dish_category'])); ?>">
                                <div class="dish-box text-center" onclick="toggleDetails(<?php echo $dish['dish_id']; ?>)">
                                    <div class="dist-img">
                                        <img src="<?php echo htmlspecialchars($dish['dish_img']); ?>" alt="">
                                    </div>
                                    <div class="dish-title">
                                        <h3 class="h3-title"><?php echo htmlspecialchars($dish['dish_name']); ?></h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Structure -->
                            <div id="modal-<?php echo $dish['dish_id']; ?>" class="modal" onclick="closeModalOutside(event, <?php echo $dish['dish_id']; ?>)">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal(<?php echo $dish['dish_id']; ?>)">&times;</span>
                                    
                                        <img src="<?php echo htmlspecialchars($dish['dish_img']); ?>" alt="">
                                    
                                    <div class="dish-title">
                                        <h3 class="h3-title"><?php echo htmlspecialchars($dish['dish_name']); ?></h3>
                                    </div>
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b><?php echo htmlspecialchars($dish['dish_category']); ?></b>
                                        </li>
                                        <li>
                                            <p>Description</p>
                                            <b><?php echo htmlspecialchars($dish['dish_description']); ?></b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleDetails(dishId) {
            var modal = document.getElementById('modal-' + dishId);
            modal.style.display = "block";
        }

        function closeModal(dishId) {
            var modal = document.getElementById('modal-' + dishId);
            modal.style.display = "none";
        }

        // Close modal if clicked outside of modal-content
        function closeModalOutside(event, dishId) {
            var modal = document.getElementById('modal-' + dishId);
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php 
} 
?>
</body>
</html>