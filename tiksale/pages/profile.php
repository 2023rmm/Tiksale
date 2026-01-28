<?php
/**
 * MY PROFILE - Update User Details & Profile Picture
 */
require_once __DIR__ . '/../includes/session_init.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

// Fetch user
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $country   = trim($_POST['country'] ?? '');

    $profile_image = $user['profile_image'];

    // Image upload
    if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === 0) {
        $upload_dir = '../uploads/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $path)) {
                $profile_image = 'http://localhost/tiksale/uploads/profiles/' . $filename;
            }
        }
    }

    $update = $db->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, phone = ?, country = ?, profile_image = ?
        WHERE user_id = ?
    ");

    if ($update->execute([$full_name, $email, $phone, $country, $profile_image, $user_id])) {
        $success_message = 'Profile updated successfully!';
        $_SESSION['full_name'] = $full_name;

        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error_message = 'Failed to update profile.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tiksale</title>

    <!-- SAME CSS ORDER AS DASHBOARD / MY SHOP -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .profile-container {
            max-width: 900px;
            margin: auto;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            margin: auto;
            position: relative;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #6366f1;
        }
        .upload-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 42px;
            height: 42px;
            background: #6366f1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid #fff;
        }
        .upload-overlay i { color: #fff; }
        input[type=file] { display: none; }

        .profile-form {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }
        .btn-submit {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .alert {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #dcfce7;
            color: #065f46;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body class="dashboard-body">
<div class="dashboard-container">

    <?php include 'layout/header.php'; ?>
    <?php include 'layout/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <p>Update your personal details</p>
        </header>

        <div class="profile-container">

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="profile-header">
                <div class="profile-avatar">
                    <img id="avatarPreview"
                         src="<?php echo $user['profile_image'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=6366f1&color=fff'; ?>">
                    <label for="profileImageInput" class="upload-overlay">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
            </div>

            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <input type="file" id="profileImageInput" name="profile_image" accept="image/*" onchange="previewImage(this)">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required value="<?php echo htmlspecialchars($user['full_name']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <div class="form-group full">
                        <label>Country</label>
                        <select name="country">
                            <option value="">Select Country</option>
                            <?php
                            $countries = ['Kenya','USA','UK','Canada','Australia'];
                            foreach ($countries as $c) {
                                $sel = $user['country'] === $c ? 'selected' : '';
                                echo "<option $sel>$c</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button class="btn-submit"><i class="fas fa-save"></i> Save Changes</button>
            </form>

        </div>
    </main>
</div>


<script>
function previewImage(input) {
    if (input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>





